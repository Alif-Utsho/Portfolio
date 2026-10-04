<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#111411">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', $settings['seo_description'] ?? $settings['site_description'] ?? 'Software engineer focused on PHP and Laravel backend development.')">
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('meta_title', $settings['seo_title'] ?? $settings['site_title'] ?? 'Md Shahoriar Alif Utsho — Software Engineer')">
    <meta property="og:description" content="@yield('meta_description', $settings['seo_description'] ?? $settings['site_description'] ?? 'Software engineer focused on PHP and Laravel backend development.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <title>@yield('meta_title', $settings['seo_title'] ?? $settings['site_title'] ?? 'Md Shahoriar Alif Utsho — Software Engineer')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body data-analytics-opt-out-url="{{ route('analytics.opt-out') }}" data-analytics-event-url="{{ route('analytics.events') }}" data-analytics-activity-url="{{ route('analytics.activity') }}" data-project-id="@yield('analytics_project_id')">
    <a class="skip-link" href="#main">Skip to content</a>
    <header class="site-header" id="top">
        <nav class="nav-shell" aria-label="Main navigation">
            <a class="wordmark" href="{{ route('home') }}" aria-label="Alif — home"><span class="wordmark-mark">A<span>.</span></span><span class="wordmark-name">ALIF UTSHO</span></a>
            <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-menu" aria-label="Open navigation"><span></span><span></span></button>
            <div class="nav-links" id="primary-menu">
                @forelse ($navigationLinks as $link)
                    <a href="{{ $link->url }}">{{ $link->title }}</a>
                @empty
                    <a href="{{ route('home') }}#about">About</a><a href="{{ route('home') }}#experience">Experience</a><a href="{{ route('home') }}#skills">Stack</a><a href="{{ route('projects.index') }}">Projects</a><a href="{{ route('home') }}#beyond">Beyond code</a>
                @endforelse
            </div>
            <div class="nav-actions">
                <button class="theme-toggle" type="button" aria-label="Switch to light theme" title="Toggle theme"><span class="theme-icon" aria-hidden="true">◐</span></button>
                <a class="nav-cta" href="{{ route('home') }}#contact">Let’s talk <span aria-hidden="true">↗</span></a>
            </div>
        </nav>
    </header>
    <main id="main">@yield('content')</main>
    <aside class="analytics-notice" data-analytics-notice hidden aria-label="Analytics privacy notice"><p>This portfolio uses anonymous, first-party analytics to understand page visits and interactions. No raw IP address, full browser details, or form contents are stored.</p><button type="button" data-analytics-opt-out>Opt out</button><button type="button" data-analytics-notice-close aria-label="Dismiss notice">×</button></aside>
    <footer class="site-footer"><div class="section-wrap footer-inner"><a class="wordmark" href="{{ route('home') }}"><span class="wordmark-mark">A<span>.</span></span><span class="wordmark-name">ALIF UTSHO</span></a><span>DESIGNED TO BE USEFUL. BUILT TO LAST.</span><button class="privacy-preference" type="button" data-analytics-preferences>Privacy settings</button><a href="#top">BACK TO TOP ↑</a><span class="footer-year">© {{ date('Y') }} ALIF UTSHO</span></div></footer>
</body>
</html>
