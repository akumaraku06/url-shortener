<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ShortUrl;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $shortUrls = collect();
        $companies = collect();
        $teamMembers = collect();

        if ($user->isSuperAdmin()) {
            $companies = Company::withCount('users')->latest()->take(5)->get();
            $shortUrls = ShortUrl::with(['user', 'company'])->latest()->take(5)->get();
        } elseif ($user->isAdmin()) {
            $shortUrls = ShortUrl::visibleToAdmin($user)->with(['user', 'company'])->latest()->take(5)->get();
            $teamMembers = $user->company->users()->where('id', '!=', $user->id)->latest()->take(5)->get();
        } else {
            // member
            $shortUrls = ShortUrl::visibleToMember($user)->with(['user', 'company'])->latest()->take(5)->get();
        }

        return view('dashboard', compact('shortUrls', 'companies', 'teamMembers'));
    }
}
