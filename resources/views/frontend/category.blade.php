@extends('layouts.app')

@section('title', $category->name . ' - Sneaker Shop')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="my-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
            @if($category->parent)
                <li class="breadcrumb-item">
                    <a href="{{ route('category.show', $category->parent->slug) }}">{{ $category->parent->name }}</a>
                </li>
            @endif
            <li class="breadcrumb-item active">{{ $category->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">{{ $category->name }}</h5>
                </div>
                <div class="card-body">
                    <p>{{ $category->description }}</p>
                    
                    @if($category->children->isNotEmpty())
                        <hr>
                        <h6>Danh mục con:</h6>
                        <ul class="list-unstyled">
                            @foreach($category->children as $child)
                                <li class="mb-2">
                                    <a href="{{ route('category.show', $child->slug) }}" class="text-dark">
                                        {{ $child->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Lọc theo giá</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('category.show', $category->slug) }}" method="GET">
                        <div class="mb-3">
                            <label for="min_price" class="form-label">Giá tối thiểu</label>
                            <input type="number" class="form-control" id="min_price" name="min_price" value="{{ request('min_price') }}">
                        </div>
                        <div class="mb-3">
                            <label for="max_price" class="form-label">Giá tối đa</label>
                            <input type="number" class="form-control" id="max_price" name="max_price" value="{{ request('max_price') }}">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Áp dụng</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>{{ $category->name }}</h1>
                <div>
                    <select class="form-select" onchange="window.location.href=this.value">
                        <option value="{{ route('category.show', $category->slug) }}" {{ !request('sort') ? 'selected' : '' }}>Sắp xếp</option>
                        <option value="{{ route('category.show', ['slug' => $category->slug, 'sort' => 'latest']) }}" {{ request('sort') == 'latest' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="{{ route('category.show', ['slug' => $category->slug, 'sort' => 'price_asc']) }}" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá: Thấp đến cao</option>
                        <option value="{{ route('category.show', ['slug' => $category->slug, 'sort' => 'price_desc']) }}" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá: Cao đến thấp</option>
                        <option value="{{ route('category.show', ['slug' => $category->slug, 'sort' => 'name_asc']) }}" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Tên: A-Z</option>
                        <option value="{{ route('category.show', ['slug' => $category->slug, 'sort' => 'name_desc']) }}" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Tên: Z-A</option>
                    </select>
                </div>
            </div>

            <div class="row">
                @forelse ($products as $product)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            @if($product->images->isNotEmpty())
                                @php
                                    $imagePath = $product->images->first()->image_path;
                                    $isExternalUrl = filter_var($imagePath, FILTER_VALIDATE_URL);
                                @endphp
                                <img src="{{ $isExternalUrl ? $imagePath : asset('storage/' . $imagePath) }}" class="card-img-top" alt="{{ $product->name }}">
                            @else
                                <img src="https://via.placeholder.com/300x300?text=No+Image" class="card-img-top" alt="{{ $product->name }}">
                            @endif
                            <div class="card-body">
                                <h5 class="card-title">{{ $product->name }}</h5>
                                <div class="d-flex justify-content-between align-items-center">
                                    @if($product->sale_price)
                                        <div>
                                            <span class="text-muted text-decoration-line-through">{{ number_format($product->price) }} đ</span>
                                            <span class="text-danger fw-bold">{{ number_format($product->sale_price) }} đ</span>
                                        </div>
                                    @else
                                        <span class="fw-bold">{{ number_format($product->price) }} đ</span>
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer bg-white border-0 d-flex justify-content-between">
                                <a href="{{ route('product.show', $product->slug) }}" class="btn btn-sm btn-outline-primary">Chi tiết</a>
                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">
                            Không có sản phẩm nào trong danh mục này.
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Handle active filter
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('min_price') || urlParams.has('max_price')) {
            $('.filter-card').addClass('active');
        }
    });
</script>
@endsection