<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InvitationController extends Controller
{
    /**
     * Roles a SuperAdmin is allowed to invite into a (new or existing) company.
     * SuperAdmin can invite an Admin in a new company.
     */
    private const SUPERADMIN_INVITABLE_ROLES = [User::ROLE_ADMIN, User::ROLE_MEMBER];

    /**
     * Roles an Admin is allowed to invite within their own company.
     * An Admin can invite another Admin or Member in their own company.
     */
    private const ADMIN_INVITABLE_ROLES = [User::ROLE_ADMIN, User::ROLE_MEMBER];

    public function create(Request $request): View
    {
        $user = $request->user();

        if ($user->isSuperAdmin()) {
            $companies = Company::orderBy('name')->get();

            return view('invitations.create', [
                'companies' => $companies,
                'invitableRoles' => self::SUPERADMIN_INVITABLE_ROLES,
            ]);
        }

        if ($user->isAdmin()) {
            return view('invitations.create', [
                'companies' => null,
                'invitableRoles' => self::ADMIN_INVITABLE_ROLES,
            ]);
        }

        abort(403, 'You are not authorized to send invitations.');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->isSuperAdmin()) {
            $data = $request->validate([
                'company_id' => ['required', 'exists:companies,id'],
                'email' => ['required', 'email'],
                'role' => ['required', 'in:'.implode(',', self::SUPERADMIN_INVITABLE_ROLES)],
            ]);
            $companyId = $data['company_id'];
        } elseif ($user->isAdmin()) {
            $data = $request->validate([
                'email' => ['required', 'email'],
                'role' => ['required', 'in:'.implode(',', self::ADMIN_INVITABLE_ROLES)],
            ]);
            $companyId = $user->company_id;
        } else {
            abort(403, 'You are not authorized to send invitations.');
        }

        $invitation = Invitation::create([
            'company_id' => $companyId,
            'email' => $data['email'],
            'role' => $data['role'],
            'invited_by' => $user->id,
            'token' => Str::random(48),
        ]);

        $acceptUrl = route('invitations.accept', $invitation->token);

        // No mail server is required for the assignment: the invite link is
        // surfaced directly in the UI / flashed session so it can be tested manually.
        return redirect()->back()->with('status', "Invitation sent. Share this accept link: {$acceptUrl}");
    }

    public function showAccept(string $token): View
    {
        $invitation = Invitation::where('token', $token)->whereNull('accepted_at')->firstOrFail();

        return view('invitations.accept', compact('invitation'));
    }

    public function accept(Request $request, string $token): RedirectResponse
    {
        $invitation = Invitation::where('token', $token)->whereNull('accepted_at')->firstOrFail();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (User::where('email', $invitation->email)->exists()) {
            throw new HttpException(409, 'An account with this email already exists.');
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $invitation->email,
            'password' => Hash::make($data['password']),
            'company_id' => $invitation->company_id,
            'role' => $invitation->role,
        ]);

        $invitation->update(['accepted_at' => now()]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('status', 'Welcome!  account is ready.');
    }
}
