<?php
namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;

class MyApplicationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $base = Application::forUser($user);

        $applications = (clone $base)->with('job.company', 'job.location')
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()->paginate(10)->withQueryString();

        $counts = (clone $base)->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('my-applications', compact('applications', 'counts'));
    }

    public function cancel(Request $request, Application $application)
    {
        $user = $request->user();
        abort_unless($application->user_id === $user->id || $application->email === $user->email, 403);
        abort_unless($application->canCancel(), 422, 'This application can no longer be cancelled.');

        $application->update(['status' => 'cancelled']);
        return back()->with('success', 'Application cancelled.');
    }
}
