@extends('layouts.admin')

@section('title', 'Báo cáo sản phẩm bán chạy - Admin Sneaker Shop')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Báo cáo sản phẩm bán chạy</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Sản phẩm bán chạy</li>
    </ol>

    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i>
            Bộ lọc báo cáo
        </div>
        <div class="card-body">
            <form action="{{ route('admin.reports.best-selling') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Từ ngày</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">Đến ngày</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label for="category_id" class="form-label">Danh mục</label>
                    <select class="form-select" id="category_id" name="category_id">
                        <option value="all" {{ $categoryId == 'all' ? 'selected' : '' }}>Tất cả danh mục</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @foreach($category->children as $child)
                                <option value="{{ $child->id }}" {{ $categoryId == $child->id ? 'selected' : '' }}>&nbsp;&nbsp;- {{ $child->name }}</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="brand_id" class="form-label">Thương hiệu</label>
                    <select class="form-select" id="brand_id" name="brand_id">
                        <option value="all" {{ $brandId == 'all' ? 'selected' : '' }}>Tất cả thương hiệu</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ $brandId == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="limit" class="form-label">Số lượng hiển thị</label>
                    <select class="form-select" id="limit" name="limit">
                        <option value="10" {{ $limit == 10 ? 'selected' : '' }}>10 sản phẩm</option>
                        <option value="20" {{ $limit == 20 ? 'selected' : '' }}>20 sản phẩm</option>
                        <option value="50" {{ $limit == 50 ? 'selected' : '' }}>50 sản phẩm</option>
                        <option value="100" {{ $limit == 100 ? 'selected' : '' }}>100 sản phẩm</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Áp dụng</button>
                    <a href="{{ route('admin.reports.best-selling') }}" class="btn btn-secondary">Đặt lại</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-xl-6 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ number_format($totalRevenue) }} đ</h4>
                            <div>Tổng doanh thu</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-money-bill fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ number_format($totalQuantity) }}</h4>
                            <div>Tổng số lượng bán ra</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Best Selling Products Chart -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-chart-bar me-1"></i>
            Biểu đồ sản phẩm bán chạy từ {{ $startDate->format('d/m/Y') }} đến {{ $endDate->format('d/m/Y') }}
        </div>
        <div class="card-body">
            <canvas id="bestSellingChart" width="100%" height="40"></canvas>
        </div>
    </div>

    <!-- Pie Chart by Revenue -->
    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>
                    Tỷ lệ doanh thu theo sản phẩm (Top 10)
                </div>
                <div class="card-body">
                    <canvas id="revenueShareChart" width="100%" height="50"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>
                    Tỷ lệ số lượng bán ra theo sản phẩm (Top 10)
                </div>
                <div class="card-body">
                    <canvas id="quantityShareChart" width="100%" height="50"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Products Table -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Danh sách sản phẩm bán chạy
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover datatable-table">
                    <thead>
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 40%">Sản phẩm</th>
                            <th style="width: 15%">Thương hiệu</th>
                            <th style="width: 15%">Số lượng</th>
                            <th style="width: 15%">Doanh thu</th>
                            <th style="width: 10%">Tỷ lệ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $index => $product)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <a href="{{ route('admin.products.show', $product->id) }}" target="_blank">
                                        {{ $product->product_name }}
                                    </a>
                                </td>
                                <td>{{ $product->brand_name }}</td>
                                <td>{{ number_format($product->total_quantity) }}</td>
                                <td>{{ number_format($product->total_revenue) }} đ</td>
                                <td>{{ number_format(($product->total_revenue / ($totalRevenue ?: 1)) * 100, 1) }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <td colspan="3">Tổng cộng</td>
                            <td>{{ number_format($totalQuantity) }}</td>
                            <td>{{ number_format($totalRevenue) }} đ</td>
                            <td>100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Best Selling Products Chart
    var ctx1 = document.getElementById('bestSellingChart').getContext('2d');
    var productNames = [];
    var productQuantities = [];
    var productRevenues = [];
    var bgColors = [];
    
    // Generate colors for each product
    function generateColors(count) {
        var colors = [];
        for (var i = 0; i < count; i++) {
            var hue = (i * 30) % 360;
            colors.push('hsla(' + hue + ', 70%, 60%, 0.7)');
        }
        return colors;
    }

    @foreach($products->take(10) as $product)
        productNames.push('{{ Str::limit($product->product_name, 20) }}');
        productQuantities.push({{ $product->total_quantity }});
        productRevenues.push({{ $product->total_revenue }});
    @endforeach
    
    bgColors = generateColors(productNames.length);
    
    var bestSellingChart = new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: productNames,
            datasets: [
                {
                    label: 'Số lượng bán ra',
                    data: productQuantities,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    yAxisID: 'y-axis-1'
                },
                {
                    label: 'Doanh thu (VNĐ)',
                    data: productRevenues,
                    type: 'line',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgb(255, 99, 132)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgb(255, 99, 132)',
                    yAxisID: 'y-axis-2'
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    ticks: {
                        autoSkip: false,
                        maxRotation: 45,
                        minRotation: 45
                    }
                },
                'y-axis-1': {
                    type: 'linear',
                    position: 'left',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Số lượng'
                    }
                },
                'y-axis-2': {
                    type: 'linear',
                    position: 'right',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Doanh thu (VNĐ)'
                    },
                    grid: {
                        drawOnChartArea: false
                    },
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) {
                                return (value / 1000000) + 'M';
                            } else if (value >= 1000) {
                                return (value / 1000) + 'K';
                            }
                            return value;
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.dataset.label.includes('Doanh thu')) {
                                label += new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(context.raw);
                            } else {
                                label += new Intl.NumberFormat('vi-VN').format(context.raw);
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });

    // Revenue Share Pie Chart
    var ctx2 = document.getElementById('revenueShareChart').getContext('2d');
    var revenueShareChart = new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: productNames,
            datasets: [{
                label: 'Doanh thu',
                data: productRevenues,
                backgroundColor: bgColors,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            var value = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(context.raw);
                            var percentage = new Intl.NumberFormat('vi-VN', { style: 'percent', minimumFractionDigits: 1, maximumFractionDigits: 1 }).format(context.raw / {{ $totalRevenue ?: 1 }});
                            label += value + ' (' + percentage + ')';
                            return label;
                        }
                    }
                }
            }
        }
    });

    // Quantity Share Pie Chart
    var ctx3 = document.getElementById('quantityShareChart').getContext('2d');
    var quantityShareChart = new Chart(ctx3, {
        type: 'pie',
        data: {
            labels: productNames,
            datasets: [{
                label: 'Số lượng',
                data: productQuantities,
                backgroundColor: bgColors,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            var quantity = new Intl.NumberFormat('vi-VN').format(context.raw);
                            var percentage = new Intl.NumberFormat('vi-VN', { style: 'percent', minimumFractionDigits: 1, maximumFractionDigits: 1 }).format(context.raw / {{ $totalQuantity ?: 1 }});
                            label += quantity + ' sản phẩm (' + percentage + ')';
                            return label;
                        }
                    }
                }
            }
        }
    });

    // Initialize datatable for the products table
    document.addEventListener('DOMContentLoaded', function () {
        const dataTable = document.querySelector('.datatable-table');
        if (dataTable) {
            new simpleDatatables.DataTable(dataTable, {
                searchable: true,
                fixedHeight: true,
                paging: true,
                perPage: 20,
                ordering: true
            });
        }
    });
</script>
@endpush
@endsection