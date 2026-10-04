@extends('admin.auth.layout')

@section('title', 'Sign in')
@section('heading', 'Welcome back.')
@section('description', 'Sign in to manage the portfolio and review messages.')

@section('content')
    <form class="admin-form auth-form" method="POST" action="{{ route('admin.login.store') }}">@csrf
        <label for="email">Email address</label><input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required>@error('email')<span class="field-error">{{ $message }}</span>@enderror
        <label for="password">Password</label><input id="password" name="password" type="password" autocomplete="current-password" required>@error('password')<span class="field-error">{{ $message }}</span>@enderror
        <button class="admin-button admin-button-primary" type="submit">Sign in <span>↗</span></button>
    </form>
    <a class="auth-back" href="{{ route('home') }}">← Return to portfolio</a>
@endsection
