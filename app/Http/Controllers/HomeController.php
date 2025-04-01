<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\Coupon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // Lấy 8 sản phẩm nổi bật
        $featuredProducts = Product::where('active', 1)
            ->where('featured', 1)
            ->with(['images' => function($query) {
                $query->where('is_primary', 1);
            }])
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();
        
        // Lấy tất cả danh mục
        $categories = Category::all();
        
        return view('frontend.home', compact('featuredProducts', 'categories'));
    }
    
    public function contact()
    {
        return view('frontend.contact');
    }
    
    public function sendContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);
        
        // Trong tương lai, bạn có thể thêm chức năng gửi email hoặc lưu vào database
        // Mail::to('contact@sneakershop.vn')->send(new ContactFormMail($validated));
        
        return redirect()->route('contact')->with('success', 'Cảm ơn bạn đã liên hệ với chúng tôi! Chúng tôi sẽ phản hồi trong thời gian sớm nhất.');
    }
    
    public function cart()
    {
        // Lấy giỏ hàng từ session
        $cart = session()->get('cart', []);
        
        return view('frontend.cart', compact('cart'));
    }
    
    public function addToCart(Request $request)
    {
        $product = Product::findOrFail($request->product_id);
        
        $cart = session()->get('cart', []);
        
        $cartItemId = $request->product_id . '-' . ($request->variant_id ?? 0);
        
        // Nếu đã có sản phẩm trong giỏ hàng thì tăng số lượng
        if(isset($cart[$cartItemId])) {
            $cart[$cartItemId]['quantity'] += $request->quantity;
        } else {
            // Nếu chưa có sản phẩm trong giỏ hàng thì thêm mới
            $price = $product->sale_price ?? $product->price;
            
            // Lấy hình ảnh chính của sản phẩm
            $image = $product->images()->where('is_primary', 1)->first();
            
            $cart[$cartItemId] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'price' => $price,
                'quantity' => $request->quantity,
                'image' => $image ? $image->image_path : null,
            ];
        }
        
        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Sản phẩm đã được thêm vào giỏ hàng!');
    }
    
    public function updateCart(Request $request)
    {
        if($request->id && $request->quantity) {
            $cart = session()->get('cart');
            $cart[$request->id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Giỏ hàng đã được cập nhật!');
        }
        
        if($request->id && $request->remove) {
            $cart = session()->get('cart');
            unset($cart[$request->id]);
            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Sản phẩm đã được xóa khỏi giỏ hàng!');
        }
    }
    
    public function checkout()
    {
        return view('frontend.checkout');
    }
    
    public function profile()
    {
        $user = Auth::user();
        return view('frontend.profile', compact('user'));
    }
    
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;
        $user->address = $request->address;
        
        // Thay đổi mật khẩu nếu có
        if($request->filled('current_password') && $request->filled('password')) {
            if(!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng']);
            }
            $user->password = Hash::make($request->password);
        }
        
        $user->save();
        
        return back()->with('success', 'Thông tin cá nhân đã được cập nhật!');
    }
    
    public function orders()
    {
        $orders = Auth::user()->orders()->orderBy('created_at', 'desc')->paginate(10);
        return view('frontend.orders', compact('orders'));
    }
    
    public function cancelOrder(Request $request, Order $order)
    {
        // Check if the order belongs to the authenticated user
        if($order->user_id != Auth::id()) {
            return redirect()->route('orders')->with('error', 'Bạn không có quyền hủy đơn hàng này!');
        }
        
        // Check if order status is 'pending'
        if($order->status !== 'pending') {
            return redirect()->route('orders')->with('error', 'Chỉ có thể hủy đơn hàng chưa được xử lý!');
        }
        
        DB::beginTransaction();
        try {
            // Update order status to 'cancelled'
            $order->status = 'cancelled';
            if($request->has('cancel_reason')) {
                $order->notes = ($order->notes ? $order->notes . "\n\n" : '') . "Lý do hủy: " . $request->cancel_reason;
            }
            $order->save();
            
            // Return items to inventory
            foreach($order->orderItems as $item) {
                if($item->product_variant_id) {
                    $variant = ProductVariant::find($item->product_variant_id);
                    if($variant) {
                        $variant->stock += $item->quantity;
                        $variant->save();
                    }
                }
            }
            
            DB::commit();
            return redirect()->route('orders')->with('success', 'Đơn hàng đã được hủy thành công!');
        } catch(\Exception $e) {
            DB::rollBack();
            return redirect()->route('orders')->with('error', 'Đã xảy ra lỗi khi hủy đơn hàng: ' . $e->getMessage());
        }
    }

    public function placeOrder(Request $request)
    {
        // Validate request data
        $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_email' => 'required|email|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:255',
            'shipping_city' => 'required|string|max:100',
            'payment_method' => 'required|in:cod,bank_transfer,momo',
            'total_amount' => 'required|numeric|min:0',
            'agree' => 'required|accepted',
            'notes' => 'nullable|string',
        ]);

        // Check if cart exists and has items
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Giỏ hàng của bạn đang trống');
        }

        // Start DB transaction
        DB::beginTransaction();

        try {
            // Create new order
            $order = new Order();
            $order->order_number = 'ORD-' . strtoupper(Str::random(10));
            $order->user_id = Auth::id();
            $order->status = 'pending';
            $order->total_amount = $request->total_amount;
            $order->payment_method = $request->payment_method;
            $order->payment_status = ($request->payment_method == 'cod') ? 'pending' : 'pending';
            $order->shipping_name = $request->shipping_name;
            $order->shipping_email = $request->shipping_email;
            $order->shipping_phone = $request->shipping_phone;
            $order->shipping_address = $request->shipping_address;
            $order->shipping_city = $request->shipping_city;
            $order->notes = $request->notes;
            
            // Apply coupon if exists
            if (session()->has('coupon')) {
                $coupon = session('coupon');
                $couponModel = Coupon::where('code', $coupon['code'])->first();
                
                if ($couponModel) {
                    $order->coupon_id = $couponModel->id;
                    $order->discount_amount = $coupon['type'] == 'fixed' 
                        ? $coupon['value'] 
                        : ($request->total_amount * $coupon['value'] / 100);
                }
            }
            
            $order->save();
            
            // Create order items
            foreach ($cart as $id => $item) {
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $item['product_id'];
                
                // Check if it's a variant
                if (isset($item['variant_id']) && $item['variant_id']) {
                    $orderItem->product_variant_id = $item['variant_id'];
                    
                    // Update stock for variant
                    $variant = ProductVariant::find($item['variant_id']);
                    if ($variant) {
                        $variant->stock = $variant->stock - $item['quantity'];
                        $variant->save();
                    }
                }
                
                $orderItem->quantity = $item['quantity'];
                $orderItem->price = $item['price'];
                $orderItem->subtotal = $item['price'] * $item['quantity'];
                $orderItem->save();
            }
            
            // Commit transaction
            DB::commit();
            
            // Clear cart and coupon session
            session()->forget(['cart', 'coupon']);
            
            // Redirect to success page
            return redirect()->route('orders')->with('success', 'Đặt hàng thành công. Cảm ơn bạn đã mua hàng!');
            
        } catch (\Exception $e) {
            // Rollback in case of error
            DB::rollBack();
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi đặt hàng: ' . $e->getMessage());
        }
    }
    
    // Admin methods
    public function adminDashboard()
    {
        // Thống kê cơ bản
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $recentOrders = Order::with('user')->orderBy('created_at', 'desc')->take(5)->get();
        
        // Doanh thu theo tháng (6 tháng gần nhất)
        $monthlyRevenue = [];
        $monthLabels = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthName = $month->format('M Y'); // Format: Jan 2023
            $monthLabels[] = $monthName;
            
            // Tính tổng doanh thu của đơn hàng hoàn thành trong tháng
            $revenue = Order::where('status', 'completed')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total_amount');
            
            // Thêm dữ liệu mẫu nếu doanh thu = 0 (chỉ để demo biểu đồ)
            if ($revenue == 0) {
                // Tạo dữ liệu mẫu từ 1.000.000 đến 5.000.000
                $revenue = rand(1000000, 5000000);
            }
                
            $monthlyRevenue[] = $revenue;
        }
        
        // Sản phẩm bán chạy nhất (top 5)
        $bestSellingProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_quantity', 'desc')
            ->take(5)
            ->get();
            
        // Nếu không có sản phẩm bán chạy (để demo)
        if ($bestSellingProducts->isEmpty()) {
            // Lấy 5 sản phẩm bất kỳ
            $products = Product::inRandomOrder()->take(5)->get(['id', 'name']);
            $bestSellingProducts = collect();
            
            foreach ($products as $product) {
                $bestSellingProducts->push((object)[
                    'id' => $product->id,
                    'name' => $product->name,
                    'total_quantity' => rand(10, 100),
                    'total_revenue' => rand(1000000, 5000000)
                ]);
            }
        }
            
        return view('admin.dashboard', compact(
            'totalProducts',
            'totalCategories',
            'recentOrders',
            'monthlyRevenue',
            'monthLabels',
            'bestSellingProducts'
        ));
    }
}
