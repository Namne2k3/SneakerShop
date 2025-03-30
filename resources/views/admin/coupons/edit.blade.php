@extends('layouts.admin')

@section('title', 'Chỉnh sửa mã giảm giá - Sneaker Shop Admin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Chỉnh sửa mã giảm giá</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.coupons.index') }}">Mã giảm giá</a></li>
        <li class="breadcrumb-item active">Chỉnh sửa</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-tag me-1"></i>
            Thông tin mã giảm giá
        </div>
        <div class="card-body">
            @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong><i class="fas fa-exclamation-triangle me-1"></i>Lỗi!</strong> Vui lòng kiểm tra lại thông tin.
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="code" class="form-label">Mã giảm giá <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $coupon->code) }}" placeholder="VD: SUMMER2023" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Mã giảm giá là duy nhất, không phân biệt chữ hoa/thường</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="type" class="form-label">Loại giảm giá <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="fixed" {{ old('type', $coupon->type) == 'fixed' ? 'selected' : '' }}>Giảm giá cố định (VNĐ)</option>
                                <option value="percent" {{ old('type', $coupon->type) == 'percent' ? 'selected' : '' }}>Giảm giá phần trăm (%)</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="value" class="form-label">Giá trị <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('value') is-invalid @enderror" id="value" name="value" value="{{ old('value', $coupon->value) }}" min="0" required>
                                <span class="input-group-text" id="value-addon">VNĐ</span>
                            </div>
                            @error('value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted" id="value-help">Số tiền giảm giá cố định</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="min_order_value" class="form-label">Giá trị đơn hàng tối thiểu <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('min_order_value') is-invalid @enderror" id="min_order_value" name="min_order_value" value="{{ old('min_order_value', $coupon->min_order_value) }}" min="0" required>
                                <span class="input-group-text">VNĐ</span>
                            </div>
                            @error('min_order_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Giá trị đơn hàng tối thiểu để áp dụng mã giảm giá</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                            @php
                                $startDateValue = '';
                                if (old('start_date')) {
                                    $startDateValue = old('start_date');
                                } elseif ($coupon->start_date) {
                                    if ($coupon->start_date instanceof \Carbon\Carbon) {
                                        $startDateValue = $coupon->start_date->format('Y-m-d');
                                    } else {
                                        $startDateValue = date('Y-m-d', strtotime($coupon->start_date));
                                    }
                                }
                            @endphp
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ $startDateValue }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="end_date" class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                            @php
                                $endDateValue = '';
                                if (old('end_date')) {
                                    $endDateValue = old('end_date');
                                } elseif ($coupon->end_date) {
                                    if ($coupon->end_date instanceof \Carbon\Carbon) {
                                        $endDateValue = $coupon->end_date->format('Y-m-d');
                                    } else {
                                        $endDateValue = date('Y-m-d', strtotime($coupon->end_date));
                                    }
                                }
                            @endphp
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ $endDateValue }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3"></div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="active" name="active" value="1" {{ old('active', $coupon->active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="active">Kích hoạt mã giảm giá</label>
                            </div>
                            <small class="text-muted">Mã giảm giá sẽ chỉ có hiệu lực nếu được kích hoạt và nằm trong khoảng thời gian hiệu lực</small>
                        </div>

                        @php
                            $now = \Carbon\Carbon::now();
                            $isExpired = false;
                            $notStarted = false;

                            if ($coupon->end_date) {
                                $endDate = $coupon->end_date instanceof \Carbon\Carbon 
                                    ? $coupon->end_date 
                                    : \Carbon\Carbon::parse($coupon->end_date);
                                $isExpired = $endDate < $now;
                            }

                            if ($coupon->start_date) {
                                $startDate = $coupon->start_date instanceof \Carbon\Carbon 
                                    ? $coupon->start_date 
                                    : \Carbon\Carbon::parse($coupon->start_date);
                                $notStarted = $startDate > $now;
                            }
                        @endphp
                        
                        @if($isExpired)
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-1"></i>
                                Mã giảm giá này đã hết hạn vào ngày {{ $coupon->end_date instanceof \Carbon\Carbon ? $coupon->end_date->format('d/m/Y') : date('d/m/Y', strtotime($coupon->end_date)) }}
                            </div>
                        @elseif($notStarted)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Mã giảm giá này sẽ có hiệu lực từ ngày {{ $coupon->start_date instanceof \Carbon\Carbon ? $coupon->start_date->format('d/m/Y') : date('d/m/Y', strtotime($coupon->start_date)) }}
                            </div>
                        @elseif(!$coupon->active)
                            <div class="alert alert-secondary">
                                <i class="fas fa-info-circle me-1"></i>
                                Mã giảm giá này hiện đang bị vô hiệu hóa
                            </div>
                        @else
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-1"></i>
                                Mã giảm giá này đang có hiệu lực
                            </div>
                        @endif
                    </div>
                </div>

                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-1"></i>
                    <span id="coupon-preview">Mã giảm giá <strong>{{ old('code', $coupon->code) }}</strong> sẽ giảm <strong>{{ $coupon->type == 'fixed' ? number_format($coupon->value) . ' VNĐ' : $coupon->value . '%' }}</strong> cho đơn hàng từ <strong>{{ number_format($coupon->min_order_value) }} VNĐ</strong>.</span>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type');
        const valueInput = document.getElementById('value');
        const valueAddon = document.getElementById('value-addon');
        const valueHelp = document.getElementById('value-help');
        
        // Coupon preview elements
        const codeInput = document.getElementById('code');
        const minOrderValueInput = document.getElementById('min_order_value');
        const couponPreview = document.getElementById('coupon-preview');
        
        function updateValueLabel() {
            if (typeSelect.value === 'fixed') {
                valueAddon.textContent = 'VNĐ';
                valueHelp.textContent = 'Số tiền giảm giá cố định';
            } else {
                valueAddon.textContent = '%';
                valueHelp.textContent = 'Phần trăm giảm giá (1-100)';
                if (parseInt(valueInput.value) > 100) {
                    valueInput.value = 100;
                }
            }
            updatePreview();
        }
        
        function updatePreview() {
            const code = codeInput.value || 'COUPONCODE';
            const value = valueInput.value || '0';
            const minOrderValue = minOrderValueInput.value || '0';
            
            let valueText = '';
            if (typeSelect.value === 'fixed') {
                valueText = `<strong>${parseInt(value).toLocaleString('vi-VN')} VNĐ</strong>`;
            } else {
                valueText = `<strong>${value}%</strong>`;
            }
            
            couponPreview.innerHTML = `Mã giảm giá <strong>${code}</strong> sẽ giảm ${valueText} cho đơn hàng từ <strong>${parseInt(minOrderValue).toLocaleString('vi-VN')} VNĐ</strong>.`;
        }
        
        typeSelect.addEventListener('change', updateValueLabel);
        valueInput.addEventListener('input', updatePreview);
        codeInput.addEventListener('input', updatePreview);
        minOrderValueInput.addEventListener('input', updatePreview);
        
        // Initial setup
        updateValueLabel();
    });
</script>
@endsection