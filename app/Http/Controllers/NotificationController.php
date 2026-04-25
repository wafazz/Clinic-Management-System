<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::where('user_id', auth()->id())
            ->when($request->type, fn($q, $v) => $q->where('type', $v))
            ->when($request->status === 'unread', fn($q) => $q->whereNull('read_at'))
            ->when($request->status === 'read', fn($q) => $q->whereNotNull('read_at'))
            ->orderByDesc('created_at');

        $notifications = $query->paginate(25)->withQueryString();
        $unreadCount = Notification::where('user_id', auth()->id())->whereNull('read_at')->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->markAsRead();

        if ($notification->link) {
            // Strip host so legacy localhost-prefixed links still resolve
            // against the current domain
            $path = parse_url($notification->link, PHP_URL_PATH);
            $query = parse_url($notification->link, PHP_URL_QUERY);
            $target = ($path ?: '/') . ($query ? '?' . $query : '');
            return redirect($target);
        }

        return back();
    }

    public function markAllRead()
    {
        Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->delete();
        return back()->with('success', 'Notification deleted.');
    }

    public function getUnreadCount()
    {
        $count = Notification::where('user_id', auth()->id())->whereNull('read_at')->count();
        return response()->json(['count' => $count]);
    }
}
