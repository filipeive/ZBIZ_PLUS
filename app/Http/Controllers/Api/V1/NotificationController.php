<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = Notification::where('user_id', $request->user()?->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($notifications);
    }

    public function markAsRead(Request $request, Notification $notification): JsonResponse
    {
        $notification->update(['read_at' => now()]);
        return response()->json(['message' => 'Notificação marcada como lida.']);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        Notification::where('user_id', $request->user()?->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'Todas as notificações marcadas como lidas.']);
    }

    public function clearAll(Request $request): JsonResponse
    {
        Notification::where('user_id', $request->user()?->id)->delete();
        return response()->json(['message' => 'Notificações eliminadas.']);
    }
}
