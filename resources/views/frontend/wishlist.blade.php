@extends('layouts.app')

@section('title', 'Danh sách yêu thích - Sneaker Shop')

@section('content')
<div class="container my-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Tài khoản của tôi</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('profile') }}" class="list-group-item list-group-item-action">Thông tin cá nhân</a>
                    <a href="{{ route('orders') }}" class="list-group-item list-group-item-action">Đơn hàng của tôi</a>
                    <a href="{{ route('wishlist')}}" class="list-group-item list-group-item-action active">Sản phẩm yêu thích</a>
                    <a href="{{ route('logout') }}" class="list-group-item list-group-item-action text-danger" 
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                       Đăng xuất
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header bg-white">
                    <h4 class="mb-0">Danh sách sản phẩm yêu thích</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    @if($wishlistItems->count() > 0)
                        <div class="row">
                            @foreach($wishlistItems as $item)
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100 product-card shadow-sm">
                                        <!-- Remove button -->
                                        <form action="{{ route('wishlist.remove', $item->id) }}" method="POST" class="position-absolute" style="top: 10px; right: 10px; z-index: 10;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle shadow-sm" title="Xóa khỏi danh sách">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                        
                                        <!-- Product image -->
                                        <div class="product-image-wrapper">
                                            @if($item->product->images->isNotEmpty())
                                                @php
                                                    $imagePath = $item->product->images->first()->image_path;
                                                    $isExternalUrl = filter_var($imagePath, FILTER_VALIDATE_URL);
                                                @endphp
                                                <a href="{{ route('product.show', $item->product->slug) }}">
                                                    <img src="{{ $isExternalUrl ? $imagePath : asset('storage/' . $imagePath) }}" 
                                                        class="card-img-top" alt="{{ $item->product->name }}" style="height: 180px; object-fit: contain;">
                                                </a>
                                            @else
                                                <a href="{{ route('product.show', $item->product->slug) }}">
                                                    <img src="https://via.placeholder.com/300x300?text=No+Image" class="card-img-top" alt="{{ $item->product->name }}" style="height: 180px; object-fit: contain;">
                                                </a>
                                            @endif
                                        </div>
                                        
                                        <!-- Product info -->
                                        <div class="card-body">
                                            <h5 class="card-title text-truncate">
                                                <a href="{{ route('product.show', $item->product->slug) }}" class="text-decoration-none text-dark">
                                                    {{ $item->product->name }}
                                                </a>
                                            </h5>
                                            <div class="mb-2">
                                                @if($item->product->brand)
                                                    <span class="badge bg-secondary">{{ $item->product->brand->name }}</span>
                                                @endif
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                @if($item->product->sale_price)
                                                    <div>
                                                        <span class="text-muted text-decoration-line-through">{{ number_format($item->product->price) }} đ</span>
                                                        <br>
                                                        <span class="text-danger fw-bold">{{ number_format($item->product->sale_price) }} đ</span>
                                                    </div>
                                                @else
                                                    <span class="fw-bold">{{ number_format($item->product->price) }} đ</span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <!-- Card footer -->
                                        <div class="card-footer bg-white border-0 d-flex justify-content-between">
                                            <a href="{{ route('product.show', $item->product->slug) }}" class="btn btn-sm btn-outline-primary">Chi tiết</a>
                                            <form action="{{ route('cart.add') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $item->product->id }}">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <p>Bạn chưa có sản phẩm nào trong danh sách yêu thích.</p>
                            <a href="{{ route('shop') }}" class="btn btn-primary mt-3">Tiếp tục mua sắm</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.product-card {
    transition: all 0.3s ease;
    border-radius: 8px;
    overflow: hidden;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}

.product-image-wrapper {
    overflow: hidden;
    position: relative;
    height: 180px;
    background-color: #f8f9fa;
}

.product-image-wrapper img {
    transition: transform 0.3s ease;
}

.product-image-wrapper:hover img {
    transform: scale(1.05);
}

.card-body {
    padding-bottom: 0.5rem;
}

.card-footer {
    padding-top: 0.5rem;
}
</style>
@endsection