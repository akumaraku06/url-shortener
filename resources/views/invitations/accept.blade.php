@extends('layouts.app')

@section('title', 'Accept Invitation')

@section('content')
    <div class="login-wrap">
        <div class="card login-card">
            <h1>Accept Invitation</h1>
            <p class="card-subtitle" style="text-align:center;">You've been invited to join as <strong>{{ ucfirst($invitation->role) }}</strong> ({{ $invitation->email }}).</p>

            <form method="POST" action="{{ route('invitations.accept.store', $invitation->token) }}" class="form-card">
                @csrf
                <label for="name"> Name</label>
                <input type="text" id="name" name="name" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="8">

                <label for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8">

                <button type="submit" class="btn btn-primary" style="width:100%;margin-top:18px;">Create Account</button>
            </form>
        </div>
    </div>
@endsection
