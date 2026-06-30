@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="login-wrap">
        <div class="card login-card">
            <h1>🔗 URL Shortener</h1>
            <h1>🔗 URL Shortener</h1>

            <form method="POST" action="{{ route('login.attempt') }}" class="form-card">
                @csrf
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="e.g. sample@example.com" required autofocus>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>

                <button type="submit" class="btn btn-primary" style="width:100%;margin-top:18px;">Login</button>
            </form>

            <p class="hint">
                Seeded accounts (password for all: <code>password</code>):<br>
                superadmin@example.com (SuperAdmin)<br>
                admin@acme.test (Admin, Acme Inc)<br>
                member@acme.test (Member, Acme Inc)<br>
                admin@globex.test (Admin, Globex Corp)<br>
                member@globex.test (Member, Globex Corp)
            </p>
        </div>
    </div>
@endsection
