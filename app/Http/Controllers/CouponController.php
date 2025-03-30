<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    /**
     * Display a listing of the coupons.
     */
    public function index(Request $request)
    {
        $query = Coupon::query();
        
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }
        
        if ($request->has('status')) {
            $status = $request->get('status');
            if ($status === 'active') {
                $query->where('active', true);
            } elseif ($status === 'inactive') {
                $query->where('active', false);
            }
        }
        
        $coupons = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Show the form for creating a new coupon.
     */
    public function create()
    {
        return view('admin.coupons.create');
    }

    /**
     * Store a newly created coupon in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:coupons,code|max:50',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string|max:255',
            'usage_limit' => 'nullable|integer|min:0',
            'active' => 'boolean'
        ]);
        
        // Convert date inputs to proper format
        $validated['start_date'] = date('Y-m-d H:i:s', strtotime($validated['start_date']));
        $validated['end_date'] = date('Y-m-d H:i:s', strtotime($validated['end_date']));
        $validated['active'] = $request->has('active');
        
        // Create the coupon
        Coupon::create($validated);
        
        return redirect()->route('admin.coupons.index')
            ->with('success', 'Mã giảm giá đã được tạo thành công!');
    }

    /**
     * Show the form for editing the specified coupon.
     */
    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    /**
     * Update the specified coupon in storage.
     */
    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string|max:255',
            'usage_limit' => 'nullable|integer|min:0',
            'active' => 'boolean'
        ]);
        
        // Convert date inputs to proper format
        $validated['start_date'] = date('Y-m-d H:i:s', strtotime($validated['start_date']));
        $validated['end_date'] = date('Y-m-d H:i:s', strtotime($validated['end_date']));
        $validated['active'] = $request->has('active');
        
        // Update the coupon
        $coupon->update($validated);
        
        return redirect()->route('admin.coupons.index')
            ->with('success', 'Mã giảm giá đã được cập nhật thành công!');
    }

    /**
     * Remove the specified coupon from storage.
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        
        return redirect()->route('admin.coupons.index')
            ->with('success', 'Mã giảm giá đã được xóa thành công!');
    }
    
    /**
     * Validate a coupon code (for frontend use).
     */
    public function validateCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50',
            'total' => 'required|numeric|min:0'
        ]);
        
        $code = $request->input('code');
        $orderTotal = $request->input('total');
        
        // Find the coupon
        $coupon = Coupon::where('code', $code)
            ->where('active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
        
        if (!$coupon) {
            return response()->json([
                'valid' => false,
                'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn.'
            ]);
        }
        
        // Check minimum order amount
        if ($coupon->min_order_amount && $orderTotal < $coupon->min_order_amount) {
            return response()->json([
                'valid' => false,
                'message' => 'Giá trị đơn hàng chưa đạt tối thiểu ' . number_format($coupon->min_order_amount) . 'đ để sử dụng mã giảm giá này.'
            ]);
        }
        
        // Check usage limit
        if ($coupon->usage_limit !== null && $coupon->usage_count >= $coupon->usage_limit) {
            return response()->json([
                'valid' => false,
                'message' => 'Mã giảm giá này đã hết lượt sử dụng.'
            ]);
        }
        
        // Calculate discount
        $discount = 0;
        
        if ($coupon->type === 'fixed') {
            $discount = $coupon->value;
        } else { // percentage
            $discount = ($coupon->value / 100) * $orderTotal;
            
            // Apply maximum discount if specified
            if ($coupon->max_discount_amount && $discount > $coupon->max_discount_amount) {
                $discount = $coupon->max_discount_amount;
            }
        }
        
        return response()->json([
            'valid' => true,
            'message' => 'Mã giảm giá hợp lệ!',
            'discount' => $discount,
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'description' => $coupon->description
            ]
        ]);
    }

    /**
     * Generate a random coupon code.
     */
    public function generateCode()
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (Coupon::where('code', $code)->exists());
        
        return response()->json([
            'code' => $code
        ]);
    }
}