@extends('admin.layout')
@section('title', $title)
@section('content')
<div class="page-heading"><div><span class="sidebar-label">{{ $eyebrow }}</span><h1>{{ $title }}</h1><p>{{ $description }}</p></div><a class="admin-button admin-button-quiet" href="{{ route('admin.analytics.export', array_merge(request()->query(), ['kind' => 'sessions'])) }}">Export CSV ↓</a></div>
@include('admin.analytics.filters')
<section class="admin-panel table-panel"><div class="table-scroll"><table><thead><tr><th>{{ $title === 'Geography' ? 'Country' : 'Source' }}</th><th>{{ $valueLabel }}</th></tr></thead><tbody>@forelse ($rows as $row)<tr><td>{{ $row->label ?: 'Direct' }}</td><td>{{ $row->total }}</td></tr>@empty<tr><td colspan="2"><div class="empty-panel"><p>No {{ strtolower($title) }} data for this period.</p></div></td></tr>@endforelse</tbody></table></div>@if (method_exists($rows, 'links'))<div class="simple-pagination">{{ $rows->links('pagination::simple-tailwind') }}</div>@endif</section>
@endsection
