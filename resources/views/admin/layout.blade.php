<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow"><title>@yield('title', 'Admin') · Alif Utsho</title>
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="admin-body">
    <div class="admin-frame">
        <aside class="admin-sidebar" id="admin-sidebar">
            <a class="admin-brand" href="{{ route('admin.dashboard') }}"><span class="brand-mark">A<span>.</span></span><span>ALIF UTSHO<small>PORTFOLIO ADMIN</small></span></a>
            <span class="sidebar-label">WORKSPACE</span>
            <nav class="admin-nav" aria-label="Admin navigation">
                <a @class(['active' => request()->routeIs('admin.dashboard')]) href="{{ route('admin.dashboard') }}"><span>⌂</span>Overview</a>
                @foreach (config('portfolio.content_types') as $type => $label)<a @class(['active' => request()->route('type') === $type]) href="{{ route('admin.content.index', $type) }}"><span>{{ ['project' => '◇', 'experience' => '↗', 'skill' => '⌘', 'personal' => '◎', 'social' => '↗', 'navigation' => '☰'][$type] }}</span>{{ $label }}</a>@endforeach
                <a @class(['active' => request()->routeIs('admin.media.*')]) href="{{ route('admin.media.index') }}"><span>▧</span>Media library</a>
                <a @class(['active' => request()->routeIs('admin.messages.*')]) href="{{ route('admin.messages.index') }}"><span>✉</span>Messages <small class="nav-count">{{ \App\Models\ContactMessage::query()->where('status', 'unread')->count() }}</small></a>
                <a @class(['active' => request()->routeIs('admin.settings.*')]) href="{{ route('admin.settings.edit') }}"><span>⚙</span>Site settings</a>
            </nav>
            <div class="sidebar-bottom"><a href="{{ route('home') }}" target="_blank" rel="noreferrer">↗ View live site</a><form method="POST" action="{{ route('admin.logout') }}">@csrf<button type="submit">↪ Sign out</button></form><span>OWNER ACCOUNT</span></div>
        </aside>
        <main class="admin-main">
            <header class="admin-topbar"><button type="button" class="sidebar-toggle" aria-controls="admin-sidebar" aria-expanded="false">☰<span class="sr-only">Toggle menu</span></button><span>PORTFOLIO / ADMIN</span><span class="admin-user">{{ auth()->user()->name }}</span></header>
            <div class="admin-content">
                @if (session('status'))<div class="notice notice-success" role="status">{{ session('status') }}</div>@endif
                @if ($errors->any())<div class="notice notice-error" role="alert">Please review the fields highlighted below.</div>@endif
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
