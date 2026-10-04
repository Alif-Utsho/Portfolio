<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactMessageController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');
        abort_unless(in_array($status, ['all', 'unread', 'read', 'archived'], true), 404);

        return view('admin.messages.index', [
            'status' => $status,
            'messages' => ContactMessage::query()
                ->when($status !== 'all', fn ($query) => $query->where('status', $status))
                ->latest()
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    public function show(ContactMessage $message): View
    {
        if ($message->status === 'unread') {
            $message->update(['status' => 'read']);
        }

        return view('admin.messages.show', ['message' => $message]);
    }

    public function updateStatus(Request $request, ContactMessage $message): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['unread', 'read', 'archived'])],
        ]);
        $message->update($validated);

        return back()->with('status', 'Message status updated.');
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $message->delete();

        return to_route('admin.messages.index')->with('status', 'Message deleted.');
    }
}
