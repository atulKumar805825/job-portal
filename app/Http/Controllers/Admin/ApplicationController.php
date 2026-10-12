<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// Admin sees all applications; company users only see applications for their own jobs
class ApplicationController extends Controller
{
    private function authorizeApp(Application $application): void
    {
        $u = auth()->user();
        if ($u->isCompany()) abort_unless($application->job->company_id === $u->company_id, 403);
    }

    public function index(Request $request)
    {
        $u = $request->user();
        $applications = Application::with('job.company')
            ->when($u->isCompany(), fn ($q) => $q->whereHas('job', fn ($j) => $j->where('company_id', $u->company_id)))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->q, fn ($q, $s) => $q->where(fn ($x) => $x->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%")))
            ->latest()->paginate(15)->withQueryString();
        return view('admin.applications.index', compact('applications'));
    }

    public function update(Request $request, Application $application)
    {
        $this->authorizeApp($application);
        $request->validate(['status' => 'required|in:' . implode(',', Application::STATUSES)]);
        $application->update(['status' => $request->status]);
        return back()->with('success', 'Status updated.');
    }

    public function resume(Application $application)
    {
        $this->authorizeApp($application);
        abort_unless($application->resume_path && Storage::disk('public')->exists($application->resume_path), 404);
        if ($application->status === 'pending') $application->update(['status' => 'viewed']); // candidate sees "Application Viewed"
        return Storage::disk('public')->download($application->resume_path);
    }

    public function destroy(Application $application)
    {
        $this->authorizeApp($application);
        if ($application->resume_path) Storage::disk('public')->delete($application->resume_path);
        $application->delete();
        return back()->with('success', 'Application deleted.');
    }
}
