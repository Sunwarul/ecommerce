<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Support\Search;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $messages = ContactMessage::query()
            ->with('reader:id,name')
            ->when($request->filter === 'unread', fn ($q) => $q->unread())
            ->when($request->filter === 'replied', fn ($q) => $q->whereNotNull('replied_at'))
            ->when($request->filter === 'trashed', fn ($q) => $q->onlyTrashed())
            ->when($request->search, fn ($q, $term) => Search::apply($q, $term, ['name', 'email', 'subject', 'message'], ['phone']))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/ContactMessages/Index', [
            'messages' => $messages,
            'filters' => $request->only('search', 'filter'),
            'counts' => [
                'all' => ContactMessage::count(),
                'unread' => ContactMessage::unread()->count(),
                'replied' => ContactMessage::whereNotNull('replied_at')->count(),
                'trashed' => ContactMessage::onlyTrashed()->count(),
            ],
        ]);
    }

    /** Opening a message marks it read. */
    public function read(Request $request, ContactMessage $message)
    {
        if (! $message->read_at) {
            $message->update(['read_at' => now(), 'read_by' => $request->user()->id]);
        }

        return back();
    }

    public function update(Request $request, ContactMessage $message)
    {
        $data = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:2000'],
            'replied' => ['nullable', 'boolean'],
            'unread' => ['nullable', 'boolean'],
        ]);

        if ($request->has('admin_note')) {
            $message->admin_note = $data['admin_note'];
        }
        if ($request->has('replied')) {
            $message->replied_at = $request->boolean('replied') ? ($message->replied_at ?? now()) : null;
        }
        if ($request->boolean('unread')) {
            $message->read_at = null;
            $message->read_by = null;
        }
        $message->save();

        return back()->with('success', __('Message updated.'));
    }

    public function bulk(Request $request)
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'max:500'],
            'ids.*' => ['integer'],
            'action' => ['required', 'in:read,unread,delete,restore,force-delete'],
        ]);
        if (in_array($data['action'], ['delete', 'restore', 'force-delete'], true)) {
            abort_unless($request->user()->can('Contact Message Delete'), 403);
        }

        $query = ContactMessage::withTrashed()->whereIn('id', $data['ids']);
        match ($data['action']) {
            'read' => $query->whereNull('read_at')->update(['read_at' => now(), 'read_by' => $request->user()->id]),
            'unread' => $query->update(['read_at' => null, 'read_by' => null]),
            'delete' => $query->whereNull('deleted_at')->get()->each->delete(),
            'restore' => $query->onlyTrashed()->restore(),
            'force-delete' => $query->onlyTrashed()->get()->each->forceDelete(),
        };

        return back()->with('success', __('Done.'));
    }
}
