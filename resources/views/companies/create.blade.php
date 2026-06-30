@extends('layouts.app')

@section('title', 'Invite New Client')

@section('content')
    <div class="card" style="max-width:480px;">
        <div class="card-header">
            <h2>Invite New Client</h2>
        </div>
        <p class="card-subtitle">This creates a new client (company) along with its first Admin account.</p>

        <form method="POST" action="{{ route('companies.store') }}" class="form-card">
            @csrf
            <label for="company_name">Client Name</label>
            <input type="text" id="company_name" name="company_name" placeholder="e.g. Acme Corp" value="{{ old('company_name') }}" required>

            <label for="admin_name">Admin Name</label>
            <input type="text" id="admin_name" name="admin_name" value="{{ old('admin_name') }}" required>

            <label for="admin_email">Admin Email</label>
            <input type="email" id="admin_email" name="admin_email" placeholder="ex. name@example.com" value="{{ old('admin_email') }}" required>

            <label for="admin_password">Admin Password</label>
            <input type="password" id="admin_password" name="admin_password" required minlength="8">

            <button type="submit" class="btn btn-primary" style="width:100%;margin-top:18px;">Send Invitation</button>
        </form>
    </div>
@endsection
