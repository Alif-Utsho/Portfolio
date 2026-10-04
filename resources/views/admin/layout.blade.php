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
                <span class="sidebar-label nav-section-label">ANALYTICS</span>
                <a @class(['active' => request()->routeIs('admin.analytics.overview')]) href="{{ route('admin.analytics.overview') }}"><span>◷</span>Analytics</a>
                <a @class(['active' => request()->routeIs('admin.analytics.visitors*')]) href="{{ route('admin.analytics.visitors') }}"><span>◎</span>Visitors</a>
                <a @class(['active' => request()->routeIs('admin.analytics.sources')]) href="{{ route('admin.analytics.sources') }}"><span>↗</span>Traffic sources</a>
                <a @class(['active' => request()->routeIs('admin.analytics.pages*') || request()->routeIs('admin.analytics.page')]) href="{{ route('admin.analytics.pages') }}"><span>▤</span>Pages</a>
                <a @class(['active' => request()->routeIs('admin.analytics.events')]) href="{{ route('admin.analytics.events') }}"><span>⌘</span>Events</a>
                <a @class(['active' => request()->routeIs('admin.analytics.geography')]) href="{{ route('admin.analytics.geography') }}"><span>⊕</span>Geography</a>
                <a @class(['active' => request()->routeIs('admin.analytics.realtime')]) href="{{ route('admin.analytics.realtime') }}"><span>●</span>Real time</a>
                <span class="sidebar-label nav-section-label">CONTENT</span>
                @foreach (config('portfolio.content_types') as $type => $label)<a @class(['active' => request()->route('type') === $type]) href="{{ route('admin.content.index', $type) }}"><span>{{ ['project' => '◇', 'experience' => '↗', 'skill' => '⌘', 'personal' => '◎', 'social' => '↗', 'navigation' => '☰'][$type] }}</span>{{ $label }}</a>@endforeach
                <a @class(['active' => request()->routeIs('admin.media.*')]) href="{{ route('admin.media.index') }}"><span>▧</span>Media library</a>
                <a @class(['active' => request()->routeIs('admin.messages.*')]) href="{{ route('admin.messages.index') }}"><span>✉</span>Messages <small class="nav-count">{{ \App\Models\ContactMessage::query()->where('status', 'unread')->count() }}</small></a>
                <a @class(['active' => request()->routeIs('admin.settings.*')]) href="{{ route('admin.settings.edit') }}"><span>⚙</span>Site settings</a>
            </nav>
            <div class="sidebar-bottom"><a href="{{ route('home') }}" target="_blank" rel="noreferrer">↗ View live site</a><form method="POST" action="{{ route('admin.logout') }}">@csrf<button type="submit">↪ Sign out</button></form><span>OWNER ACCOUNT</span></div>
        </aside>
        <main class="admin-main">
            <header class="admin-topbar"><button type="button" class="sidebar-toggle" aria-controls="admin-sidebar" aria-expanded="false" aria-label="Open navigation">☰</button><span class="admin-current-title">{{ trim($__env->yieldContent('title', 'Portfolio admin')) }}</span><a class="admin-topbar-messages" href="{{ route('admin.messages.index') }}" aria-label="Unread messages">✉ <b>{{ \App\Models\ContactMessage::query()->where('status', 'unread')->count() }}</b></a><details class="admin-account"><summary>{{ auth()->user()->name }}⌄</summary><form method="POST" action="{{ route('admin.logout') }}">@csrf<button type="submit">Sign out</button></form></details></header>
            <div class="admin-content">
                @if (session('status'))<div class="notice notice-success" role="status">{{ session('status') }}</div>@endif
                @if ($errors->any())<div class="notice notice-error" role="alert">Please review the fields highlighted below.</div>@endif
                @yield('content')
            </div>
        </main>
    </div>
    <button class="admin-sidebar-overlay" type="button" aria-label="Close navigation" tabindex="-1"></button>
</body>
</html>
