<?php
namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', auth()->id())->findOrFail($id);
        $notification->markAsRead();
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->each->markAsRead();
        return response()->json(['success' => true]);
    }

    public function clearAll()
    {
        auth()->user()->notifications()->delete();
        return response()->json(['success' => true]);
    }
}