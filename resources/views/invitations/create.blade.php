@extends('layouts.app')

@section('title', 'Invite New Team Member')

@section('content')
    <div class="card" style="max-width:480px;">
        <div class="card-header">
            <h2>Invite New Team Member</h2>
        </div>

        <form method="POST" action="{{ route('invitations.store') }}" class="form-card">
            @csrf

            @if(auth()->user()->isSuperAdmin())
                <label for="company_id">Client</label>
                <select id="company_id" name="company_id" required>
                    <option value="">-- Select client --</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" @selected(old('company_id') == $company->id)>{{ $company->name }}</option>
                    @endforeach
                </select>
            @else
                <p class="card-subtitle">Inviting into: <strong>{{ auth()->user()->company->name }}</strong></p>
            @endif

            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="ex. name@example.com" value="{{ old('email') }}" required>

            <label for="role">Role</label>
            <select id="role" name="role" required>
                <option value="">-- Select role --</option>
                @foreach($invitableRoles as $role)
                    <option value="{{ $role }}" @selected(old('role') == $role)>{{ ucfirst($role) }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-primary" style="width:100%;margin-top:18px;">Send Invitation</button>
        </form>
    </div>
@endsection
