@extends('layouts.admin')

@section('title', 'Chi tiết sản phẩm - Sneaker Shop Admin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Chi tiết sản phẩm</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Sản phẩm</a></li>
        <li class="breadcrumb-item active">Chi tiết</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-info-circle me-1"></i>
                Thông tin sản phẩm
            </div>
            <div>
                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit me-1"></i> Chỉnh sửa
                </a>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-list me-1"></i> Danh sách sản phẩm
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-5">
                    <!-- Product Images -->
                    <div class="mb-4">
                        <h5 class="border-bottom pb-2">Hình ảnh sản phẩm</h5>
                        @if($product->images->isNotEmpty())
                            <div class="product-main-image mb-3">
                                @php
                                    $mainImage = $product->images->where('is_primary', 1)->first() ?? $product->images->first();
                                    $imagePath = $mainImage->image_path;
                                    $isExternalUrl = filter_var($imagePath, FILTER_VALIDATE_URL);
                                @endphp
                                <img src="{{ $isExternalUrl ? $imagePath : asset('storage/' . $imagePath) }}" 
                                    class="img-fluid rounded" alt="{{ $product->name }}">
                            </div>
                            <div class="row g-2 product-thumbnails">
                                @foreach($product->images as $image)
                                    @php
                                        $imagePath = $image->image_path;
                                        $isExternalUrl = filter_var($imagePath, FILTER_VALIDATE_URL);
                                    @endphp
                                    <div class="col-3">
                                        <img src="{{ $isExternalUrl ? $imagePath : asset('storage/' . $imagePath) }}" 
                                            class="img-thumbnail {{ $image->is_primary ? 'border border-primary' : '' }}" 
                                            alt="Thumbnail">
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center p-5 bg-light rounded">
                                <i class="fas fa-image fa-3x text-muted mb-3"></i>
                                <p>Không có hình ảnh</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-7">
                    <!-- Basic Info -->
                    <div class="mb-4">
                        <h5 class="border-bottom pb-2">Thông tin cơ bản</h5>
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th style="width: 200px;">ID</th>
                                    <td>{{ $product->id }}</td>
                                </tr>
                                <tr>
                                    <th>Tên sản phẩm</th>
                                    <td>
                                        {{ $product->name }}
                                        @if($product->featured)
                                            <span class="badge bg-warning ms-2">Nổi bật</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Slug</th>
                                    <td>{{ $product->slug }}</td>
                                </tr>
                                <tr>
                                    <th>Danh mục</th>
                                    <td>
                                        @foreach($product->categories as $category)
                                            <span class="badge bg-secondary me-1">{{ $category->name }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <th>Thương hiệu</th>
                                    <td>{{ $product->brand ? $product->brand->name : 'Không có' }}</td>
                                </tr>
                                <tr>
                                    <th>Giá</th>
                                    <td>
                                        @if($product->sale_price)
                                            <del class="text-muted">{{ number_format($product->price) }} đ</del>
                                            <span class="text-danger ms-2">{{ number_format($product->sale_price) }} đ</span>
                                        @else
                                            {{ number_format($product->price) }} đ
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Trạng thái</th>
                                    <td>
                                        @if($product->active)
                                            <span class="badge bg-success">Hiển thị</span>
                                        @else
                                            <span class="badge bg-secondary">Ẩn</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Ngày tạo</th>
                                    <td>{{ $product->created_at->format('H:i:s d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Cập nhật lần cuối</th>
                                    <td>{{ $product->updated_at->format('H:i:s d/m/Y') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Product Features -->
                    @if($product->features)
                    <div class="mb-4">
                        <h5 class="border-bottom pb-2">Tính năng sản phẩm</h5>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($product->features)) !!}
                        </div>
                    </div>
                    @endif
                    
                    <!-- Product Description -->
                    <div class="mb-4">
                        <h5 class="border-bottom pb-2">Mô tả sản phẩm</h5>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Variants -->
            <div class="mt-4">
                <h5 class="border-bottom pb-2">Biến thể sản phẩm <span class="badge bg-info">{{ $product->variants->count() }}</span></h5>
                
                @if($product->variants->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>SKU</th>
                                    <th>Kích cỡ</th>
                                    <th>Màu sắc</th>
                                    <th>Số lượng</th>
                                    <th>Giá thêm</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->variants as $variant)
                                <tr>
                                    <td>{{ $variant->sku }}</td>
                                    <td>{{ $variant->size }}</td>
                                    <td>
                                        <span class="d-inline-block me-2">{{ $variant->color }}</span>
                                    </td>
                                    <td>
                                        @if($variant->stock > 10)
                                            <span class="badge bg-success">{{ $variant->stock }}</span>
                                        @elseif($variant->stock > 0)
                                            <span class="badge bg-warning">{{ $variant->stock }}</span>
                                        @else
                                            <span class="badge bg-danger">Hết hàng</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($variant->additional_price > 0)
                                            +{{ number_format($variant->additional_price) }} đ
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.products.edit', $product->id) }}?focus_variant={{ $variant->id }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Sản phẩm này chưa có biến thể nào.
                    </div>
                @endif
            </div>
            
            <!-- Actions -->
            <div class="mt-4 d-flex gap-2">
                <a href="{{ route('product.show', $product->slug) }}" class="btn btn-info" target="_blank">
                    <i class="fas fa-eye me-1"></i> Xem trang sản phẩm
                </a>
                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Chỉnh sửa sản phẩm
                </a>
                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-1"></i> Xóa sản phẩm
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Enlarge main image when clicking on thumbnails
        const thumbnails = document.querySelectorAll('.product-thumbnails img');
        const mainImage = document.querySelector('.product-main-image img');
        
        if (thumbnails.length > 0 && mainImage) {
            thumbnails.forEach(thumb => {
                thumb.addEventListener('click', function() {
                    mainImage.src = this.src;
                    
                    // Remove border from all thumbnails and add to clicked one
                    thumbnails.forEach(t => t.classList.remove('border', 'border-primary'));
                    this.classList.add('border', 'border-primary');
                });
            });
        }
    });
</script>
@endsection