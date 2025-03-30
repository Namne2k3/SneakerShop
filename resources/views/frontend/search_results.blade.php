@extends('layouts.app')

@section('title', 'Kết quả tìm kiếm "' . $query . '" - Sneaker Shop')

@section('content')
    <!-- Search Results Section -->
    <section class="search-results-section my-5">
        <div class="container">
            <h1 class="mb-4">Kết quả tìm kiếm: "{{ $query }}"</h1>
            
            @if($products->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Không tìm thấy sản phẩm nào phù hợp với từ khóa "{{ $query }}".
                </div>
                
                <div class="text-center my-5">
                    <p>Vui lòng thử tìm kiếm với từ khóa khác hoặc xem các sản phẩm của chúng tôi</p>
                    <a href="{{ route('shop') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-shopping-bag me-2"></i> Xem tất cả sản phẩm
                    </a>
                </div>
            @else
                <p>Tìm thấy {{ $products->total() }} sản phẩm phù hợp</p>
                
                <!-- Products Grid -->
                <div class="row">
                    @foreach($products as $product)
                        <div class="col-md-3 mb-4">
                            <div class="card h-100">
                                @if($product->images->isNotEmpty())
                                    @php
                                        $imagePath = $product->images->first()->image_path;
                                        $isExternalUrl = filter_var($imagePath, FILTER_VALIDATE_URL);
                                    @endphp
                                    <img src="{{ $isExternalUrl ? $imagePath : asset('storage/' . $imagePath) }}" class="card-img-top" alt="{{ $product->name }}">
                                @else
                                    <img src="{{ asset('images/no-image.png') }}" class="card-img-top" alt="No Image">
                                @endif
                                
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">{{ $product->name }}</h5>
                                    
                                    <div class="d-flex align-items-center mb-2">
                                        @if($product->sale_price)
                                            <span class="text-danger me-2">{{ number_format($product->sale_price) }}₫</span>
                                            <span class="text-muted text-decoration-line-through">{{ number_format($product->price) }}₫</span>
                                        @else
                                            <span class="text-danger">{{ number_format($product->price) }}₫</span>
                                        @endif
                                    </div>
                                    
                                    <p class="card-text flex-grow-1">{{ Str::limit($product->description, 100) }}</p>
                                    
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('product.show', $product->slug) }}" class="btn btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i> Chi tiết
                                        </a>
                                        <form action="{{ route('cart.add') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="fas fa-shopping-cart me-1"></i> Thêm vào giỏ
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->appends(['query' => $query])->links() }}
                </div>
            @endif
            
            <!-- Search Tips -->
            <div class="mt-5">
                <h4>Gợi ý tìm kiếm:</h4>
                <ul>
                    <li>Kiểm tra lỗi chính tả</li>
                    <li>Sử dụng từ khóa ngắn gọn và chung hơn</li>
                    <li>Thử tìm kiếm theo tên thương hiệu hoặc danh mục</li>
                </ul>
            </div>
        </div>
    </section>
@endsection