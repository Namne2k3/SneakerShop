@extends('layouts.app')

@section('title', 'Cửa hàng - Sneaker Shop')

@section('content')
<div class="container">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Danh mục</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @foreach($categories as $category)
                            <li class="mb-2">
                                <a href="{{ route('category.show', $category->slug) }}" class="text-dark">
                                    {{ $category->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Lọc theo giá</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('shop') }}" method="GET">
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
                <h1>Sản phẩm</h1>
                <div>
                    <select class="form-select" onchange="window.location.href=this.value">
                        <option value="{{ route('shop', ['sort' => 'latest']) }}" {{ request('sort') == 'latest' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="{{ route('shop', ['sort' => 'price_asc']) }}" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá: Thấp đến cao</option>
                        <option value="{{ route('shop', ['sort' => 'price_desc']) }}" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá: Cao đến thấp</option>
                        <option value="{{ route('shop', ['sort' => 'name_asc']) }}" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Tên: A-Z</option>
                        <option value="{{ route('shop', ['sort' => 'name_desc']) }}" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Tên: Z-A</option>
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
                        <p class="text-center">Không có sản phẩm nào.</p>
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
