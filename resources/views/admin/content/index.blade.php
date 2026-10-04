@extends('admin.layout')

@section('title', $label)

@section('content')
<div class="page-heading">
    <div><span class="sidebar-label">CONTENT MANAGER</span>
        <h1>{{ $label }}</h1>
        <p>Manage and publish {{ strtolower($label) }} on the portfolio.</p>
    </div><a class="admin-button admin-button-primary" href="{{ route('admin.content.create', $type) }}">Add {{ \Illuminate\Support\Str::singular(strtolower($label)) }} <span>+</span></a>
</div>
<section class="admin-panel table-panel">
    <div class="table-scroll">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Details</th>
                    <th>Status</th>
                    <th>Order</th>
                    <th><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)<tr>
                    <td><strong>{{ $item->title }}</strong>@if ($item->slug)<small>/projects/{{ $item->slug }}</small>@endif</td>
                    <td>{{ $item->subtitle ?: ($item->summary ? \Illuminate\Support\Str::limit($item->summary, 65) : '—') }}</td>
                    <td><span class="badge {{ $item->is_published ? 'badge-published' : 'badge-draft' }}">{{ $item->is_published ? 'Published' : 'Draft' }}</span></td>
                    <td>{{ $item->sort_order }}</td>
                    <td>
                        <div class="table-actions"><a href="{{ route('admin.content.edit', $item) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.content.destroy', $item) }}" data-confirm="Delete this content item? This cannot be undone.">@csrf @method('DELETE')<button type="submit">Delete</button></form>
                        </div>
                    </td>
                </tr>@empty<tr>
                    <td colspan="5">
                        <div class="empty-panel"><span>◇</span>
                            <p>No {{ strtolower($label) }} yet. Create the first entry to get started.</p>
                        </div>
                    </td>
                </tr>@endforelse
            </tbody>
        </table>
    </div>
    <div class="simple-pagination">{{ $items->links('pagination::simple-tailwind') }}</div>
</section>
@endsection