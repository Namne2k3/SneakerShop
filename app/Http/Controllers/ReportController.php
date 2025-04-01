<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Hiển thị báo cáo doanh thu
     */
    public function revenue(Request $request)
    {
        // Khởi tạo mặc định lọc theo tháng hiện tại
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $groupBy = $request->input('group_by', 'day');
        $paymentMethod = $request->input('payment_method', 'all');

        // Chuyển đổi sang dạng Carbon để dễ xử lý
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        // Tùy chỉnh format tùy thuộc vào groupBy
        $dateFormat = '%Y-%m-%d'; // Default: group by day
        $displayFormat = 'd/m/Y';
        
        if ($groupBy == 'month') {
            $dateFormat = '%Y-%m';
            $displayFormat = 'm/Y';
        } elseif ($groupBy == 'year') {
            $dateFormat = '%Y';
            $displayFormat = 'Y';
        } elseif ($groupBy == 'week') {
            // MySQL uses week numbers (1-53)
            $dateFormat = '%x-%v'; // Year and week number
            $displayFormat = '\T\u\ầ\n W, Y';
        }

        // Khởi tạo query
        $query = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate]);
        
        // Lọc theo phương thức thanh toán
        if ($paymentMethod !== 'all') {
            $query->where('payment_method', $paymentMethod);
        }
        
        // Group theo thời gian và tính tổng doanh thu
        $revenueData = $query->select(
                DB::raw("DATE_FORMAT(created_at, '$dateFormat') as date"),
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('COUNT(*) as total_orders')
            )
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '$dateFormat')"))
            ->orderBy('date')
            ->get();
            
        // Tính tổng doanh thu
        $totalRevenue = $revenueData->sum('total_revenue');
        $totalOrders = $revenueData->sum('total_orders');
        
        // Doanh thu theo phương thức thanh toán
        $paymentMethodsData = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('payment_method')
            ->get();
            
        // Format tên phương thức thanh toán cho dễ hiểu
        $paymentMethods = [
            'cod' => 'Tiền mặt khi nhận hàng',
            'bank_transfer' => 'Chuyển khoản ngân hàng',
            'momo' => 'Ví Momo'
        ];
        
        return view('admin.reports.revenue', compact(
            'revenueData', 
            'totalRevenue', 
            'totalOrders', 
            'paymentMethodsData',
            'paymentMethods',
            'startDate', 
            'endDate',
            'groupBy',
            'paymentMethod',
            'displayFormat'
        ));
    }

    /**
     * Hiển thị báo cáo sản phẩm bán chạy
     */
    public function bestSelling(Request $request)
    {
        // Khởi tạo mặc định lọc theo tháng hiện tại
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $categoryId = $request->input('category_id', 'all');
        $brandId = $request->input('brand_id', 'all');
        $limit = $request->input('limit', 20);

        // Chuyển đổi sang dạng Carbon để dễ xử lý
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        // Khởi tạo query
        $query = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [$startDate, $endDate]);
        
        // Lọc theo category nếu có
        if ($categoryId !== 'all') {
            $query->join('product_category', 'products.id', '=', 'product_category.product_id')
                ->where('product_category.category_id', $categoryId);
        }
        
        // Lọc theo brand nếu có
        if ($brandId !== 'all') {
            $query->where('products.brand_id', $brandId);
        }
        
        // Lấy dữ liệu sản phẩm bán chạy
        $products = $query->select(
                'products.id',
                'products.name as product_name',
                'brands.name as brand_name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'brands.name')
            ->orderBy('total_quantity', 'desc')
            ->limit($limit)
            ->get();
            
        // Lấy danh sách categories và brands để hiển thị trong form lọc
        $categories = Category::whereNull('parent_id')->with('children')->get();
        $brands = Brand::all();

        // Tính tổng số lượng và doanh thu
        $totalQuantity = $products->sum('total_quantity');
        $totalRevenue = $products->sum('total_revenue');
        
        return view('admin.reports.best_selling', compact(
            'products', 
            'totalQuantity', 
            'totalRevenue',
            'categories', 
            'brands',
            'startDate',
            'endDate',
            'categoryId',
            'brandId',
            'limit'
        ));
    }
}
