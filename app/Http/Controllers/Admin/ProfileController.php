<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// Company users edit their own company profile
class ProfileController extends Controller
{
    public function edit()
    {
        abort_unless(auth()->user()->isCompany(), 403);
        return view('admin.profile', ['company' => auth()->user()->company]);
    }

    public function update(Request $request)
    {
        abort_unless($request->user()->isCompany(), 403);
        $company = $request->user()->company;

        $data = $request->validate([
            'name' => 'required|string|max:150',
            'website' => 'nullable|url|max:200',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
        ]);
        if ($request->hasFile('logo')) {
            if ($company->logo) Storage::disk('public')->delete($company->logo);
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }
        $company->update($data);
        return back()->with('success', 'Company profile updated.');
    }
}
