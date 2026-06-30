@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    @if(auth()->user()->canCreateShortUrls())
        <div class="card">
            <div class="card-header">
                <h2>Generate Short URL</h2>
            </div>
            <form method="POST" action="{{ route('short-urls.store') }}" class="form-card">
                @csrf
                <label for="original_url">Long URL</label>
                <div style="display:flex;gap:10px;align-items:flex-end;">
                    <div style="flex:1;">
                        <input type="url" id="original_url" name="original_url" placeholder="e.g. https://.com/travel-software/features/best-itinerary-builder" value="{{ old('original_url') }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Generate</button>
                </div>
            </form>
        </div>
    @endif

    @if(auth()->user()->isSuperAdmin())
        <div class="card">
            <div class="card-header">
                <h2>Clients</h2>
                <a href="{{ route('companies.create') }}" class="btn btn-primary">+ Invite</a>
            </div>
            <table>
                <thead>
                    <tr><th>Client Name</th><th>Users</th></tr>
                </thead>
                <tbody>
                    @forelse($companies as $company)
                        <tr>
                            <td>{{ $company->name }}</td>
                            <td>{{ $company->users_count }}</td>
                        </tr>
                    @empty
                        <tr class="empty-row"><td colspan="2">No clients yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <p style="margin-top:10px;"><a href="{{ route('companies.index') }}" style="font-size:13px;color:var(--orange);text-decoration:none;">View all clients →</a></p>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h2>Generated Short URLs</h2>
        </div>
        @if(auth()->user()->isAdmin())
            <p class="card-subtitle">Showing short urls created in  own company.</p>
        @elseif(auth()->user()->isMember())
            <p class="card-subtitle">Showing short urls created by you.</p>
        @elseif(auth()->user()->isSuperAdmin())
            <p class="card-subtitle">Showing short urls across every company.</p>
        @endif

        <table>
            <thead>
                <tr><th>Short URL</th><th>Long URL</th><th>Company</th><th>Created At</th></tr>
            </thead>
            <tbody>
                @forelse($shortUrls as $shortUrl)
                    <tr>
                        <td><span class="badge">{{ $shortUrl->code }}</span></td>
                        <td>{{ \Illuminate\Support\Str::limit($shortUrl->original_url, 40) }}</td>
                        <td>{{ $shortUrl->company->name }}</td>
                        <td>{{ $shortUrl->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="4">No short urls to display.</td></tr>
                @endforelse
            </tbody>
        </table>
        <p style="margin-top:10px;"><a href="{{ route('short-urls.index') }}" style="font-size:13px;color:var(--orange);text-decoration:none;">View all short urls →</a></p>
    </div>

    @if(auth()->user()->isAdmin())
        <div class="card">
            <div class="card-header">
                <h2>Team Members</h2>
                <a href="{{ route('invitations.create') }}" class="btn btn-primary">+ Invite</a>
            </div>
            <table>
                <thead>
                    <tr><th>Name</th><th>Email</th><th>Role</th></tr>
                </thead>
                <tbody>
                    @forelse($teamMembers as $member)
                        <tr>
                            <td>{{ $member->name }}</td>
                            <td>{{ $member->email }}</td>
                            <td><span class="badge">{{ ucfirst($member->role) }}</span></td>
                        </tr>
                    @empty
                        <tr class="empty-row"><td colspan="3">No team members yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

@endsection
