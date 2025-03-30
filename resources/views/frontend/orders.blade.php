@extends('layouts.app')

@section('title', 'Đơn hàng của tôi - Sneaker Shop')

@section('content')
<div class="container my-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Tài khoản của tôi</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('profile') }}" class="list-group-item list-group-item-action">Thông tin cá nhân</a>
                    <a href="{{ route('orders') }}" class="list-group-item list-group-item-action active">Đơn hàng của tôi</a>
                    <a href="{{ route('wishlist') }}" class="list-group-item list-group-item-action">Sản phẩm yêu thích</a>
                    <a href="{{ route('logout') }}" class="list-group-item list-group-item-action text-danger" 
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                       Đăng xuất
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <h2 class="mb-4">Đơn hàng của tôi</h2>
            
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            
            @if($orders->isEmpty())
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                        <h4>Bạn chưa có đơn hàng nào</h4>
                        <p class="text-muted">Hãy mua sắm và quay lại đây để xem lịch sử đơn hàng của bạn.</p>
                        <a href="{{ route('shop') }}" class="btn btn-primary">Tiếp tục mua sắm</a>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Mã đơn hàng</th>
                                        <th>Ngày đặt</th>
                                        <th>Trạng thái</th>
                                        <th>Tổng tiền</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>{{ $order->order_number }}</td>
                                            <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                @if($order->status == 'pending')
                                                    <span class="badge bg-warning">Chờ xử lý</span>
                                                @elseif($order->status == 'processing')
                                                    <span class="badge bg-info">Đang xử lý</span>
                                                @elseif($order->status == 'completed')
                                                    <span class="badge bg-success">Hoàn thành</span>
                                                @elseif($order->status == 'cancelled')
                                                    <span class="badge bg-danger">Đã hủy</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $order->status }}</span>
                                                @endif
                                            </td>
                                            <td>{{ number_format($order->total_amount) }} đ</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#orderModal{{ $order->id }}">
                                                    Chi tiết
                                                </button>
                                                @if($order->status == 'pending')
                                                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal{{ $order->id }}">
                                                        Hủy
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $orders->links() }}
                </div>
                
                <!-- Order Detail Modals -->
                @foreach($orders as $order)
                    <div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1" aria-labelledby="orderModalLabel{{ $order->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="orderModalLabel{{ $order->id }}">Chi tiết đơn hàng #{{ $order->order_number }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <h6>Thông tin đơn hàng</h6>
                                            <p class="mb-1">Mã đơn hàng: <strong>{{ $order->order_number }}</strong></p>
                                            <p class="mb-1">Ngày đặt: <strong>{{ $order->created_at->format('d/m/Y H:i') }}</strong></p>
                                            <p class="mb-1">
                                                Trạng thái: 
                                                @if($order->status == 'pending')
                                                    <span class="badge bg-warning">Chờ xử lý</span>
                                                @elseif($order->status == 'processing')
                                                    <span class="badge bg-info">Đang xử lý</span>
                                                @elseif($order->status == 'completed')
                                                    <span class="badge bg-success">Hoàn thành</span>
                                                @elseif($order->status == 'cancelled')
                                                    <span class="badge bg-danger">Đã hủy</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $order->status }}</span>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Thông tin thanh toán</h6>
                                            <p class="mb-1">
                                                Phương thức thanh toán: 
                                                @if($order->payment_method == 'cod')
                                                    <strong>Thanh toán khi nhận hàng</strong>
                                                @elseif($order->payment_method == 'bank_transfer')
                                                    <strong>Chuyển khoản ngân hàng</strong>
                                                @elseif($order->payment_method == 'momo')
                                                    <strong>Ví điện tử MoMo</strong>
                                                @endif
                                            </p>
                                            <p class="mb-1">
                                                Trạng thái thanh toán:
                                                @if($order->payment_status == 'pending')
                                                    <span class="badge bg-warning">Chờ thanh toán</span>
                                                @elseif($order->payment_status == 'paid')
                                                    <span class="badge bg-success">Đã thanh toán</span>
                                                @else
                                                    <span class="badge bg-danger">Thất bại</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <h6>Thông tin giao hàng</h6>
                                            <p class="mb-1">Người nhận: <strong>{{ $order->shipping_name }}</strong></p>
                                            <p class="mb-1">Số điện thoại: <strong>{{ $order->shipping_phone }}</strong></p>
                                            <p class="mb-1">Email: <strong>{{ $order->shipping_email }}</strong></p>
                                            <p class="mb-1">Địa chỉ: <strong>{{ $order->shipping_address }}, {{ $order->shipping_city }}</strong></p>
                                        </div>
                                        <div class="col-md-6">
                                            @if($order->notes)
                                                <h6>Ghi chú</h6>
                                                <p>{{ $order->notes }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <h6>Danh sách sản phẩm</h6>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Sản phẩm</th>
                                                    <th class="text-center">Đơn giá</th>
                                                    <th class="text-center">Số lượng</th>
                                                    <th class="text-end">Thành tiền</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($order->orderItems as $item)
                                                    <tr>
                                                        <td>
                                                            {{ $item->product->name }}
                                                            @if($item->productVariant)
                                                                <small class="d-block text-muted">
                                                                    Size: {{ $item->productVariant->size }} / 
                                                                    Màu: {{ $item->productVariant->color }}
                                                                </small>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">{{ number_format($item->price) }} đ</td>
                                                        <td class="text-center">{{ $item->quantity }}</td>
                                                        <td class="text-end">{{ number_format($item->subtotal) }} đ</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3" class="text-end"><strong>Tạm tính:</strong></td>
                                                    <td class="text-end">{{ number_format($order->orderItems->sum('subtotal')) }} đ</td>
                                                </tr>
                                                @if($order->discount_amount > 0)
                                                    <tr>
                                                        <td colspan="3" class="text-end"><strong>Giảm giá:</strong></td>
                                                        <td class="text-end">-{{ number_format($order->discount_amount) }} đ</td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <td colspan="3" class="text-end"><strong>Phí vận chuyển:</strong></td>
                                                    <td class="text-end">Miễn phí</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="text-end"><strong>Tổng cộng:</strong></td>
                                                    <td class="text-end fw-bold text-danger">{{ number_format($order->total_amount) }} đ</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                    @if($order->status == 'pending')
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal{{ $order->id }}" data-bs-dismiss="modal">
                                            Hủy đơn hàng
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Cancel Order Modal -->
                    @if($order->status == 'pending')
                        <div class="modal fade" id="cancelModal{{ $order->id }}" tabindex="-1" aria-labelledby="cancelModalLabel{{ $order->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="cancelModalLabel{{ $order->id }}">Hủy đơn hàng #{{ $order->order_number }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('order.cancel', $order) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <p>Bạn có chắc chắn muốn hủy đơn hàng này?</p>
                                            <div class="mb-3">
                                                <label for="cancel_reason" class="form-label">Lý do hủy đơn:</label>
                                                <select class="form-select" id="cancel_reason" name="cancel_reason">
                                                    <option value="Thay đổi ý định mua hàng">Thay đổi ý định mua hàng</option>
                                                    <option value="Muốn thay đổi sản phẩm">Muốn thay đổi sản phẩm</option>
                                                    <option value="Tìm thấy giá tốt hơn ở nơi khác">Tìm thấy giá tốt hơn ở nơi khác</option>
                                                    <option value="Thời gian giao hàng quá lâu">Thời gian giao hàng quá lâu</option>
                                                    <option value="Lý do khác">Lý do khác</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                            <button type="submit" class="btn btn-danger">Xác nhận hủy</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection