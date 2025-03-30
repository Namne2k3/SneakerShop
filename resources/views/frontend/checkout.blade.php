@extends('layouts.app')

@section('title', 'Thanh toán - Sneaker Shop')

@section('content')
<div class="container my-5">
    <h1 class="mb-4">Thanh toán</h1>
    
    @if(session()->has('cart') && count(session('cart')) > 0)
        <form action="{{ route('order.place') }}" method="POST">
            @csrf
            <div class="row">
                <!-- Customer Information -->
                <div class="col-lg-7">
                    <div class="card mb-4">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">Thông tin giao hàng</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="shipping_name" class="form-label">Họ tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('shipping_name') is-invalid @enderror" id="shipping_name" name="shipping_name" value="{{ auth()->user()->name ?? old('shipping_name') }}" required>
                                    @error('shipping_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="shipping_email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('shipping_email') is-invalid @enderror" id="shipping_email" name="shipping_email" value="{{ auth()->user()->email ?? old('shipping_email') }}" required>
                                    @error('shipping_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="shipping_phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('shipping_phone') is-invalid @enderror" id="shipping_phone" name="shipping_phone" value="{{ auth()->user()->phone_number ?? old('shipping_phone') }}" required>
                                    @error('shipping_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-12">
                                    <label for="shipping_address" class="form-label">Địa chỉ <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('shipping_address') is-invalid @enderror" id="shipping_address" name="shipping_address" value="{{ auth()->user()->address ?? old('shipping_address') }}" required>
                                    @error('shipping_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-12">
                                    <label for="shipping_city" class="form-label">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('shipping_city') is-invalid @enderror" id="shipping_city" name="shipping_city" value="{{ old('shipping_city') }}" required>
                                    @error('shipping_city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-12">
                                    <label for="notes" class="form-label">Ghi chú</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">Phương thức thanh toán</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                                <label class="form-check-label" for="cod">
                                    <i class="fas fa-money-bill-wave me-2"></i> Thanh toán khi nhận hàng (COD)
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="bank_transfer" value="bank_transfer">
                                <label class="form-check-label" for="bank_transfer">
                                    <i class="fas fa-university me-2"></i> Chuyển khoản ngân hàng
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="momo" value="momo">
                                <label class="form-check-label" for="momo">
                                    <i class="fas fa-wallet me-2"></i> Ví điện tử MoMo
                                </label>
                            </div>
                            
                            <div class="bank-info mt-3 p-3 bg-light rounded" style="display: none;">
                                <h6>Thông tin chuyển khoản:</h6>
                                <p class="mb-1"><strong>Ngân hàng:</strong> Vietcombank</p>
                                <p class="mb-1"><strong>Số tài khoản:</strong> 1234567890</p>
                                <p class="mb-1"><strong>Chủ tài khoản:</strong> CÔNG TY TNHH SNEAKER SHOP</p>
                                <p class="mb-0"><strong>Nội dung:</strong> Thanh toán đơn hàng + [Họ tên]</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="col-lg-5">
                    <div class="card mb-4">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">Đơn hàng của bạn</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $totalAmount = 0;
                                $totalItems = 0;
                            @endphp
                            
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Sản phẩm</th>
                                            <th class="text-center">SL</th>
                                            <th class="text-end">Giá</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(session('cart') as $id => $item)
                                            @php
                                                $price = $item['price'];
                                                $subtotal = $price * $item['quantity'];
                                                $totalAmount += $subtotal;
                                                $totalItems += $item['quantity'];
                                            @endphp
                                            <tr>
                                                <td>
                                                    {{ $item['product_name'] }}
                                                    @if(isset($item['variant_id']) && $item['variant_id'])
                                                        <small class="d-block text-muted">
                                                            @if(isset($item['size'])) Size: {{ $item['size'] }}@endif
                                                            @if(isset($item['color'])) / Màu: {{ $item['color'] }}@endif
                                                        </small>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $item['quantity'] }}</td>
                                                <td class="text-end">{{ number_format($subtotal) }} đ</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tạm tính:</span>
                                <span>{{ number_format($totalAmount) }} đ</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Phí vận chuyển:</span>
                                <span>Miễn phí</span>
                            </div>
                            
                            <!-- Coupon Form -->
                            @if(!session()->has('coupon'))
                                <div class="mt-3 mb-3">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="coupon_code" placeholder="Mã giảm giá" aria-label="Mã giảm giá">
                                        <button class="btn btn-outline-secondary" type="button" id="apply-coupon">Áp dụng</button>
                                    </div>
                                </div>
                            @else
                                @php
                                    $coupon = session('coupon');
                                    $discountAmount = ($coupon['type'] == 'fixed') 
                                        ? $coupon['value'] 
                                        : ($totalAmount * $coupon['value'] / 100);
                                    $totalAmount = $totalAmount - $discountAmount;
                                @endphp
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Giảm giá ({{ $coupon['code'] }}):</span>
                                    <span>-{{ number_format($discountAmount) }} đ</span>
                                </div>
                            @endif
                            
                            <div class="d-flex justify-content-between mb-2 mt-2">
                                <span class="fw-bold">Tổng cộng:</span>
                                <span class="fw-bold text-danger">{{ number_format($totalAmount) }} đ</span>
                            </div>
                            
                            <input type="hidden" name="total_amount" value="{{ $totalAmount }}">
                            
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" id="agree" name="agree" required>
                                <label class="form-check-label" for="agree">
                                    Tôi đã đọc và đồng ý với <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">điều khoản và điều kiện</a>
                                </label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 mt-3">Đặt hàng</button>
                            <a href="{{ route('cart') }}" class="btn btn-outline-secondary w-100 mt-2">Quay lại giỏ hàng</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @else
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-4x mb-3 text-muted"></i>
            <h3>Giỏ hàng của bạn đang trống</h3>
            <p class="mb-4">Hãy thêm sản phẩm vào giỏ hàng để tiến hành thanh toán</p>
            <a href="{{ route('shop') }}" class="btn btn-primary">Tiếp tục mua sắm</a>
        </div>
    @endif

    <!-- Terms Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">Điều khoản và điều kiện</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5>1. Đơn hàng và thanh toán</h5>
                    <p>Khi bạn đặt hàng tại Sneaker Shop, bạn đồng ý thanh toán đầy đủ số tiền của đơn hàng bằng phương thức thanh toán mà bạn đã chọn. Đơn hàng chỉ được xác nhận sau khi chúng tôi nhận được thanh toán đầy đủ hoặc xác nhận đơn hàng (đối với thanh toán khi nhận hàng).</p>
                    
                    <h5>2. Giao hàng</h5>
                    <p>Sneaker Shop sẽ giao hàng đến địa chỉ mà bạn đã cung cấp. Thời gian giao hàng thông thường từ 2-5 ngày làm việc tùy thuộc vào khu vực. Chúng tôi không chịu trách nhiệm cho việc giao hàng chậm trễ do thông tin địa chỉ không chính xác hoặc do các yếu tố khách quan như thiên tai, dịch bệnh.</p>
                    
                    <h5>3. Đổi trả và hoàn tiền</h5>
                    <p>Bạn có thể yêu cầu đổi trả trong vòng 7 ngày kể từ ngày nhận hàng nếu sản phẩm bị lỗi do nhà sản xuất hoặc không đúng mô tả. Sản phẩm đổi trả phải còn nguyên tem, nhãn mác và chưa qua sử dụng. Chúng tôi sẽ không chấp nhận đổi trả cho các sản phẩm đã qua sử dụng hoặc bị hư hỏng do lỗi của người sử dụng.</p>
                    
                    <h5>4. Bảo mật thông tin</h5>
                    <p>Sneaker Shop cam kết bảo mật thông tin cá nhân của khách hàng. Chúng tôi không chia sẻ thông tin của bạn cho bên thứ ba nào trừ khi được pháp luật yêu cầu hoặc được sự đồng ý của bạn.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tôi đồng ý</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Toggle bank transfer information
        $('input[name="payment_method"]').change(function() {
            if($(this).val() === 'bank_transfer') {
                $('.bank-info').slideDown();
            } else {
                $('.bank-info').slideUp();
            }
        });
        
        // Apply coupon button
        $('#apply-coupon').click(function() {
            // This would typically submit an AJAX request to validate the coupon
            // and update the order summary
            const couponCode = $('input[name="coupon_code"]').val();
            if(couponCode) {
                // Example of how you would implement the AJAX request
                /*
                $.ajax({
                    url: '/apply-coupon',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        coupon_code: couponCode
                    },
                    success: function(response) {
                        if(response.success) {
                            // Update the order summary
                            window.location.reload();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('Đã xảy ra lỗi. Vui lòng thử lại sau.');
                    }
                });
                */
                
                // For now, just show an alert
                alert('Tính năng áp dụng mã giảm giá đang được phát triển.');
            } else {
                alert('Vui lòng nhập mã giảm giá.');
            }
        });
    });
</script>
@endsection