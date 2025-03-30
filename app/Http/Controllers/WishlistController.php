<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Toggle product in wishlist (add if not exists, remove if exists)
     */
    public function toggle(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vui lòng đăng nhập để sử dụng tính năng này',
                'redirect' => route('login')
            ], 401);
        }
        
        $user = Auth::user();
        $productId = $request->input('product_id');
        
        // Check if product exists
        $product = Product::find($productId);
        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sản phẩm không tồn tại'
            ], 404);
        }
        
        // Check if product already in wishlist
        $wishlistItem = Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();
        
        if ($wishlistItem) {
            // Remove from wishlist
            $wishlistItem->delete();
            
            return response()->json([
                'status' => 'success',
                'action' => 'removed',
                'message' => 'Đã xóa sản phẩm khỏi danh sách yêu thích'
            ]);
        } else {
            // Add to wishlist
            Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $productId
            ]);
            
            return response()->json([
                'status' => 'success',
                'action' => 'added',
                'message' => 'Đã thêm sản phẩm vào danh sách yêu thích'
            ]);
        }
    }
    
    /**
     * Display the user's wishlist
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để xem danh sách yêu thích');
        }
        
        $wishlistItems = Auth::user()->wishlists()->with('product')->get();
        
        return view('frontend.wishlist', [
            'wishlistItems' => $wishlistItems
        ]);
    }
    
    /**
     * Remove a product from wishlist
     */
    public function remove($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $wishlistItem = Wishlist::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();
            
        if ($wishlistItem) {
            $wishlistItem->delete();
            return redirect()->back()->with('success', 'Đã xóa sản phẩm khỏi danh sách yêu thích');
        }
        
        return redirect()->back()->with('error', 'Không tìm thấy sản phẩm trong danh sách yêu thích');
    }
}
