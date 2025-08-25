<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MessageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $messages = Message::forUser(auth()->id())
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'messages' => $messages,
            'unread_count' => Message::where('receiver_id', auth()->id())
                ->where('is_read', false)
                ->count()
        ]);
    }

    public function show(Request $request, Message $message): JsonResponse
    {
        if ($message->sender_id !== auth()->id() && $message->receiver_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($message->receiver_id === auth()->id() && !$message->is_read) {
            $message->markAsRead();
        }

        return response()->json([
            'message' => $message->load(['sender', 'receiver'])
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'content' => 'required|string|max:5000',
            'attachments' => 'nullable|array'
        ]);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'subject' => $request->subject,
            'content' => $request->content,
            'attachments' => $request->attachments
        ]);

        // Créer une notification pour le destinataire
        \App\Models\Notification::create([
            'user_id' => $request->receiver_id,
            'type' => 'message',
            'title' => 'Nouveau message',
            'content' => 'Vous avez reçu un nouveau message de ' . auth()->user()->name,
            'action_url' => '/portal/messages/' . $message->id
        ]);

        return response()->json([
            'message' => 'Message envoyé avec succès',
            'data' => $message->load(['sender', 'receiver'])
        ]);
    }

    public function getUsers(Request $request): JsonResponse
    {
        $users = User::where('id', '!=', auth()->id())
            ->where('role', '!=', 'system_admin')
            ->select('id', 'name', 'email', 'role')
            ->get();

        return response()->json(['users' => $users]);
    }

    public function getConversation(Request $request, int $userId): JsonResponse
    {
        $messages = Message::where(function($query) use ($userId) {
            $query->where('sender_id', auth()->id())
                  ->where('receiver_id', $userId);
        })->orWhere(function($query) use ($userId) {
            $query->where('sender_id', $userId)
                  ->where('receiver_id', auth()->id());
        })
        ->with(['sender', 'receiver'])
        ->orderBy('created_at', 'asc')
        ->get();

        // Marquer comme lus les messages reçus
        Message::where('sender_id', $userId)
            ->where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json(['messages' => $messages]);
    }

    public function destroy(Request $request, Message $message): JsonResponse
    {
        if ($message->sender_id !== auth()->id() && $message->receiver_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->deleteForUser(auth()->id());

        return response()->json([
            'message' => 'Message supprimé'
        ]);
    }
}
