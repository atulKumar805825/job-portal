<?php
namespace App\Http\Controllers;

use App\Models\{Company, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB};

class AuthController extends Controller
{
    public function showLogin() { return view('auth.login'); }
    public function showRegister() { return view('auth.register'); }
    public function showCompanyRegister() { return view('auth.register-company'); }

    public function login(Request $request)
    {
        $cred = $request->validate(['email' => 'required|email', 'password' => 'required']);
        if (!Auth::attempt($cred, $request->boolean('remember'))) {
            return back()->withInput($request->only('email'))->with('error', 'Invalid email or password.');
        }
        $request->session()->regenerate();
        $u = Auth::user();
        return ($u->isAdmin() || $u->isCompany()) ? redirect()->route('admin.dashboard') : redirect()->intended('/');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
        Auth::login(User::create($data + ['role' => 'user']));
        return redirect('/');
    }

    // Company self-registration (needs admin approval before jobs go public)
    public function registerCompany(Request $request)
    {
        $data = $request->validate([
            'company_name' => 'required|string|max:150',
            'website' => 'nullable|url|max:200',
            'description' => 'nullable|string|max:3000',
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = DB::transaction(function () use ($data) {
            $company = Company::create([
                'name' => $data['company_name'], 'website' => $data['website'] ?? null,
                'description' => $data['description'] ?? null, 'is_approved' => false,
            ]);
            return User::create([
                'name' => $data['name'], 'email' => $data['email'], 'password' => $data['password'],
                'role' => 'company', 'company_id' => $company->id,
            ]);
        });

        Auth::login($user);
        return redirect()->route('admin.dashboard')->with('success', 'Company registered! You can post jobs once an admin approves your company.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
