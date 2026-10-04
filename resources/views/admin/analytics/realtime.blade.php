@extends('admin.layout')
@section('title', 'Real-time visitors')
@section('content')
<div class="page-heading"><div><span class="sidebar-label">ACTIVITY IN THE LAST TWO MINUTES</span><h1>Real time</h1><p>This view refreshes every 30 seconds while this page is visible.</p></div><span class="live-pill"><i></i><b data-live-count>0</b> online</span></div><section class="admin-panel" data-realtime-widget data-live-url="{{ route('admin.analytics.live') }}"><div class="panel-heading"><div><span class="sidebar-label">LIVE SESSIONS</span><h2>Visitors right now</h2></div><span data-live-status role="status">Loading live activity…</span></div><div class="analytics-live-list" data-live-list><div class="empty-panel"><p>Loading visitor activity…</p></div></div></section>
@endsection
