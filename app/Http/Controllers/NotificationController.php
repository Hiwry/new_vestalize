<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class NotificationController extends Controller
{
    /**
     * Buscar notificações do usuário (API)
     * Otimizado com cache de 10 segundos
     */
    public function index()
    {
        $userId = Auth::id();
        $cacheKey = "notifications_{$userId}";
        
        return Cache::remember($cacheKey, 10, function () use ($userId) {
            $notifications = Notification::where('user_id', $userId)
                ->select(['id', 'title', 'message', 'type', 'link', 'read', 'created_at'])
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();

            $unreadCount = Notification::where('user_id', $userId)
                ->where('read', false)
                ->count();

            return response()->json([
                'notifications' => $notifications,
                'unread_count' => $unreadCount,
            ]);
        });
    }

    /**
     * Marcar notificação como lida
     */
    public function markAsRead($id)
    {
        $userId = Auth::id();
        $notification = Notification::where('user_id', $userId)
            ->findOrFail($id);

        $notification->markAsRead();
        
        // Invalidar cache
        Cache::forget("notifications_{$userId}");

        return response()->json(['success' => true]);
    }

    /**
     * Marcar todas como lidas
     */
    public function markAllAsRead()
    {
        $userId = Auth::id();
        
        Notification::where('user_id', $userId)
            ->where('read', false)
            ->update([
                'read' => true,
                'read_at' => now(),
            ]);
        
        // Invalidar cache
        Cache::forget("notifications_{$userId}");

        return response()->json(['success' => true]);
    }

    /**
     * Deletar notificação
     */
    public function destroy($id)
    {
        $userId = Auth::id();
        $notification = Notification::where('user_id', $userId)
            ->findOrFail($id);

        $notification->delete();
        
        // Invalidar cache
        Cache::forget("notifications_{$userId}");

        return response()->json(['success' => true]);
    }
}
