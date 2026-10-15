@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="card card-body shadow-sm">
        <h1 class="h4">Welcome, {{ auth()->user()->name }}</h1>
        <p class="mb-0 text-body-secondary">You're logged in.</p>
    </div>
@endsection
