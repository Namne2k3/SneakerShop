@extends('layouts.app')

@section('title', 'Giỏ hàng - Sneaker Shop')

@section('content')
<div class="container my-5">
    <h1 class="mb-4">Giỏ hàng</h1>
    
    @if(session()->has('cart') && count(session('cart')) > 0)
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">
                        <div class="row align-items-center">
                            <div class="col-md-6">Sản phẩm</div>
                            <div class="col-md-2 text-center">Giá</div>
                            <div class="col-md-2 text-center">Số lượng</div>
                            <div class="col-md-2 text-center">Thành tiền</div>
                        </div>
                    </div>
                    <div class="card-body">
                        @php
                            $totalAmount = 0;
                            $totalItems = 0;
                        @endphp

                        @foreach(session('cart') as $id => $item)
                            @php
                                $price = $item['price'];
                                $subtotal = $price * $item['quantity'];
                                $totalAmount += $subtotal;
                                $totalItems += $item['quantity'];
                                $isExternalUrl = filter_var($item['image'], FILTER_VALIDATE_URL);
                                $imageSrc = $item['image'] ? ($isExternalUrl ? $item['image'] : asset('storage/' . $item['image'])) : 'https://via.placeholder.com/100x100?text=No+Image';
                            @endphp
                            <div class="row cart-item align-items-center py-3 border-bottom">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $imageSrc }}" alt="{{ $item['product_name'] }}" class="img-fluid" style="width: 80px; height: 80px; object-fit: cover;">
                                        <div class="ms-3">
                                            <h5 class="mb-0">{{ $item['product_name'] }}</h5>
                                            @if(isset($item['variant_id']) && $item['variant_id'])
                                                <small class="text-muted">
                                                    @if(isset($item['size'])) Size: {{ $item['size'] }}@endif
                                                    @if(isset($item['color'])) / Màu: {{ $item['color'] }}@endif
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center">
                                    <span>{{ number_format($price) }} đ</span>
                                </div>
                                <div class="col-md-2 text-center">
                                    <form action="{{ route('cart.update') }}" method="POST" class="d-flex justify-content-center">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $id }}">
                                        <div class="input-group" style="width: 100px;">
                                            <button type="button" class="btn btn-sm btn-outline-secondary decrease-qty">-</button>
                                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="form-control form-control-sm text-center qty-input">
                                            <button type="button" class="btn btn-sm btn-outline-secondary increase-qty">+</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-2 text-center position-relative">
                                    <span class="fw-bold">{{ number_format($subtotal) }} đ</span>
                                    <form action="{{ route('cart.update') }}" method="POST" class="position-absolute top-0 end-0">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $id }}">
                                        <input type="hidden" name="remove" value="1">
                                        <button type="submit" class="btn btn-link text-danger p-0" title="Xóa">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Tổng giỏ hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Tổng sản phẩm:</span>
                            <span>{{ $totalItems }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Tạm tính:</span>
                            <span>{{ number_format($totalAmount) }} đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Phí vận chuyển:</span>
                            <span>Miễn phí</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-bold">Tổng cộng:</span>
                            <span class="fw-bold text-danger">{{ number_format($totalAmount) }} đ</span>
                        </div>

                        <!-- Coupon Code -->
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Mã giảm giá">
                                <button class="btn btn-outline-secondary" type="button">Áp dụng</button>
                            </div>
                        </div>

                        <!-- Checkout Button -->
                        <a href="{{ route('checkout') }}" class="btn btn-primary w-100">Tiến hành thanh toán</a>
                        <a href="{{ route('shop') }}" class="btn btn-outline-secondary w-100 mt-2">Tiếp tục mua sắm</a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-4x mb-3 text-muted"></i>
            <h3>Giỏ hàng của bạn đang trống</h3>
            <p class="mb-4">Hãy thêm sản phẩm vào giỏ hàng để tiến hành mua sắm</p>
            <a href="{{ route('shop') }}" class="btn btn-primary">Tiếp tục mua sắm</a>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Handle quantity increase
        $('.increase-qty').on('click', function() {
            const input = $(this).siblings('.qty-input');
            const currentVal = parseInt(input.val());
            input.val(currentVal + 1);
            $(this).closest('form').submit();
        });
        
        // Handle quantity decrease
        $('.decrease-qty').on('click', function() {
            const input = $(this).siblings('.qty-input');
            const currentVal = parseInt(input.val());
            if (currentVal > 1) {
                input.val(currentVal - 1);
                $(this).closest('form').submit();
            }
        });
        
        // Auto-submit when quantity is changed manually
        $('.qty-input').on('change', function() {
            $(this).closest('form').submit();
        });
    });
</script>
@endsection