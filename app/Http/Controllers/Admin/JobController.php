<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Category, Company, JobListing, Location, Skill};
use Illuminate\Http\Request;
use Illuminate\Support\Str;

// Used by BOTH admin (all jobs) and company users (only their own jobs)
class JobController extends Controller
{
    private function isCompany(): bool { return auth()->user()->isCompany(); }

    private function authorizeJob(JobListing $job): void
    {
        if ($this->isCompany()) abort_unless($job->company_id === auth()->user()->company_id, 403);
    }

    private function ensureApproved()
    {
        if ($this->isCompany() && !auth()->user()->company->is_approved) {
            return redirect()->route('admin.jobs.index')->with('error', 'Your company is waiting for admin approval. You can post jobs after approval.');
        }
        return null;
    }

    public function index(Request $request)
    {
        $jobs = JobListing::with(['company', 'category', 'location'])->withCount('applications')
            ->when($this->isCompany(), fn ($q) => $q->where('company_id', auth()->user()->company_id))
            ->when($request->q, fn ($q, $s) => $q->where('title', 'like', "%$s%"))
            ->latest()->paginate(15)->withQueryString();
        return view('admin.jobs.index', compact('jobs'));
    }

    public function create()
    {
        if ($r = $this->ensureApproved()) return $r;
        return view('admin.jobs.form', $this->formData(new JobListing(['is_active' => true])));
    }

    public function store(Request $request)
    {
        if ($r = $this->ensureApproved()) return $r;
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['title']) . '-' . Str::lower(Str::random(5));
        $job = JobListing::create($data);
        $job->skills()->sync($request->input('skills', []));
        return redirect()->route('admin.jobs.index')->with('success', 'Job created.');
    }

    public function edit(JobListing $job)
    {
        $this->authorizeJob($job);
        return view('admin.jobs.form', $this->formData($job));
    }

    public function update(Request $request, JobListing $job)
    {
        $this->authorizeJob($job);
        $job->update($this->validated($request));
        $job->skills()->sync($request->input('skills', []));
        return redirect()->route('admin.jobs.index')->with('success', 'Job updated.');
    }

    public function destroy(JobListing $job)
    {
        $this->authorizeJob($job);
        $job->delete();
        return back()->with('success', 'Job deleted.');
    }

    public function toggle(JobListing $job)
    {
        $this->authorizeJob($job);
        $job->update(['is_active' => !$job->is_active]);
        return back()->with('success', 'Job status changed.');
    }

    private function validated(Request $r): array
    {
        $rules = [
            'title' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'job_type' => 'required|in:' . implode(',', JobListing::TYPES),
            'salary_min' => 'nullable|integer|min:0',
            'salary_max' => 'nullable|integer|gte:salary_min',
            'experience' => 'nullable|string|max:100',
            'description' => 'required|string',
            'deadline' => 'nullable|date',
            'skills' => 'nullable|array', 'skills.*' => 'exists:skills,id',
        ];
        if (!$this->isCompany()) $rules['company_id'] = 'required|exists:companies,id';

        $data = $r->validate($rules);
        unset($data['skills']);
        // company users always post under their own company
        if ($this->isCompany()) $data['company_id'] = auth()->user()->company_id;
        $data['is_active'] = $r->boolean('is_active');
        return $data;
    }

    private function formData(JobListing $job): array
    {
        return [
            'job' => $job,
            'companies' => $this->isCompany() ? collect() : Company::where('is_approved', true)->orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'locations' => Location::orderBy('name')->get(),
            'skills' => Skill::orderBy('name')->get(),
            'selected' => $job->exists ? $job->skills->pluck('id')->all() : [],
            'types' => JobListing::TYPES,
        ];
    }
}
