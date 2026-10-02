<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

/** Centre de notifications de l'utilisateur connecté (cloche du site). */
class NotificationCenterController extends Controller
{
    public function index(Request $request)
    {
        return view('notifications.index', [
            'notifications' => $request->user()->notifications()->latest()->paginate(25),
        ]);
    }

    /** Ouvre une notification : la marque comme lue et mène à la page concernée. */
    public function open(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->whereKey($id)->firstOrFail();
        $notification->markAsRead();
        $url = (string) ($notification->data['url'] ?? '');

        // Seules les adresses de l'application sont suivies.
        return Str::startsWith($url, [url('/'), '/']) && !Str::startsWith($url, '//') ? redirect($url) : redirect()->route('notifications.index');
    }

    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Toutes les notifications sont marquées comme lues.');
    }
}
