@extends('layouts.admin')

@section('title', 'Thêm mã giảm giá')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Thêm mã giảm giá</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.coupons.index') }}">Mã giảm giá</a></li>
        <li class="breadcrumb-item active">Thêm mã giảm giá</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-tag me-1"></i>
            Thông tin mã giảm giá
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.coupons.store') }}" method="POST">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="code" class="form-label">Mã giảm giá <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="code" name="code" value="{{ old('code') }}" required>
                                <button class="btn btn-outline-secondary" type="button" id="generate-code">Tạo mã</button>
                            </div>
                            <small class="form-text text-muted">Mã giảm giá sẽ được chuyển thành chữ hoa.</small>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea class="form-control" id="description" name="description" rows="2">{{ old('description') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Loại giảm giá <span class="text-danger">*</span></label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Giảm tiền cố định</option>
                                <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Giảm theo phần trăm</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="value" class="form-label">Giá trị <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="value" name="value" value="{{ old('value') }}" required min="0" step="0.01">
                                <span class="input-group-text" id="value-addon">đ</span>
                            </div>
                            <small class="form-text text-muted" id="value-help">Số tiền giảm giá cố định.</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="min_order_amount" class="form-label">Giá trị đơn hàng tối thiểu</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="min_order_amount" name="min_order_amount" value="{{ old('min_order_amount', 0) }}" min="0">
                                <span class="input-group-text">đ</span>
                            </div>
                            <small class="form-text text-muted">Đơn hàng phải đạt giá trị tối thiểu này mới được áp dụng mã giảm giá.</small>
                        </div>

                        <div class="mb-3">
                            <label for="max_uses" class="form-label">Số lượt sử dụng tối đa</label>
                            <input type="number" class="form-control" id="max_uses" name="max_uses" value="{{ old('max_uses') }}" min="0">
                            <small class="form-text text-muted">Để trống nếu không giới hạn số lượt sử dụng.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="starts_at" class="form-label">Ngày bắt đầu</label>
                                    <input type="date" class="form-control" id="starts_at" name="starts_at" value="{{ old('starts_at') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expires_at" class="form-label">Ngày hết hạn</label>
                                    <input type="date" class="form-control" id="expires_at" name="expires_at" value="{{ old('expires_at') }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-check-label mb-2">Tùy chọn</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Kích hoạt</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">Hủy</a>
                    <button type="submit" class="btn btn-primary">Lưu mã giảm giá</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Generate random coupon code
        $('#generate-code').click(function() {
            const randomCode = Math.random().toString(36).substring(2, 8).toUpperCase();
            $('#code').val(randomCode);
        });

        // Update value addon based on coupon type
        $('#type').change(function() {
            const type = $(this).val();
            if (type === 'percentage') {
                $('#value-addon').text('%');
                $('#value-help').text('Phần trăm giảm giá (0-100).');
                $('#value').attr('max', 100);
            } else {
                $('#value-addon').text('đ');
                $('#value-help').text('Số tiền giảm giá cố định.');
                $('#value').removeAttr('max');
            }
        });

        // Trigger change event to initialize
        $('#type').trigger('change');
    });
</script>
@endsection