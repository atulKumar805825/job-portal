<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Company, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Storage};

// Admin only: manage companies and their login accounts
class CompanyController extends Controller
{
    public function index()
    {
        return view('admin.companies.index', [
            'companies' => Company::with('users')->withCount('jobs')->orderBy('is_approved')->orderBy('name')->paginate(15),
        ]);
    }

    public function create() { return view('admin.companies.form', ['company' => new Company]); }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $login = $request->validate([
            'login_email' => 'nullable|email|unique:users,email|required_with:login_password',
            'login_password' => 'nullable|min:6|required_with:login_email',
        ]);
        if ($request->hasFile('logo')) $data['logo'] = $request->file('logo')->store('logos', 'public');
        $data['is_approved'] = true; // admin-created companies are approved

        DB::transaction(function () use ($data, $login) {
            $company = Company::create($data);
            if (!empty($login['login_email'])) {
                User::create(['name' => $company->name, 'email' => $login['login_email'],
                    'password' => $login['login_password'], 'role' => 'company', 'company_id' => $company->id]);
            }
        });
        return redirect()->route('admin.companies.index')->with('success', 'Company added.');
    }

    public function edit(Company $company) { return view('admin.companies.form', compact('company')); }

    public function update(Request $request, Company $company)
    {
        $data = $this->validated($request);
        if ($request->hasFile('logo')) {
            if ($company->logo) Storage::disk('public')->delete($company->logo);
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }
        $company->update($data);
        return redirect()->route('admin.companies.index')->with('success', 'Company updated.');
    }

    public function approve(Company $company)
    {
        $company->update(['is_approved' => !$company->is_approved]);
        return back()->with('success', $company->is_approved ? 'Company approved.' : 'Company set to pending (its jobs are hidden).');
    }

    public function destroy(Company $company)
    {
        $company->users()->delete();
        $company->delete(); // its jobs are deleted too (cascade)
        return back()->with('success', 'Company deleted.');
    }

    private function validated(Request $r): array
    {
        return $r->validate([
            'name' => 'required|string|max:150',
            'website' => 'nullable|url|max:200',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
        ]);
    }
}
