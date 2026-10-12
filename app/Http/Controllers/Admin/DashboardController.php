<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Application, Category, Company, JobListing, Location};

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isCompany()) {
            $cid = $user->company_id;
            $apps = Application::whereHas('job', fn ($q) => $q->where('company_id', $cid));
            return view('admin.dashboard', [
                'stats' => [
                    'My Jobs' => JobListing::where('company_id', $cid)->count(),
                    'Active Jobs' => JobListing::where('company_id', $cid)->where('is_active', true)->count(),
                    'Applications' => (clone $apps)->count(),
                    'New (Reached)' => (clone $apps)->where('status', 'pending')->count(),
                    'Shortlisted' => (clone $apps)->where('status', 'shortlisted')->count(),
                ],
                'latest' => (clone $apps)->with('job')->latest()->take(8)->get(),
            ]);
        }

        return view('admin.dashboard', [
            'stats' => [
                'Jobs' => JobListing::count(),
                'Active Jobs' => JobListing::active()->count(),
                'Companies' => Company::count(),
                'Pending Companies' => Company::where('is_approved', false)->count(),
                'Categories' => Category::count(),
                'Locations' => Location::count(),
                'Applications' => Application::count(),
            ],
            'latest' => Application::with('job')->latest()->take(8)->get(),
        ]);
    }
}
