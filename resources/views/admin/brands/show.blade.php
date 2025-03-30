@extends('layouts.admin')

@section('title', 'Chi tiết thương hiệu - Sneaker Shop Admin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Chi tiết thương hiệu</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.brands.index') }}">Thương hiệu</a></li>
        <li class="breadcrumb-item active">Chi tiết</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-info-circle me-1"></i>
                Thông tin thương hiệu
            </div>
            <div>
                <a href="{{ route('admin.brands.edit', $brand->id) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit me-1"></i> Chỉnh sửa
                </a>
                <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-list me-1"></i> Danh sách thương hiệu
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <!-- Brand Logo -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-image me-1"></i>
                            Logo
                        </div>
                        <div class="card-body text-center">
                            @if($brand->logo)
                                <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}" class="img-fluid rounded" style="max-height: 250px;">
                            @else
                                <div class="p-5 bg-light rounded d-flex flex-column align-items-center justify-content-center">
                                    <i class="fas fa-image fa-5x text-muted mb-3"></i>
                                    <p class="text-muted">Không có logo</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-chart-bar me-1"></i>
                            Thống kê
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Sản phẩm
                                    <span class="badge bg-primary rounded-pill">{{ $brand->products->count() }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Ngày tạo
                                    <span>{{ $brand->created_at->format('d/m/Y H:i') }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Cập nhật lần cuối
                                    <span>{{ $brand->updated_at->format('d/m/Y H:i') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <!-- Basic Info -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-info-circle me-1"></i>
                            Thông tin cơ bản
                        </div>
                        <div class="card-body">
                            <table class="table table-striped border">
                                <tbody>
                                    <tr>
                                        <th style="width: 200px;">ID</th>
                                        <td>{{ $brand->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tên thương hiệu</th>
                                        <td>{{ $brand->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Slug</th>
                                        <td>{{ $brand->slug }}</td>
                                    </tr>
                                    <tr>
                                        <th>Mô tả</th>
                                        <td>
                                            @if($brand->description)
                                                {!! nl2br(e($brand->description)) !!}
                                            @else
                                                <span class="text-muted">Không có mô tả</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Brand Products -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-box me-1"></i>
                                Sản phẩm thuộc thương hiệu này
                            </div>
                            <a href="{{ route('admin.products.index') }}?brand={{ $brand->id }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-search me-1"></i> Xem tất cả
                            </a>
                        </div>
                        <div class="card-body">
                            @if($brand->products->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Hình ảnh</th>
                                                <th>Tên sản phẩm</th>
                                                <th>Giá</th>
                                                <th>Trạng thái</th>
                                                <th>Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($brand->products->take(5) as $product)
                                                <tr>
                                                    <td>{{ $product->id }}</td>
                                                    <td>
                                                        @if($product->images->isNotEmpty())
                                                            @php
                                                                $mainImage = $product->images->where('is_primary', 1)->first() ?? $product->images->first();
                                                                $imagePath = $mainImage->image_path;
                                                                $isExternalUrl = filter_var($imagePath, FILTER_VALIDATE_URL);
                                                            @endphp
                                                            <img src="{{ $isExternalUrl ? $imagePath : asset('storage/' . $imagePath) }}" 
                                                                class="img-thumbnail" alt="{{ $product->name }}" style="max-height: 40px;">
                                                        @else
                                                            <span class="text-muted"><i class="fas fa-image"></i></span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.products.show', $product->id) }}">{{ $product->name }}</a>
                                                        @if($product->featured)
                                                            <span class="badge bg-warning">Nổi bật</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($product->sale_price)
                                                            <del class="text-muted">{{ number_format($product->price) }} đ</del><br>
                                                            <span class="text-danger">{{ number_format($product->sale_price) }} đ</span>
                                                        @else
                                                            {{ number_format($product->price) }} đ
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($product->active)
                                                            <span class="badge bg-success">Hiển thị</span>
                                                        @else
                                                            <span class="badge bg-secondary">Ẩn</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($brand->products->count() > 5)
                                    <div class="text-center mt-3">
                                        <a href="{{ route('admin.products.index') }}?brand={{ $brand->id }}" class="btn btn-outline-primary">
                                            <i class="fas fa-plus me-1"></i> Xem tất cả {{ $brand->products->count() }} sản phẩm
                                        </a>
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle me-1"></i> Chưa có sản phẩm nào thuộc thương hiệu này.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-4 d-flex justify-content-between">
                <div>
                    <a href="{{ route('brand.show', $brand->slug) }}" class="btn btn-info" target="_blank">
                        <i class="fas fa-eye me-1"></i> Xem trang thương hiệu
                    </a>
                </div>
                <div>
                    <a href="{{ route('admin.brands.edit', $brand->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i> Chỉnh sửa
                    </a>
                    <form action="{{ route('admin.brands.destroy', $brand->id) }}" method="POST" class="d-inline ms-2" 
                          onsubmit="return confirm('Bạn có chắc chắn muốn xóa thương hiệu này? Lưu ý: Không thể xóa thương hiệu đã có sản phẩm.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" {{ $brand->products->count() > 0 ? 'disabled' : '' }}>
                            <i class="fas fa-trash-alt me-1"></i> Xóa thương hiệu
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection