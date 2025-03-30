@extends('layouts.admin')

@section('title', 'Quản lý mã giảm giá')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý mã giảm giá</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Mã giảm giá</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-tag me-1"></i>
                    Danh sách mã giảm giá
                </div>
                <div>
                    <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i> Thêm mã giảm giá
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="coupons-table">
                    <thead>
                        <tr>
                            <th width="50">ID</th>
                            <th>Mã</th>
                            <th>Loại giảm giá</th>
                            <th>Giá trị</th>
                            <th>Giá trị tối thiểu</th>
                            <th>Số lượt dùng</th>
                            <th>Thời gian</th>
                            <th>Trạng thái</th>
                            <th width="150">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($coupons as $coupon)
                        <tr>
                            <td>{{ $coupon->id }}</td>
                            <td><strong>{{ $coupon->code }}</strong></td>
                            <td>
                                @if($coupon->type == 'fixed')
                                    <span class="badge bg-info">Giảm tiền cố định</span>
                                @else
                                    <span class="badge bg-primary">Giảm theo phần trăm</span>
                                @endif
                            </td>
                            <td>
                                @if($coupon->type == 'fixed')
                                    {{ number_format($coupon->value) }}đ
                                @else
                                    {{ number_format($coupon->value) }}%
                                @endif
                            </td>
                            <td>{{ number_format($coupon->min_order_amount) }}đ</td>
                            <td>
                                @if($coupon->max_uses)
                                    {{ $coupon->used }}/{{ $coupon->max_uses }}
                                @else
                                    {{ $coupon->used }}/∞
                                @endif
                            </td>
                            <td>
                                @if($coupon->starts_at && $coupon->expires_at)
                                    {{ $coupon->starts_at->format('d/m/Y') }} - {{ $coupon->expires_at->format('d/m/Y') }}
                                @elseif($coupon->starts_at)
                                    Từ {{ $coupon->starts_at->format('d/m/Y') }}
                                @elseif($coupon->expires_at)
                                    Đến {{ $coupon->expires_at->format('d/m/Y') }}
                                @else
                                    Không giới hạn
                                @endif
                            </td>
                            <td>
                                @php
                                    $now = now();
                                    $isActive = (!$coupon->starts_at || $coupon->starts_at <= $now) && 
                                              (!$coupon->expires_at || $coupon->expires_at >= $now) && 
                                              (!$coupon->max_uses || $coupon->used < $coupon->max_uses);
                                @endphp

                                @if($isActive)
                                    <span class="badge bg-success">Còn hiệu lực</span>
                                @else
                                    <span class="badge bg-danger">Hết hiệu lực</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa mã giảm giá này?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">Không có mã giảm giá nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $coupons->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#coupons-table').DataTable({
            paging: false,
            searching: true,
            ordering: true,
            info: false,
            responsive: true,
            language: {
                search: "Tìm kiếm:",
                zeroRecords: "Không tìm thấy kết quả nào",
                emptyTable: "Không có dữ liệu trong bảng"
            }
        });
    });
</script>
@endsection