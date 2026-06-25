<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    /**
     * Returns a signed token for socket.io authentication
     */
    public function token(Request $request)
    {
        $user = auth()->user();
        abort_unless($user, 401);

        $token = base64_encode(json_encode([
            'user_id'   => $user->id,
            'name'      => $user->name,
            'role'      => $user->can('order.chat') ? 'staff' : 'customer',
            'signature' => hash_hmac('sha256', $user->id, config('app.key')),
        ]));

        return response()->json(['token' => $token]);
    }

    /**
     * Returns message history for an order
     */
    public function messages(Order $order)
    {
        $this->authorizeOrderAccess($order);

        return response()->json(
            $order->messages()
                ->with('user:id,name')
                ->latest('created_at')
                ->limit(100)
                ->get()
                ->sortBy('created_at')
                ->values()
        );
    }

    /**
     * Save message — called by Node.js with internal key OR by browser fallback
     */
    public function store(Request $request, Order $order)
    {
        // Allow internal Node.js call with secret key
        $isInternal = $request->header('X-Internal-Key') === config('chat.internal_key');

        if (!$isInternal) {
            abort_unless(auth()->check(), 401);
            $this->authorizeOrderAccess($order);
        }

        if (!$order->isChatOpen()) {
            return response()->json(['error' => 'Chat closed'], 403);
        }

        $rules = ['message' => 'required|string|max:2000'];

        if ($isInternal) {
            $rules['user_id'] = 'required|exists:users,id';
            $rules['sender_role'] = 'required|in:customer,staff';
        }

        $validated = $request->validate($rules);

        $senderRole = $validated['sender_role'] ?? null;

        // If not internal call, determine role from user
        if (!$isInternal) {
            $senderRole = auth()->user()->can('order.chat') ? 'staff' : 'customer';
        }

        $msg = OrderMessage::create([
            'order_id'    => $order->id,
            'user_id'     => $validated['user_id'] ?? auth()->id(),
            'sender_role' => $senderRole,
            'message'     => $validated['message'],
        ]);

        return response()->json($msg->load('user:id,name'));
    }

    /**
     * Close chat (called when order status changes to delivered/cancelled)
     */
    public function close(Order $order)
    {
        $isInternal = request()->header('X-Internal-Key') === config('chat.internal_key');

        if (!$isInternal) {
            abort_unless(auth()->check(), 401);
            $this->authorizeOrderAccess($order);
        }

        // Mark all messages as read
        OrderMessage::where('order_id', $order->id)->update(['is_read' => true]);

        // Notify Node.js to close the room
        try {
            Http::withHeaders([
                'X-Internal-Key' => config('chat.internal_key'),
                'Accept' => 'application/json',
            ])->post(config('chat.node_url') . '/internal/close-room/' . $order->id);
        } catch (\Exception $e) {
            // Log error but don't fail the request
            logger()->error('Failed to notify Node.js of chat close: ' . $e->getMessage());
        }

        return response()->json(['closed' => true]);
    }

    /**
     * Get unread message count for an order
     */
    public function unreadCount(Order $order)
    {
        $this->authorizeOrderAccess($order);

        $role = auth()->user()->can('order.chat') ? 'customer' : 'staff';

        return response()->json([
            'count' => OrderMessage::where('order_id', $order->id)
                ->where('sender_role', $role)
                ->where('is_read', false)
                ->count()
        ]);
    }

    /**
     * Authorize access to order chat
     * Staff with permission can access all orders
     * Customers can only access their own orders
     */
    private function authorizeOrderAccess(Order $order): void
    {
        $user = auth()->user();

        if ($user->can('order.chat')) {
            return; // staff — allow all orders
        }

        abort_if($order->user_id !== $user->id, 403); // customer — own orders only
    }
}
