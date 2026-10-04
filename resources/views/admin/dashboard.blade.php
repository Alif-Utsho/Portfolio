@extends('admin.layout')

@section('title', 'Overview')

@section('content')
    <div class="page-heading"><div><span class="sidebar-label">YOUR PORTFOLIO</span><h1>Overview</h1><p>Manage what visitors see and keep an eye on incoming messages.</p></div><a class="admin-button admin-button-primary" href="{{ route('home') }}" target="_blank" rel="noreferrer">View portfolio ↗</a></div>
    <div class="stat-cards"><article><span>CONTENT ITEMS</span><strong>{{ $contentCount }}</strong><small>Across all content types</small></article><article><span>PUBLISHED</span><strong>{{ $publishedCount }}</strong><small>Visible on the public site</small></article><article><span>UNREAD MESSAGES</span><strong>{{ $unreadCount }}</strong><small>Waiting in your inbox</small></article></div>
    <section class="admin-panel"><div class="panel-heading"><div><span class="sidebar-label">RECENT ACTIVITY</span><h2>Contact inbox</h2></div><a class="admin-text-link" href="{{ route('admin.messages.index') }}">View all messages →</a></div>
        @forelse ($recentMessages as $message)<a class="message-row" href="{{ route('admin.messages.show', $message) }}"><span class="message-status status-{{ $message->status }}"></span><span class="message-main"><strong>{{ $message->name }}</strong><small>{{ $message->subject ?: $message->email }}</small></span><time>{{ $message->created_at->diffForHumans() }}</time></a>@empty<div class="empty-panel"><span>✉</span><p>No messages yet. New contact form submissions will appear here.</p></div>@endforelse
    </section>
    <section class="quick-links"><a href="{{ route('admin.content.index', 'project') }}"><span>◇</span><strong>Manage projects</strong><small>Add work and edit project pages</small></a><a href="{{ route('admin.settings.edit') }}"><span>⚙</span><strong>Site settings</strong><small>Update profile, links, and SEO</small></a><a href="{{ route('admin.media.index') }}"><span>▧</span><strong>Media library</strong><small>Upload portfolio images</small></a></section>
@endsection
