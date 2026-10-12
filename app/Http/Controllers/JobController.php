<?php
namespace App\Http\Controllers;

use App\Mail\{ApplicationReceived, NewApplication};
use App\Models\{Application, Category, Company, JobListing, Location, Skill, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Log, Mail};

class JobController extends Controller
{
    public function index(Request $request)
    {
        $jobs = JobListing::with(['company', 'category', 'location', 'skills'])
            ->active()->filter($request->only(['q','category','location','company','type','skill']))
            ->latest()->paginate(10)->withQueryString();

        return view('jobs.index', [
            'jobs' => $jobs,
            'categories' => Category::orderBy('name')->get(),
            'locations'  => Location::orderBy('name')->get(),
            'companies'  => Company::orderBy('name')->get(),
            'skills'     => Skill::orderBy('name')->get(),
            'types'      => JobListing::TYPES,
        ]);
    }

    public function show(Request $request, JobListing $job)
    {
        abort_unless($job->is_active, 404);
        $job->load(['company', 'category', 'location', 'skills']);
        abort_unless($job->company->is_approved, 404);

        // Has the logged-in user already applied?
        $applied = null;
        if ($user = $request->user()) {
            $applied = Application::forUser($user)->where('job_listing_id', $job->id)
                ->where('status', '!=', 'cancelled')->latest()->first();
        }

        return view('jobs.show', ['job' => $job, 'applied' => $applied, 'related' => $this->relatedJobs($job)]);
    }

    /** 2-4 suggested jobs: same category / shared skills / same location */
    private function relatedJobs(JobListing $job)
    {
        $skillIds = $job->skills->pluck('id');

        $related = JobListing::with(['company', 'location', 'skills'])->active()
            ->where('id', '!=', $job->id)
            ->where(fn ($q) => $q->where('category_id', $job->category_id)
                ->orWhere('location_id', $job->location_id)
                ->orWhereHas('skills', fn ($s) => $s->whereIn('skills.id', $skillIds)))
            ->get()
            ->sortByDesc(fn ($j) => ($j->category_id == $job->category_id ? 3 : 0)
                + $j->skills->pluck('id')->intersect($skillIds)->count() * 2
                + ($j->location_id == $job->location_id ? 1 : 0))
            ->take(4)->values();

        // Not enough matches? Fill up with the latest jobs
        if ($related->count() < 2) {
            $more = JobListing::with(['company', 'location', 'skills'])->active()
                ->whereNotIn('id', $related->pluck('id')->push($job->id))
                ->latest()->take(4 - $related->count())->get();
            $related = $related->concat($more);
        }
        return $related;
    }

    public function apply(Request $request, JobListing $job)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:30',
            'cover_letter' => 'nullable|string|max:5000',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:4096',
        ]);

        // Cancelled applications don't block re-applying
        if ($job->applications()->where('email', $data['email'])->where('status', '!=', 'cancelled')->exists()) {
            return back()->withInput()->with('error', 'You have already applied for this job with this email.');
        }

        $application = $job->applications()->create([
            'user_id' => $request->user()?->id,
            'name' => $data['name'], 'email' => $data['email'],
            'phone' => $data['phone'] ?? null, 'cover_letter' => $data['cover_letter'] ?? null,
            'resume_path' => $request->file('resume')->store('resumes', 'public'),
        ]);
        $application->load('job.company');

        try {
            Mail::to($application->email)->send(new ApplicationReceived($application));
            // admins + the company that owns the job
            $admins = User::where('role', 'admin')
                ->orWhere(fn ($q) => $q->where('role', 'company')->where('company_id', $application->job->company_id))
                ->pluck('email')->all();
            if ($admins) Mail::to($admins)->send(new NewApplication($application));
        } catch (\Throwable $e) {
            Log::error('Application mail failed: ' . $e->getMessage());
        }

        return redirect()->route('jobs.show', $job)->with('success', 'Application submitted! A confirmation email has been sent.');
    }
}
