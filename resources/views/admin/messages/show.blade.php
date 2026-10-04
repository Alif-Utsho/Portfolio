@extends('admin.layout')

@section('title', 'Message from '.$message->name)

@section('content')
    <div class="page-heading"><div><a class="admin-text-link" href="{{ route('admin.messages.index') }}">← Contact inbox</a><h1>{{ $message->subject ?: 'Message from '.$message->name }}</h1><p>Received {{ $message->created_at->format('F j, Y \a\t g:i A') }}</p></div></div>
    <article class="admin-panel message-detail"><div class="message-sender"><span class="sender-avatar">{{ mb_strtoupper(mb_substr($message->name, 0, 1)) }}</span><div><strong>{{ $message->name }}</strong><a href="mailto:{{ $message->email }}">{{ $message->email }}</a></div><span class="badge badge-{{ $message->status }}">{{ ucfirst($message->status) }}</span></div><div class="message-body">{{ $message->message }}</div><div class="message-actions"><form method="POST" action="{{ route('admin.messages.status', $message) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="{{ $message->status === 'archived' ? 'unread' : 'archived' }}"><button class="admin-button admin-button-secondary" type="submit">{{ $message->status === 'archived' ? 'Restore message' : 'Archive message' }}</button></form><form method="POST" action="{{ route('admin.messages.destroy', $message) }}" data-confirm="Permanently delete this message?">@csrf @method('DELETE')<button class="admin-button admin-button-danger" type="submit">Delete message</button></form></div></article>
@endsection
