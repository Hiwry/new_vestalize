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
    public function index(Request $request)
    {
        $userId = Auth::id();
        $cacheKey = "notifications_{$userId}";

        if ($request->wantsJson() || $request->ajax()) {
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

        $notifications = Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        $unreadCount = Notification::where('user_id', $userId)
            ->where('read', false)
            ->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
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
    public function markAllAsRead(Request $request)
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
        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Notificações marcadas como lidas.');
    }

    /**
     * Deletar notificação
     */
    public function destroy(Request $request, $id)
    {
        $userId = Auth::id();
        $notification = Notification::where('user_id', $userId)
            ->findOrFail($id);

        $notification->delete();
        
        // Invalidar cache
        Cache::forget("notifications_{$userId}");
        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Notificação removida.');
    }

    /**
     * Limpar todas as notificações
     */
    public function clearAll(Request $request)
    {
        $userId = Auth::id();
        Notification::where('user_id', $userId)->delete();
        Cache::forget("notifications_{$userId}");

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Notificações removidas.');
    }
}
