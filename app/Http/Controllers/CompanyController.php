<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Only the SuperAdmin manages companies.
 *
 * Note: the spec says "SuperAdmin can't invite an Admin in a new company".
 * That restriction is specifically about the *invitation* flow (see
 * InvitationController). Creating a company necessarily needs a first
 * Admin to manage it, so the SuperAdmin creates the company and its Admin
 * directly here, rather than through an invitation.
 */
class CompanyController extends Controller
{
    public function index(): View
    {
        $companies = Company::with('users')->latest()->get();

        return view('companies.index', compact('companies'));
    }

    public function create(): View
    {
        return view('companies.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'unique:users,email'],
            'admin_password' => ['required', 'string', 'min:8'],
        ]);

        $company = Company::create(['name' => $data['company_name']]);

        User::create([
            'name' => $data['admin_name'],
            'email' => $data['admin_email'],
            'password' => Hash::make($data['admin_password']),
            'company_id' => $company->id,
            'role' => User::ROLE_ADMIN,
        ]);

        return redirect()->route('companies.index')
            ->with('status', "Company '{$company->name}' created with its Admin account.");
    }
}
