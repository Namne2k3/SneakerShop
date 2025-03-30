@extends('layouts.admin')

@section('title', 'Chi tiết danh mục - Sneaker Shop Admin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Chi tiết danh mục</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Danh mục</a></li>
        <li class="breadcrumb-item active">Chi tiết</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-info-circle me-1"></i>
                Thông tin danh mục
            </div>
            <div>
                <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit me-1"></i> Chỉnh sửa
                </a>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-list me-1"></i> Danh sách danh mục
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <!-- Category Stats -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-chart-bar me-1"></i>
                            Thống kê
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-box me-2"></i>
                                        Sản phẩm
                                    </div>
                                    <span class="badge bg-primary rounded-pill">{{ $category->products->count() }}</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-folder me-2"></i>
                                        Danh mục con
                                    </div>
                                    <span class="badge bg-secondary rounded-pill">{{ $category->children->count() }}</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-clock me-2"></i>
                                        Ngày tạo
                                    </div>
                                    <span>{{ $category->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-edit me-2"></i>
                                        Cập nhật lần cuối
                                    </div>
                                    <span>{{ $category->updated_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hierarchy Info -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-sitemap me-1"></i>
                            Cấu trúc phân cấp
                        </div>
                        <div class="card-body">
                            <h6>Danh mục cha:</h6>
                            @if($category->parent)
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-level-up-alt me-2"></i>
                                    <a href="{{ route('admin.categories.show', $category->parent->id) }}" class="text-decoration-none">
                                        {{ $category->parent->name }}
                                    </a>
                                </div>
                            @else
                                <p class="text-muted mb-3">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Đây là danh mục gốc (không có danh mục cha)
                                </p>
                            @endif

                            <h6 class="mt-3">Danh mục con:</h6>
                            @if($category->children->count() > 0)
                                <ul class="list-group">
                                    @foreach($category->children as $childCategory)
                                        <li class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <a href="{{ route('admin.categories.show', $childCategory->id) }}" class="text-decoration-none">
                                                    <i class="fas fa-level-down-alt me-1"></i>
                                                    {{ $childCategory->name }}
                                                </a>
                                                <div>
                                                    <span class="badge bg-info">{{ $childCategory->products->count() }} sản phẩm</span>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Chưa có danh mục con nào
                                </p>
                            @endif
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
                                        <td>{{ $category->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tên danh mục</th>
                                        <td>{{ $category->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Slug</th>
                                        <td>{{ $category->slug }}</td>
                                    </tr>
                                    <tr>
                                        <th>Mô tả</th>
                                        <td>
                                            @if($category->description)
                                                {!! nl2br(e($category->description)) !!}
                                            @else
                                                <span class="text-muted">Không có mô tả</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Category Products -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-box me-1"></i>
                                Sản phẩm thuộc danh mục này
                            </div>
                            <a href="{{ route('admin.products.index') }}?category={{ $category->id }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-search me-1"></i> Xem tất cả
                            </a>
                        </div>
                        <div class="card-body">
                            @if($category->products->count() > 0)
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
                                            @foreach($category->products->take(5) as $product)
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
                                @if($category->products->count() > 5)
                                    <div class="text-center mt-3">
                                        <a href="{{ route('admin.products.index') }}?category={{ $category->id }}" class="btn btn-outline-primary">
                                            <i class="fas fa-plus me-1"></i> Xem tất cả {{ $category->products->count() }} sản phẩm
                                        </a>
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle me-1"></i> Chưa có sản phẩm nào thuộc danh mục này.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-4 d-flex justify-content-between">
                <div>
                    <a href="{{ route('category.show', $category->slug) }}" class="btn btn-info" target="_blank">
                        <i class="fas fa-eye me-1"></i> Xem trang danh mục
                    </a>
                </div>
                <div>
                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i> Chỉnh sửa
                    </a>
                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline ms-2" 
                          onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này? Tất cả danh mục con sẽ trở thành danh mục gốc. Không thể xóa danh mục có sản phẩm.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" {{ $category->products->count() > 0 ? 'disabled' : '' }}>
                            <i class="fas fa-trash-alt me-1"></i> Xóa danh mục
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection