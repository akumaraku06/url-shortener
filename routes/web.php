<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ShortUrlController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/login');

// Publicly resolvable short url redirect (no auth required).
Route::get('/s/{code}', [ShortUrlController::class, 'redirectToOriginal'])->name('short-urls.redirect');

// --- Guest only ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');

    Route::get('/invitations/{token}', [InvitationController::class, 'showAccept'])->name('invitations.accept');
    Route::post('/invitations/{token}', [InvitationController::class, 'accept'])->name('invitations.accept.store');
});

// --- Authenticated ---
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // SuperAdmin only: manage companies (creates the company + its first Admin).
    Route::middleware('role:superadmin')->group(function () {
        Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
        Route::get('/companies/create', [CompanyController::class, 'create'])->name('companies.create');
        Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
    });

    // SuperAdmin and Admin can send invitations.
    // SuperAdmin can invite an Admin in a new company.
    // An Admin can invite another Admin or Member in their own company.
    Route::middleware('role:superadmin,admin')->group(function () {
        Route::get('/invitations/create', [InvitationController::class, 'create'])->name('invitations.create');
        Route::post('/invitations', [InvitationController::class, 'store'])->name('invitations.store');
    });

    // SuperAdmin, Admin and Member can all view short url lists
    // (each scoped differently inside the controller).
    Route::middleware('role:superadmin,admin,member')->group(function () {
        Route::get('/short-urls', [ShortUrlController::class, 'index'])->name('short-urls.index');
    });

    // Admin and Member can create short urls. SuperAdmin cannot.
    Route::middleware('role:admin,member')->group(function () {
        Route::get('/short-urls/create', [ShortUrlController::class, 'create'])->name('short-urls.create');
        Route::post('/short-urls', [ShortUrlController::class, 'store'])->name('short-urls.store');
    });
});
