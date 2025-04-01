@extends('layouts.admin')

@section('title', 'Báo cáo doanh thu - Admin Sneaker Shop')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Báo cáo doanh thu</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Báo cáo doanh thu</li>
    </ol>

    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i>
            Bộ lọc báo cáo
        </div>
        <div class="card-body">
            <form action="{{ route('admin.reports.revenue') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Từ ngày</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">Đến ngày</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label for="group_by" class="form-label">Nhóm theo</label>
                    <select class="form-select" id="group_by" name="group_by">
                        <option value="day" {{ $groupBy == 'day' ? 'selected' : '' }}>Ngày</option>
                        <option value="week" {{ $groupBy == 'week' ? 'selected' : '' }}>Tuần</option>
                        <option value="month" {{ $groupBy == 'month' ? 'selected' : '' }}>Tháng</option>
                        <option value="year" {{ $groupBy == 'year' ? 'selected' : '' }}>Năm</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="payment_method" class="form-label">Phương thức thanh toán</label>
                    <select class="form-select" id="payment_method" name="payment_method">
                        <option value="all" {{ $paymentMethod == 'all' ? 'selected' : '' }}>Tất cả</option>
                        @foreach($paymentMethods as $key => $name)
                            <option value="{{ $key }}" {{ $paymentMethod == $key ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Áp dụng</button>
                    <a href="{{ route('admin.reports.revenue') }}" class="btn btn-secondary">Đặt lại</a>
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
                            <h4 class="mb-0">{{ $totalOrders }}</h4>
                            <div>Tổng đơn hàng</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Chart -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-chart-bar me-1"></i>
            Biểu đồ doanh thu từ {{ $startDate->format('d/m/Y') }} đến {{ $endDate->format('d/m/Y') }}
        </div>
        <div class="card-body">
            <canvas id="revenueChart" width="100%" height="40"></canvas>
        </div>
    </div>

    <!-- Payment Method Chart -->
    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>
                    Doanh thu theo phương thức thanh toán
                </div>
                <div class="card-body">
                    <canvas id="paymentMethodChart" width="100%" height="50"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Doanh thu theo phương thức thanh toán
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Phương thức thanh toán</th>
                                <th>Số đơn hàng</th>
                                <th>Doanh thu</th>
                                <th>Tỷ lệ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($paymentMethodsData as $data)
                                <tr>
                                    <td>{{ $paymentMethods[$data->payment_method] ?? $data->payment_method }}</td>
                                    <td>{{ number_format($data->count) }}</td>
                                    <td>{{ number_format($data->total) }} đ</td>
                                    <td>{{ number_format(($data->total / $totalRevenue) * 100, 1) }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Revenue Table -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Doanh thu chi tiết
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover datatable-table">
                <thead>
                    <tr>
                        <th>Thời gian</th>
                        <th>Số đơn hàng</th>
                        <th>Doanh thu</th>
                        <th>Doanh thu trung bình/đơn</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($revenueData as $data)
                        <tr>
                            <td>
                                @if($groupBy == 'week')
                                    @php
                                        list($year, $week) = explode('-', $data->date);
                                        $date = Carbon\Carbon::now()->setISODate($year, $week)->startOfWeek();
                                        echo 'Tuần ' . $week . ', ' . $year . ' (' . $date->format('d/m/Y') . ' - ' . $date->addDays(6)->format('d/m/Y') . ')';
                                    @endphp
                                @elseif($groupBy == 'month')
                                    @php
                                        $dateObj = Carbon\Carbon::createFromFormat('Y-m', $data->date);
                                        echo $dateObj->format('m/Y');
                                    @endphp
                                @elseif($groupBy == 'year')
                                    {{ $data->date }}
                                @else
                                    @php
                                        $dateObj = Carbon\Carbon::createFromFormat('Y-m-d', $data->date);
                                        echo $dateObj->format('d/m/Y');
                                    @endphp
                                @endif
                            </td>
                            <td>{{ number_format($data->total_orders) }}</td>
                            <td>{{ number_format($data->total_revenue) }} đ</td>
                            <td>{{ number_format($data->total_revenue / $data->total_orders) }} đ</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="fw-bold">
                        <td>Tổng cộng</td>
                        <td>{{ number_format($totalOrders) }}</td>
                        <td>{{ number_format($totalRevenue) }} đ</td>
                        <td>{{ $totalOrders > 0 ? number_format($totalRevenue / $totalOrders) . ' đ' : '0 đ' }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Revenue Chart
    var ctx1 = document.getElementById('revenueChart').getContext('2d');
    var labels = [];
    var revenueData = [];
    var orderData = [];
    
    @foreach($revenueData as $data)
        @if($groupBy == 'week')
            @php
                list($year, $week) = explode('-', $data->date);
                $label = "Tuần {$week}, {$year}";
            @endphp
            labels.push('{{ $label }}');
        @elseif($groupBy == 'month')
            @php
                $dateObj = Carbon\Carbon::createFromFormat('Y-m', $data->date);
                $label = $dateObj->format('m/Y');
            @endphp
            labels.push('{{ $label }}');
        @elseif($groupBy == 'year')
            labels.push('{{ $data->date }}');
        @else
            @php
                $dateObj = Carbon\Carbon::createFromFormat('Y-m-d', $data->date);
                $label = $dateObj->format('d/m/Y');
            @endphp
            labels.push('{{ $label }}');
        @endif
        
        revenueData.push({{ $data->total_revenue }});
        orderData.push({{ $data->total_orders }});
    @endforeach
    
    var revenueChart = new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Doanh thu (VNĐ)',
                    data: revenueData,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    yAxisID: 'y-axis-1'
                },
                {
                    label: 'Số đơn hàng',
                    data: orderData,
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
                'y-axis-1': {
                    type: 'linear',
                    position: 'left',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Doanh thu (VNĐ)'
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
                },
                'y-axis-2': {
                    type: 'linear',
                    position: 'right',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Số đơn hàng'
                    },
                    grid: {
                        drawOnChartArea: false
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
                                label += new Intl.NumberFormat('vi-VN').format(context.raw) + ' đơn';
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });

    // Payment Method Chart
    var ctx2 = document.getElementById('paymentMethodChart').getContext('2d');
    
    // Chuẩn bị dữ liệu
    var paymentLabels = [];
    var paymentTotals = [];
    var backgroundColors = [
        'rgba(255, 99, 132, 0.7)',
        'rgba(54, 162, 235, 0.7)',
        'rgba(255, 206, 86, 0.7)',
        'rgba(75, 192, 192, 0.7)',
        'rgba(153, 102, 255, 0.7)'
    ];
    
    @foreach($paymentMethodsData as $data)
        paymentLabels.push('{{ $paymentMethods[$data->payment_method] ?? $data->payment_method }}');
        paymentTotals.push({{ $data->total }});
    @endforeach
    
    var paymentMethodChart = new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: paymentLabels,
            datasets: [{
                label: 'Doanh thu',
                data: paymentTotals,
                backgroundColor: backgroundColors,
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
</script>

<script>
    // Initialize datatable for the revenue table
    document.addEventListener('DOMContentLoaded', function () {
        const dataTable = document.querySelector('.datatable-table');
        if (dataTable) {
            new simpleDatatables.DataTable(dataTable, {
                searchable: true,
                fixedHeight: true,
                paging: true,
                perPage: 20
            });
        }
    });
</script>
@endpush
@endsection