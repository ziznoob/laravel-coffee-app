@extends('layouts.app')

@section('title', 'Log in')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-sm-8 col-md-6 col-lg-4">
                <form method="POST" action="{{ route('login') }}" class="card card-body shadow-sm">
                    @csrf

                    <h1 class="h4 mb-3">Log in</h1>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                            @class(['form-control', 'is-invalid' => $errors->has('email')])>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" name="password" type="password" required class="form-control">
                    </div>

                    <div class="form-check mb-3">
                        <input id="remember" type="checkbox" name="remember" class="form-check-input">
                        <label for="remember" class="form-check-label">Remember me</label>
                    </div>

                    <button type="submit" class="btn btn-dark w-100">Log in</button>
                </form>
            </div>
        </div>
    </div>
@endsection
