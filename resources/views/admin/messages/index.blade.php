@extends('admin.layout')

@section('title', 'Contact inbox')

@section('content')
    <div class="page-heading"><div><span class="sidebar-label">CONTACT INBOX</span><h1>Messages</h1><p>Contact form submissions are stored here. No email is sent.</p></div></div>
    <div class="filter-tabs">@foreach (['all' => 'All', 'unread' => 'Unread', 'read' => 'Read', 'archived' => 'Archived'] as $key => $label)<a @class(['active' => $status === $key]) href="{{ route('admin.messages.index', ['status' => $key]) }}">{{ $label }}</a>@endforeach</div>
    <section class="admin-panel table-panel"><div class="table-scroll"><table><thead><tr><th>From</th><th>Subject</th><th>Status</th><th>Received</th><th></th></tr></thead><tbody>@forelse ($messages as $message)<tr><td><a href="{{ route('admin.messages.show', $message) }}"><strong>{{ $message->name }}</strong><small>{{ $message->email }}</small></a></td><td>{{ $message->subject ?: 'No subject' }}</td><td><span class="badge badge-{{ $message->status }}">{{ ucfirst($message->status) }}</span></td><td>{{ $message->created_at->format('M j, Y') }}</td><td><a class="admin-text-link" href="{{ route('admin.messages.show', $message) }}">Open →</a></td></tr>@empty<tr><td colspan="5"><div class="empty-panel"><span>✉</span><p>No messages in this view.</p></div></td></tr>@endforelse</tbody></table></div><div class="simple-pagination">{{ $messages->links('pagination::simple-tailwind') }}</div></section>
@endsection
