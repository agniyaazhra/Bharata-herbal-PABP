<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{

    public function index(Request $request)
    {
        $query = Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('is_read')) {
            $query->where('is_read', (bool) $request->is_read);
        }

        $notifications = $query->paginate(20)->withQueryString();
        $unreadCount   = Notification::where('user_id', auth()->id())->unread()->count();

        return view('admin.notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markRead(Notification $notification)
    {
        abort_if($notification->user_id !== auth()->id(), 403);

        $notification->markAsRead();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back();
    }

    public function markAllRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Semua notifikasi ditandai telah dibaca.']);
        }

        return back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }

    public function destroy(Notification $notification)
    {
        abort_if($notification->user_id !== auth()->id(), 403);

        $notification->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notifikasi dihapus.');
    }

    public function unreadCount()
    {
        $count = Notification::where('user_id', auth()->id())->unread()->count();

        return response()->json(['count' => $count]);
    }

    public function latest()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'notifications' => $notifications->map(fn($n) => [
                'id'         => $n->id,
                'title'      => $n->title,
                'message'    => $n->message,
                'type'       => $n->type,
                'is_read'    => $n->is_read,
                'created_at' => $n->created_at->diffForHumans(),
            ]),
            'unread_count' => $notifications->where('is_read', false)->count(),
        ]);
    }
}
