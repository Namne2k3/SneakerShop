<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    // Admin methods
    public function adminIndex(Request $request)
    {
        // Khởi tạo query với eager loading các relationships
        $query = Product::with(['categories', 'brand', 'variants', 'images']);
        
       
        // Filter by brand if specified
        if ($request->filled('brand')) {
            $brandId = $request->input('brand');
            
            $query->where('brand_id', $brandId);
        }
        
        // Filter by category if specified
        if ($request->filled('category')) {
            $categoryId = $request->input('category');
            
            $query->whereHas('categories', function($q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            });
        }
        
        // Filter by active status if specified
        if ($request->filled('status')) {
            $status = (int) $request->input('status');
           
            $query->where('active', $status);
        }
        
        // Search by name, slug or description
        if ($request->filled('search')) {
            $search = $request->input('search');
           
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Lấy danh sách sản phẩm với phân trang
        $products = $query->orderBy('created_at', 'desc')->paginate(10);
        
        
        
        $categories = Category::all();
        $brands = Brand::all();
        
        return view('admin.products.index', compact('products', 'categories', 'brands'));
    }

    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'features' => 'nullable|string',
            'brand_id' => 'required|exists:brands,id',
            'featured' => 'boolean',
            'active' => 'boolean',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'variants' => 'nullable|array',
            'variants.*.size' => 'required|string',
            'variants.*.color' => 'required|string',
            'variants.*.sku' => 'required|string|unique:product_variants,sku',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.additional_price' => 'required|numeric|min:0',
        ]);

        // Generate slug
        $validated['slug'] = Str::slug($validated['name']);
        
        // Create product
        $product = Product::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'sale_price' => $validated['sale_price'] ?? null,
            'features' => $validated['features'] ?? null,
            'brand_id' => $validated['brand_id'],
            'featured' => $validated['featured'] ?? false,
            'active' => $validated['active'] ?? true,
        ]);

        // Attach categories
        $product->categories()->attach($validated['categories']);

        // Handle product images
        if ($request->hasFile('images')) {
            $isPrimary = true;
            foreach ($request->file('images') as $key => $image) {
                $imagePath = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imagePath,
                    'is_primary' => $isPrimary,
                    'sort_order' => $key,
                ]);
                $isPrimary = false;
            }
        }

        // Handle variants
        if (isset($validated['variants'])) {
            foreach ($validated['variants'] as $variantData) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'size' => $variantData['size'],
                    'color' => $variantData['color'],
                    'sku' => $variantData['sku'],
                    'stock' => $variantData['stock'],
                    'additional_price' => $variantData['additional_price'],
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Sản phẩm đã được tạo thành công!');
    }

    public function adminShow(Product $product)
    {
        $product->load(['categories', 'brand', 'images', 'variants']);
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load(['categories', 'brand', 'images', 'variants']);
        $categories = Category::all();
        $brands = Brand::all();
        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'features' => 'nullable|string',
            'brand_id' => 'required|exists:brands,id',
            'featured' => 'boolean',
            'active' => 'boolean',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'new_images' => 'nullable|array',
            'new_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'variants' => 'nullable|array',
        ]);

        // Update slug if name changed
        if ($product->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Update product
        $product->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? $product->slug,
            'description' => $validated['description'],
            'price' => $validated['price'],
            'sale_price' => $validated['sale_price'] ?? null,
            'features' => $validated['features'] ?? null,
            'brand_id' => $validated['brand_id'],
            'featured' => $validated['featured'] ?? false,
            'active' => $validated['active'] ?? true,
        ]);

        // Sync categories
        $product->categories()->sync($validated['categories']);

        // Handle product images
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $key => $image) {
                $imagePath = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imagePath,
                    'is_primary' => false,
                    'sort_order' => $product->images->count() + $key,
                ]);
            }
        }

        // Handle remove images
        if ($request->has('remove_images')) {
            foreach ($request->input('remove_images') as $imageId) {
                $image = ProductImage::find($imageId);
                if ($image) {
                    // Delete image file
                    if (Storage::disk('public')->exists($image->image_path)) {
                        Storage::disk('public')->delete($image->image_path);
                    }
                    $image->delete();
                }
            }
        }

        // Handle update variants
        if (isset($validated['variants'])) {
            foreach ($validated['variants'] as $variantId => $variantData) {
                if (isset($variantData['id'])) {
                    // Existing variant
                    $variant = ProductVariant::find($variantData['id']);
                    if ($variant) {
                        $variant->update($variantData);
                    }
                } else {
                    // New variant
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'size' => $variantData['size'],
                        'color' => $variantData['color'],
                        'sku' => $variantData['sku'],
                        'stock' => $variantData['stock'],
                        'additional_price' => $variantData['additional_price'],
                    ]);
                }
            }
        }

        // Handle remove variants
        if ($request->has('remove_variants')) {
            ProductVariant::whereIn('id', $request->input('remove_variants'))->delete();
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Sản phẩm đã được cập nhật thành công!');
    }

    public function destroy(Product $product)
    {
        // Delete images
        foreach ($product->images as $image) {
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
        }
        
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Sản phẩm đã được xóa thành công!');
    }
    
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|json',
            'action' => 'required|string|in:active,inactive,delete',
        ]);
        
        $ids = json_decode($validated['ids']);
        $action = $validated['action'];
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Không có sản phẩm nào được chọn.');
        }
        
        switch ($action) {
            case 'active':
                Product::whereIn('id', $ids)->update(['active' => true]);
                $message = 'Đã hiển thị ' . count($ids) . ' sản phẩm thành công.';
                break;
                
            case 'inactive':
                Product::whereIn('id', $ids)->update(['active' => false]);
                $message = 'Đã ẩn ' . count($ids) . ' sản phẩm thành công.';
                break;
                
            case 'delete':
                // Find all products that need to be deleted
                $products = Product::whereIn('id', $ids)->get();
                
                foreach ($products as $product) {
                    // Delete images
                    foreach ($product->images as $image) {
                        if (Storage::disk('public')->exists($image->image_path)) {
                            Storage::disk('public')->delete($image->image_path);
                        }
                    }
                    
                    // Delete the product
                    $product->delete();
                }
                
                $message = 'Đã xóa ' . count($ids) . ' sản phẩm thành công.';
                break;
                
            default:
                return redirect()->back()->with('error', 'Thao tác không hợp lệ.');
        }
        
        return redirect()->back()->with('success', $message);
    }
    
    public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'active' => 'required|boolean',
        ]);
        
        $product = Product::findOrFail($validated['product_id']);
        $product->active = $validated['active'];
        $product->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Trạng thái sản phẩm đã được cập nhật.',
        ]);
    }
    
    // Frontend methods
    public function showHomePage()
    {
        $featuredProducts = Product::where('active', 1)
            ->where('featured', 1)
            ->with(['images' => function($query) {
                $query->where('is_primary', 1);
            }])
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();
            
        $categories = Category::all();
        
        return view('frontend.home', compact('featuredProducts', 'categories'));
    }
    
    public function showProductDetails($slug)
    {
        $product = Product::where('slug', $slug)
            ->with(['categories', 'brand', 'images', 'variants', 'reviews.user'])
            ->firstOrFail();
            
        $categoryIds = $product->categories->pluck('id');
        $relatedProducts = Product::whereHas('categories', function($query) use ($categoryIds) {
                $query->whereIn('category_id', $categoryIds);
            })
            ->where('id', '!=', $product->id)
            ->where('active', 1)
            ->take(4)
            ->get();
            
        return view('frontend.product_details', compact('product', 'relatedProducts'));
    }
    
    public function showProductsByCategory($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $products = $category->products()
            ->where('active', 1)
            ->with(['images' => function($query) {
                $query->where('is_primary', 1);
            }])
            ->paginate(12);
            
        return view('frontend.category_products', compact('category', 'products'));
    }

    public function index()
    {
        $products = Product::where('active', 1)
            ->with(['images' => function($query) {
                $query->where('is_primary', 1);
            }])
            ->paginate(12);
            
        $categories = Category::all();
        
        return view('frontend.products', compact('products', 'categories'));
    }
    
    public function show($slug)
    {
        $product = Product::where('slug', $slug)->with(['categories', 'brand', 'images', 'variants', 'reviews.user'])->firstOrFail();
    
        // Get related products
        $relatedProducts = Product::whereHas('categories', function($query) use ($product) {
            $query->whereIn('categories.id', $product->categories->pluck('id'));
        })->where('id', '!=', $product->id)
            ->with(['images'])
            ->take(4)
            ->get();
        
        // Check if product is in user's wishlist
        $isInWishlist = false;
        if (auth()->check()) {
            $isInWishlist = Wishlist::where('user_id', auth()->id())
                ->where('product_id', $product->id)
                ->exists();
        }
        
        return view('frontend.product_details', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'isInWishlist' => $isInWishlist
        ]);
    }
    
    public function search(Request $request)
    {
        $query = $request->input('query');
        
        $products = Product::where('active', 1)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%$query%")
                  ->orWhere('description', 'like', "%$query%");
            })
            ->with(['images' => function($q) {
                $q->where('is_primary', 1);
            }])
            ->paginate(12);
            
        return view('frontend.search_results', compact('products', 'query'));
    }
}
