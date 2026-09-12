<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\ProductBatch;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display the notifications management page.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $tenantId = $user?->tenant_id;
        $branchId = session('active_branch_id');

        // User notifications (DB)
        $notifications = Notification::where('user_id', $user->id)
            ->orderByRaw('read ASC, created_at DESC')
            ->paginate(20);

        $unreadCount = Notification::where('user_id', $user->id)
            ->where('read', false)
            ->count();

        // System alerts: expiring products within 90 days
        $expiringBatches = collect();
        if ($tenantId) {
            $expiringBatches = ProductBatch::with('product.category')
                ->where('tenant_id', $tenantId)
                ->whereNotNull('expiry_date')
                ->where('quantity', '>', 0)
                ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                ->whereDate('expiry_date', '<=', now()->addDays(90))
                ->orderBy('expiry_date', 'asc')
                ->get();
        }

        return view('notifications.index', compact(
            'notifications', 'unreadCount', 'expiringBatches'
        ));
    }

    /**
     * Mark a single notification as read (AJAX).
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', auth()->id())->findOrFail($id);
        $notification->markAsRead();
        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read (AJAX).
     */
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->each->markAsRead();
        return response()->json(['success' => true]);
    }

    /**
     * Delete (clear) all notifications.
     */
    public function clearAll()
    {
        auth()->user()->notifications()->delete();
        return response()->json(['success' => true]);
    }

    /**
     * API: return paginated notifications for the bell dropdown.
     */
    public function apiList(Request $request)
    {
        $user = auth()->user();
        $tenantId = $user?->tenant_id;
        $branchId = session('active_branch_id');

        $notifications = Notification::where('user_id', $user->id)
            ->orderByRaw('read ASC, created_at DESC')
            ->limit(10)
            ->get()
            ->map(fn ($n) => [
                'id'         => $n->id,
                'title'      => $n->title,
                'message'    => $n->message,
                'type'       => $n->type,
                'icon'       => $n->icon,
                'read'       => $n->read,
                'action_url' => $n->action_url,
                'created_at' => $n->created_at?->diffForHumans(),
            ]);

        $unreadCount = Notification::where('user_id', $user->id)
            ->where('read', false)
            ->count();

        // Expiring batches for notification panel
        $expiringCount = 0;
        if ($tenantId) {
            $expiringCount = ProductBatch::where('tenant_id', $tenantId)
                ->whereNotNull('expiry_date')
                ->where('quantity', '>', 0)
                ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                ->whereDate('expiry_date', '<=', now()->addDays(30))
                ->count();
        }

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $unreadCount,
            'expiring_count' => $expiringCount,
        ]);
    }
}