<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShortUrlController extends Controller
{
    /**
     * GET /short-urls
     *
     *  - SuperAdmin -> every short url, across every company.
     *  - Admin      -> short urls created within the Admin's own company.
     *  - Member     -> short urls created by the Member themselves.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->isSuperAdmin()) {
            $shortUrls = ShortUrl::visibleToSuperAdmin()->with(['user', 'company'])->latest()->get();
        } elseif ($user->isAdmin()) {
            $shortUrls = ShortUrl::visibleToAdmin($user)->with(['user', 'company'])->latest()->get();
        } else {
            // member
            $shortUrls = ShortUrl::visibleToMember($user)->with(['user', 'company'])->latest()->get();
        }

        return view('short-urls.index', compact('shortUrls'));
    }

    public function create(): View
    {
        return view('short-urls.create');
    }

    /**
     * POST /short-urls
     * Restricted via route middleware to role:admin,member.
     * (SuperAdmin is explicitly forbidden from creating.)
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'original_url' => ['required', 'url', 'max:2048'],
        ]);

        $user = $request->user();

        $shortUrl = ShortUrl::create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'original_url' => $data['original_url'],
            'code' => ShortUrl::generateUniqueCode(),
        ]);

        return redirect()->route('short-urls.create')
            ->with('status', "Short url created: ".route('short-urls.redirect', $shortUrl->code));
    }

    /**
     * GET /s/{code}
     *
     * Publicly resolvable: no authentication required. Anyone hitting this
     * url is redirected straight to the original url.
     */
    public function redirectToOriginal(string $code): RedirectResponse
    {
        $shortUrl = ShortUrl::where('code', $code)->firstOrFail();

        return redirect()->away($shortUrl->original_url);
    }
}
