@extends('layouts.app')

@section('title', 'Passeport du Jour')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div id="daily-passport-app">
        <daily-passport></daily-passport>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Le composant Vue sera monté automatiquement par app.js
</script>
@endpush 