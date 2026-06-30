@extends('layouts.app')

@section('title', 'Short URLs')

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Generated Short URLs</h2>
            @if(auth()->user()->canCreateShortUrls())
                <a href="{{ route('short-urls.create') }}" class="btn btn-primary">+ Generate</a>
            @endif
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
                <tr><th>Short URL</th><th>Long URL</th><th>Created By</th><th>Company</th><th>Created At</th></tr>
            </thead>
            <tbody>
            @forelse($shortUrls as $shortUrl)
                <tr>
                    <td><span class="badge">{{ $shortUrl->code }}</span></td>
                    <td>{{ \Illuminate\Support\Str::limit($shortUrl->original_url, 45) }}</td>
                    <td>{{ $shortUrl->user->name }}</td>
                    <td>{{ $shortUrl->company->name }}</td>
                    <td>{{ $shortUrl->created_at->format('d M Y') }}</td>
                </tr>
            @empty
                <tr class="empty-row"><td colspan="5">No short urls to display.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
