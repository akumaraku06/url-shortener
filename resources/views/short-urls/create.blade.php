@extends('layouts.app')

@section('title', 'Generate Short URL')

@section('content')
    <div class="card" style="max-width:560px;">
        <div class="card-header">
            <h2>Generate Short URL</h2>
        </div>

        <form method="POST" action="{{ route('short-urls.store') }}" class="form-card">
            @csrf
            <label for="original_url">Long URL</label>
            <input type="url" id="original_url" name="original_url" placeholder="e.g. https://.com/travel-software/features/best-itinerary-builder" value="{{ old('original_url') }}" required>

            <button type="submit" class="btn btn-primary" style="width:100%;margin-top:18px;">Generate</button>
        </form>
    </div>
@endsection
