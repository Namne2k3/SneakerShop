@extends('layouts.app')

@section('title', 'Trang chủ - Sneaker Shop')

@section('content')
    <!-- Hero Section -->
    <section class="hero-section mb-5">
        <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="https://wallpapers.com/images/featured/sneaker-background-nln3t3z56a1d7bfe.jpg" class="d-block w-100" alt="Slide 1">
                    <div class="carousel-caption d-none d-md-block">
                        <h2>Bộ sưu tập mới nhất</h2>
                        <p>Khám phá những mẫu giày sneaker mới nhất</p>
                        <a href="{{ route('shop') }}" class="btn btn-primary">Mua ngay</a>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="https://wallpapers.com/images/high/sneaker-jordan-3-cracking-ajwmzymyvvfi9ttc.webp" class="d-block w-100" alt="Slide 2">
                    <div class="carousel-caption d-none d-md-block">
                        <h2>Khuyến mãi sốc</h2>
                        <p>Giảm giá lên đến 50% các sản phẩm hot</p>
                        <a href="{{ route('shop') }}" class="btn btn-danger">Xem ngay</a>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="https://wallpapers.com/images/high/sneaker-630-x-1280-background-m9l7pu6stih9yfr3.webp" class="d-block w-100" alt="Slide 3">
                    <div class="carousel-caption d-none d-md-block">
                        <h2>Phiên bản giới hạn</h2>
                        <p>Những mẫu giày độc đáo chỉ có tại Sneaker Shop</p>
                        <a href="{{ route('shop') }}" class="btn btn-success">Khám phá</a>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories-section mb-5">
        <div class="container">
            <h2 class="section-title text-center mb-4">Danh mục sản phẩm</h2>
            <div class="row">
                @forelse($categories as $category)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <h5 class="card-title">{{ $category->name }}</h5>
                                <p class="card-text">{{ Str::limit($category->description, 100) }}</p>
                                <a href="{{ route('category.show', $category->slug) }}" class="btn btn-outline-primary">Xem danh mục</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-center">Không có danh mục nào.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section class="featured-products-section mb-5">
        <div class="container">
            <h2 class="section-title text-center mb-4">Sản phẩm nổi bật</h2>
            <div class="row">
                @forelse($featuredProducts as $product)
                    <div class="col-md-3 mb-4">
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
                        <p class="text-center">Không có sản phẩm nổi bật nào.</p>
                    </div>
                @endforelse
            </div>
            <div class="text-center mt-4">
                <a href="{{ route('shop') }}" class="btn btn-primary">Xem tất cả sản phẩm</a>
            </div>
        </div>
    </section>
@endsection
