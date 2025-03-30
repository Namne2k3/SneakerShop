<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        
        $query = Order::with(['user']);
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%$search%")
                  ->orWhere('total_amount', 'like', "%$search%")
                  ->orWhereHas('user', function($subQuery) use ($search) {
                      $subQuery->where('name', 'like', "%$search%")
                              ->orWhere('email', 'like', "%$search%");
                  });
            });
        }
        
        $orders = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.orders.index', compact('orders', 'status'));
    }
    
    public function show(Order $order)
    {
        $order->load(['user', 'orderItems.product', 'orderItems.productVariant', 'coupon']);
        
        return view('admin.orders.show', compact('order'));
    }
    
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,declined,cancelled',
        ]);
        
        $oldStatus = $order->status;
        $order->status = $validated['status'];
        $order->save();
        
        // Nếu đơn hàng được hoàn thành và có sử dụng coupon, tăng số lần sử dụng của coupon
        if ($order->status == 'completed' && $order->coupon_id) {
            $coupon = Coupon::find($order->coupon_id);
            if ($coupon) {
                $coupon->used_times += 1;
                $coupon->save();
            }
        }
        
        return redirect()->back()
            ->with('success', "Trạng thái đơn hàng đã được cập nhật từ '$oldStatus' sang '{$order->status}'.");
    }
    
    public function destroy(Order $order)
    {
        // Consider whether you want to actually delete orders or just mark them
        // In many systems, orders are never truly deleted
        
        // Optional: Only allow deleting cancelled orders
        if ($order->status !== 'cancelled') {
            return redirect()->back()
                ->with('error', 'Chỉ có thể xóa đơn hàng đã hủy.');
        }
        
        $order->delete();
        
        return redirect()->route('admin.orders.index')
            ->with('success', 'Đơn hàng đã được xóa thành công.');
    }
    
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|json',
            'action' => 'required|in:pending,processing,completed,declined,cancelled,delete',
        ]);
        
        $ids = json_decode($validated['ids'], true);
        $action = $validated['action'];
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Không có đơn hàng nào được chọn.');
        }
        
        if ($action === 'delete') {
            // Only delete cancelled orders
            $deletedCount = Order::whereIn('id', $ids)->where('status', 'cancelled')->delete();
            
            return redirect()->back()
                ->with('success', "Đã xóa $deletedCount đơn hàng thành công.");
        } else {
            // Update status
            $updatedCount = Order::whereIn('id', $ids)->update(['status' => $action]);
            
            // Nếu chuyển trạng thái sang hoàn thành, cập nhật số lượng sử dụng coupon
            if ($action == 'completed') {
                $orders = Order::whereIn('id', $ids)->whereNotNull('coupon_id')->get();
                foreach ($orders as $order) {
                    $coupon = Coupon::find($order->coupon_id);
                    if ($coupon) {
                        $coupon->used_times += 1;
                        $coupon->save();
                    }
                }
            }
            
            return redirect()->back()
                ->with('success', "Đã cập nhật trạng thái cho $updatedCount đơn hàng thành công.");
        }
    }
    
    // Frontend methods for user order history
    public function userIndex()
    {
        $orders = Order::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('frontend.orders.index', compact('orders'));
    }
    
    public function userShow(Order $order)
    {
        // Check if order belongs to current user
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền xem đơn hàng này.');
        }
        
        $order->load(['orderItems.product']);
        
        return view('frontend.orders.show', compact('order'));
    }
}