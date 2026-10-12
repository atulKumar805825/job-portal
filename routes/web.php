<?php
use App\Http\Controllers\{AuthController, JobController, MyApplicationController};
use App\Http\Controllers\Admin\{ApplicationController, CategoryController, CompanyController, DashboardController, LocationController, ProfileController, SkillController};
use App\Http\Controllers\Admin\JobController as AdminJobController;
use App\Http\Middleware\{AdminMiddleware, PanelMiddleware};
use Illuminate\Support\Facades\Route;

// ---------- Public ----------
Route::get('/', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{job}', [JobController::class, 'show'])->name('jobs.show');
Route::post('/jobs/{job}/apply', [JobController::class, 'apply'])->name('jobs.apply');

// ---------- Auth ----------
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/register/company', [AuthController::class, 'showCompanyRegister'])->name('register.company');
    Route::post('/register/company', [AuthController::class, 'registerCompany']);
});
Route::middleware('auth')->group(function () {
    Route::get('/my-applications', [MyApplicationController::class, 'index'])->name('my.applications');
    Route::patch('/my-applications/{application}/cancel', [MyApplicationController::class, 'cancel'])->name('my.applications.cancel');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ---------- Panel: Admin + Company users ----------
Route::prefix('admin')->name('admin.')->middleware(['auth', PanelMiddleware::class])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // jobs + applications (company users are limited to their own data inside the controllers)
    Route::patch('jobs/{job}/toggle', [AdminJobController::class, 'toggle'])->name('jobs.toggle');
    Route::resource('jobs', AdminJobController::class)->except('show');

    Route::get('applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::patch('applications/{application}', [ApplicationController::class, 'update'])->name('applications.update');
    Route::get('applications/{application}/resume', [ApplicationController::class, 'resume'])->name('applications.resume');
    Route::delete('applications/{application}', [ApplicationController::class, 'destroy'])->name('applications.destroy');

    // company profile (company users)
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

    // ---------- Admin only ----------
    Route::middleware(AdminMiddleware::class)->group(function () {
        Route::patch('companies/{company}/approve', [CompanyController::class, 'approve'])->name('companies.approve');
        Route::resource('companies', CompanyController::class)->except('show');
        Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('locations',  LocationController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('skills',     SkillController::class)->only(['index', 'store', 'update', 'destroy']);
    });
});
