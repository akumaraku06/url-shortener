@extends('layouts.app')

@section('title', 'Clients')

@section('content')
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
                    <td>
                        @forelse($company->users as $u)
                            {{ $u->name }} ({{ $u->role }})@if(!$loop->last), @endif
                        @empty
                            —
                        @endforelse
                    </td>
                </tr>
            @empty
                <tr class="empty-row"><td colspan="2">No clients yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
