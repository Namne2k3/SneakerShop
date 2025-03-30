<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
    
    // Admin methods
    public function adminDashboard()
    {
        // Thống kê cơ bản
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $recentOrders = Order::with('user')->orderBy('created_at', 'desc')->take(5)->get();
        
        return view('admin.dashboard', compact('totalProducts', 'totalCategories', 'recentOrders'));
    }
}
