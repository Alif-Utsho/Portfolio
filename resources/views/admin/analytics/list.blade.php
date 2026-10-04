@extends('admin.layout')
@section('title', $title)
@section('content')
<div class="page-heading"><div><span class="sidebar-label">{{ $eyebrow }}</span><h1>{{ $title }}</h1><p>{{ $description }}</p></div><a class="admin-button admin-button-quiet" href="{{ route('admin.analytics.export', array_merge(request()->query(), ['kind' => $kind])) }}">Export CSV ↓</a></div>
@include('admin.analytics.filters')
<div class="admin-panel table-panel"><div class="table-scroll"><table><thead>@if ($kind === 'visitors')<tr><th>Visitor</th><th>First seen</th><th>Location</th><th>Device</th><th>Latest page</th><th>Pages</th></tr>@elseif ($kind === 'pages')<tr><th>Page</th><th>Views</th><th>Details</th></tr>@else<tr><th>Event</th><th>Page</th><th>Project</th><th>When</th></tr>@endif</thead><tbody>
@forelse ($rows as $row)
    @if ($kind === 'visitors')<tr><td><a href="{{ route('admin.analytics.visitor', $row->visitor_id) }}">{{ \Illuminate\Support\Str::limit($row->visitor_key, 14) }}</a><small>{{ $row->visitor_type }} visitor</small></td><td>{{ \Illuminate\Support\Carbon::parse($row->first_seen_at)->format('M j, Y H:i') }}<small>Visit {{ \Illuminate\Support\Carbon::parse($row->started_at)->format('M j, H:i') }}</small></td><td>{{ collect([$row->city, $row->country])->filter()->join(', ') ?: 'Unknown' }}</td><td>{{ ucfirst($row->device) }} · {{ $row->browser }} / {{ $row->operating_system }}</td><td>{{ $row->landing_path }}</td><td>{{ $row->page_views }}</td></tr>
    @elseif ($kind === 'pages')<tr><td>{{ $row->path }}</td><td>{{ $row->total }}</td><td><a href="{{ route('admin.analytics.page', ['path' => $row->path]) }}">View detail</a></td></tr>
    @else<tr><td>{{ str_replace('_', ' ', ucfirst($row->event_name)) }}</td><td>{{ $row->path }}</td><td>{{ $row->project_id ? 'Project #'.$row->project_id : '—' }}</td><td>{{ \Illuminate\Support\Carbon::parse($row->occurred_at)->format('M j, Y H:i') }}</td></tr>@endif
@empty<tr><td colspan="6"><div class="empty-panel"><span>⌁</span><p>No {{ strtolower($title) }} were recorded for these filters.</p></div></td></tr>@endforelse
</tbody></table></div><div class="simple-pagination">{{ $rows->links('pagination::simple-tailwind') }}</div></div>
@endsection
