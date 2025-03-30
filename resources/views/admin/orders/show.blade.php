@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng #' . $order->order_number)

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Chi tiết đơn hàng #{{ $order->order_number }}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Đơn hàng</a></li>
        <li class="breadcrumb-item active">Chi tiết đơn hàng #{{ $order->order_number }}</li>
    </ol>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
    <div class="row">
        <div class="col-md-8">
            <!-- Order Details -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-box-open me-1"></i>
                        Thông tin đơn hàng
                    </div>
                    <div>
                        @php
                            $statusClass = [
                                'pending' => 'warning',
                                'processing' => 'info',
                                'completed' => 'success',
                                'declined' => 'danger',
                                'cancelled' => 'danger'
                            ][$order->status];
                            
                            $statusText = [
                                'pending' => 'Chờ xử lý',
                                'processing' => 'Đang xử lý',
                                'completed' => 'Hoàn thành',
                                'declined' => 'Từ chối',
                                'cancelled' => 'Đã hủy'
                            ][$order->status];
                        @endphp
                        <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6"></div>
                            <table class="table table-borderless">
                                <tr>
                                    <th>Mã đơn hàng:</th>
                                    <td>{{ $order->order_number }}</td>
                                </tr>
                                <tr>
                                    <th>Ngày đặt hàng:</th>
                                    <td>{{ $order->created_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Phương thức thanh toán:</th>
                                    <td>{{ $order->payment_method }}</td>
                                </tr>
                                <tr>
                                    <th>Trạng thái thanh toán:</th>
                                    <td>
                                        @if($order->payment_status)
                                            <span class="badge bg-success">Đã thanh toán</span>
                                        @else
                                            <span class="badge bg-warning">Chưa thanh toán</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>Tổng tiền hàng:</th>
                                    <td>{{ number_format($order->subtotal) }}đ</td>
                                </tr>
                                @if($order->discount > 0)
                                <tr>
                                    <th>Giảm giá:</th>
                                    <td>-{{ number_format($order->discount) }}đ</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Phí vận chuyển:</th>
                                    <td>{{ number_format($order->shipping_fee) }}đ</td>
                                </tr>
                                <tr>
                                    <th>Tổng thanh toán:</th>
                                    <td class="fw-bold">{{ number_format($order->total) }}đ</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                
                    <h5 class="my-3">Chi tiết sản phẩm</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Biến thể</th>
                                    <th>Giá</th>
                                    <th>Số lượng</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $item)
                                <tr>
                                    <td>
                                        @if($item->product)
                                            <div class="d-flex align-items-center">
                                                @if(isset($item->product->images[0]))
                                                <img src="{{ asset('storage/' . $item->product->images[0]->path) }}" 
                                                    alt="{{ $item->product->name }}" class="img-thumbnail me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                                @endif
                                                {{ $item->product->name }}
                                            </div>
                                        @else
                                            <span class="text-muted">Sản phẩm đã bị xóa</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->productVariant)
                                            {{ $item->productVariant->size }} / {{ $item->productVariant->color }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ number_format($item->price) }}đ</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->price * $item->quantity) }}đ</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Order History -->
            <!-- <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-history me-1"></i>
                    Lịch sử đơn hàng
                </div>
                <div class="card-body">
                    <ul class="timeline">
                        <li>
                            <span class="timeline-date">{{ $order->created_at->format('d/m/Y H:i:s') }}</span>
                            <h6 class="timeline-title">Đơn hàng được tạo</h6>
                            <p>Khách hàng đã đặt đơn hàng thành công.</p>
                        </li>
                        
                    </ul>
                </div>
            </div> -->
        </div>
        
        <div class="col-md-4">
            <!-- Customer Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user me-1"></i>
                    Thông tin khách hàng
                </div>
                <div class="card-body">
                    @if($order->user)
                        <p><strong>Tên khách hàng:</strong> {{ $order->user->name }}</p>
                        <p><strong>Email:</strong> {{ $order->user->email }}</p>
                        <p><strong>Số điện thoại:</strong> {{ $order->user->phone ?? 'N/A' }}</p>
                        <p><strong>Ngày đăng ký:</strong> {{ $order->user->created_at->format('d/m/Y') }}</p>
                        <a href="{{ route('admin.users.show', $order->user->id) }}" class="btn btn-outline-primary btn-sm">
                            Xem chi tiết khách hàng
                        </a>
                    @else
                        <p>Không có thông tin khách hàng.</p>
                    @endif
                </div>
            </div>
            
            <!-- Shipping Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-truck me-1"></i>
                    Thông tin vận chuyển
                </div>
                <div class="card-body">
                    <p><strong>Người nhận:</strong> {{ $order->shipping_name }}</p>
                    <p><strong>Số điện thoại:</strong> {{ $order->shipping_phone }}</p>
                    <p><strong>Địa chỉ:</strong> {{ $order->shipping_address }}</p>
                    <p><strong>Ghi chú:</strong> {{ $order->notes ?? 'Không có' }}</p>
                </div>
            </div>
            
            <!-- Order Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-cogs me-1"></i>
                    Hành động
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="status" class="form-label">Cập nhật trạng thái</label>
                            <select name="status" id="status" class="form-select">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                <option value="declined" {{ $order->status == 'declined' ? 'selected' : '' }}>Từ chối</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Cập nhật trạng thái</button>
                    </form>
                    
                    <hr>
                    
                    <div class="d-grid gap-2">
                        <a href="#" class="btn btn-outline-secondary" onclick="window.print()">
                            <i class="fas fa-print me-1"></i> In đơn hàng
                        </a>
                        
                        @if($order->status === 'cancelled')
                            <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100" 
                                    onclick="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này?')">
                                    <i class="fas fa-trash me-1"></i> Xóa đơn hàng
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Timeline styling */
.timeline {
    position: relative;
    padding: 20px 0;
    list-style: none;
    margin: 0;
}

.timeline:before {
    content: " ";
    position: absolute;
    top: 0;
    left: 18px;
    height: 100%;
    width: 2px;
    background: #e9ecef;
}

.timeline > li {
    position: relative;
    margin-bottom: 20px;
    padding-left: 45px;
}

.timeline > li:before {
    content: " ";
    display: inline-block;
    position: absolute;
    border-radius: 50%;
    background: #4e73df;
    left: 10px;
    top: 5px;
    height: 20px;
    width: 20px;
}

.timeline-date {
    display: block;
    color: #6c757d;
    font-size: 0.8rem;
    margin-bottom: 5px;
}

.timeline-title {
    margin-top: 0;
    margin-bottom: 5px;
    font-weight: 600;
}

@media print {
    .sidebar, .navbar, .breadcrumb, .card-header, form, .btn {
        display: none !important;
    }
    
    body {
        padding: 0;
        margin: 0;
    }
    
    .container-fluid {
        width: 100%;
        padding: 0;
        margin: 0;
    }
}
</style>
@endsection