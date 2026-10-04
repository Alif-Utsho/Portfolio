@extends('admin.auth.layout')

@section('title', 'Create owner account')
@section('heading', 'Set up your admin.')
@section('description', 'Create the private owner account. This one-time screen closes after setup.')

@section('content')
    @if (! $setupAvailable)
        <div class="notice notice-error">Admin setup is disabled. Set <code>ADMIN_SETUP_KEY</code> in the server environment, then reload this page.</div>
    @else
        <form class="admin-form auth-form" method="POST" action="{{ route('admin.setup.store') }}">@csrf
            <label for="setup_key">One-time setup key</label><input id="setup_key" name="setup_key" type="password" required autocomplete="off">@error('setup_key')<span class="field-error">{{ $message }}</span>@enderror
            <label for="name">Your name</label><input id="name" name="name" value="{{ old('name') }}" autocomplete="name" required>@error('name')<span class="field-error">{{ $message }}</span>@enderror
            <label for="email">Email address</label><input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>@error('email')<span class="field-error">{{ $message }}</span>@enderror
            <label for="password">Password <small>At least 12 characters, mixed case and a number</small></label><input id="password" name="password" type="password" autocomplete="new-password" required>@error('password')<span class="field-error">{{ $message }}</span>@enderror
            <label for="password_confirmation">Confirm password</label><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
            <button class="admin-button admin-button-primary" type="submit">Create owner account <span>↗</span></button>
        </form>
    @endif
    <a class="auth-back" href="{{ route('home') }}">← Return to portfolio</a>
@endsection
