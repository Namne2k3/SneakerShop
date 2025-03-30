@extends('layouts.app')

@section('title', $product->name . ' - Sneaker Shop')

@section('styles')
<style>
    .product-image-container {
        position: relative;
        overflow: hidden;
        margin-bottom: 15px;
    }
    
    .product-main-image {
        width: 100%;
        height: 400px;
        object-fit: contain;
    }
    
    .product-thumbnails {
        display: flex;
        overflow-x: auto;
        gap: 10px;
        margin-bottom: 20px;
    }
    
    .product-thumbnail {
        width: 80px;
        height: 80px;
        object-fit: cover;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.2s;
    }
    
    .product-thumbnail.active {
        border-color: #333;
    }
    
    .size-selector, .color-selector {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
    }
    
    .size-item, .color-item {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .size-item.active, .color-item.active {
        background-color: #333;
        color: #fff;
        border-color: #333;
    }
    
    .quantity-selector {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .quantity-btn {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        border: 1px solid #ddd;
        cursor: pointer;
    }
    
    .quantity-input {
        width: 60px;
        height: 40px;
        text-align: center;
        border: 1px solid #ddd;
        border-left: none;
        border-right: none;
    }
    
    .product-features {
        margin-top: 30px;
    }
    
    .product-features ul {
        padding-left: 20px;
    }
    
    .product-reviews {
        margin-top: 40px;
    }
    
    .review-item {
        padding: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .review-item:last-child {
        border-bottom: none;
    }
    
    .rating {
        color: #ffc107;
        font-size: 1.2rem;
    }
    
    .out-of-stock {
        color: #dc3545;
        font-weight: bold;
    }
    
    /* Heart animation */
    .btn-wishlist {
        transition: all 0.3s ease;
    }
    
    .btn-wishlist.active {
        color: #dc3545;
        border-color: #dc3545;
    }
    
    .btn-wishlist.active i {
        transform: scale(1.2);
    }
    
    .wishlist-icon {
        transition: all 0.3s ease;
    }
</style>
@endsection

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="my-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
            @if($product->categories->isNotEmpty())
                <li class="breadcrumb-item">
                    <a href="{{ route('category.show', $product->categories->first()->slug) }}">
                        {{ $product->categories->first()->name }}
                    </a>
                </li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Product Images -->
        <div class="col-md-6 mb-4">
            <div class="product-image-container">
                @if($product->images->isNotEmpty())
                    @php
                        $mainImage = $product->images->where('is_primary', 1)->first() ?? $product->images->first();
                        $imagePath = $mainImage->image_path;
                        $isExternalUrl = filter_var($imagePath, FILTER_VALIDATE_URL);
                    @endphp
                    <img id="main-product-image" src="{{ $isExternalUrl ? $imagePath : asset('storage/' . $imagePath) }}" 
                        class="product-main-image" alt="{{ $product->name }}">
                @else
                    <img src="https://via.placeholder.com/600x400?text=No+Image" class="product-main-image" alt="{{ $product->name }}">
                @endif
            </div>
            
            @if($product->images->count() > 1)
                <div class="product-thumbnails">
                    @foreach($product->images as $image)
                        @php
                            $thumbPath = $image->image_path;
                            $isThumbExternalUrl = filter_var($thumbPath, FILTER_VALIDATE_URL);
                            $isActive = ($image->is_primary) ? 'active' : '';
                        @endphp
                        <img src="{{ $isThumbExternalUrl ? $thumbPath : asset('storage/' . $thumbPath) }}" 
                            class="product-thumbnail {{ $isActive }}" 
                            data-image="{{ $isThumbExternalUrl ? $thumbPath : asset('storage/' . $thumbPath) }}" 
                            alt="{{ $product->name }}">
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Product Details -->
        <div class="col-md-6">
            <h1 class="mb-3">{{ $product->name }}</h1>
            
            @if($product->brand)
                <p class="mb-3">
                    <strong>Thương hiệu:</strong> 
                    <a href="{{ route('brand.show', $product->brand->slug) }}">{{ $product->brand->name }}</a>
                </p>
            @endif
            
            <div class="mb-3">
                @if($product->sale_price)
                    <span class="text-muted text-decoration-line-through me-2">{{ number_format($product->price) }} đ</span>
                    <span class="product-price text-danger fw-bold">{{ number_format($product->sale_price) }} đ</span>
                @else
                    <span class="product-price fw-bold">{{ number_format($product->price) }} đ</span>
                @endif
            </div>
            
            <div class="mb-4">
                <p>{{ $product->description }}</p>
            </div>

            <form action="{{ route('cart.add') }}" method="POST" id="add-to-cart-form">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                
                @if($product->variants->isNotEmpty())
                    @php
                        $sizes = $product->variants->pluck('size')->unique();
                        $colors = $product->variants->pluck('color')->unique();
                        $selectedVariant = null;
                    @endphp

                    <!-- Size Selection -->
                    @if($sizes->count() > 0)
                        <div class="mb-3">
                            <label class="form-label">Kích thước:</label>
                            <div class="size-selector">
                                @foreach($sizes as $size)
                                    <div class="size-item" data-size="{{ $size }}">{{ $size }}</div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Color Selection -->
                    @if($colors->count() > 0)
                        <div class="mb-3">
                            <label class="form-label">Màu sắc:</label>
                            <div class="color-selector">
                                @foreach($colors as $color)
                                    <div class="color-item" data-color="{{ $color }}">{{ $color }}</div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <input type="hidden" name="variant_id" id="selected-variant">
                    
                    <div id="stock-status" class="mb-3">
                        <span class="text-secondary">Vui lòng chọn kích thước và màu sắc</span>
                    </div>
                @endif
                
                <!-- Quantity -->
                <div class="mb-4">
                    <label class="form-label">Số lượng:</label>
                    <div class="quantity-selector">
                        <div class="quantity-btn" id="decrease-quantity">-</div>
                        <input type="number" name="quantity" id="quantity" class="quantity-input" value="1" min="1">
                        <div class="quantity-btn" id="increase-quantity">+</div>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-block mb-4">
                    <button type="submit" id="add-to-cart-btn" class="btn btn-primary me-md-2">
                        <i class="fas fa-shopping-cart me-2"></i>Thêm vào giỏ
                    </button>
                    <button type="button" id="add-to-wishlist-btn" class="btn btn-outline-secondary {{ $isInWishlist ? 'active' : '' }}" data-product-id="{{ $product->id }}">
                        <i class="wishlist-icon {{ $isInWishlist ? 'fas' : 'far' }} fa-heart me-2"></i>{{ $isInWishlist ? 'Đã yêu thích' : 'Yêu thích' }}
                    </button>
                </div>
            </form>

            @if($product->features)
            <div class="product-features">
                <h5>Đặc điểm sản phẩm:</h5>
                <ul>
                    @foreach(explode(';', $product->features) as $feature)
                        <li>{{ trim($feature) }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>

    <!-- Product Reviews -->
    <div class="row mt-5">
        <div class="col-12">
            <h3>Đánh giá sản phẩm</h3>
            <hr>
            
            <div class="product-reviews">
                @forelse($product->reviews as $review)
                    <div class="review-item">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>{{ $review->user->name }}</strong>
                                <div class="rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                            <small class="text-muted">{{ $review->created_at->format('d/m/Y') }}</small>
                        </div>
                        <p class="mt-2">{{ $review->comment }}</p>
                    </div>
                @empty
                    <p>Chưa có đánh giá nào cho sản phẩm này.</p>
                @endforelse

                @auth
                    <div class="mt-4">
                        <h5>Viết đánh giá của bạn</h5>
                        <form action="{{ route('reviews.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            
                            <div class="mb-3">
                                <label for="rating" class="form-label">Đánh giá:</label>
                                <select class="form-select" name="rating" id="rating">
                                    <option value="5">5 - Xuất sắc</option>
                                    <option value="4">4 - Tốt</option>
                                    <option value="3">3 - Bình thường</option>
                                    <option value="2">2 - Kém</option>
                                    <option value="1">1 - Rất kém</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="comment" class="form-label">Nhận xét:</label>
                                <textarea class="form-control" name="comment" id="comment" rows="3"></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                        </form>
                    </div>
                @else
                    <div class="alert alert-info mt-4">
                        Vui lòng <a href="{{ route('login') }}">đăng nhập</a> để viết đánh giá.
                    </div>
                @endauth
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
    <div class="row mt-5">
        <div class="col-12">
            <h3>Sản phẩm liên quan</h3>
            <hr>
            
            <div class="row">
                @foreach($relatedProducts as $relatedProduct)
                    <div class="col-md-3 mb-4">
                        <div class="card h-100">
                            @if($relatedProduct->images->isNotEmpty())
                                @php
                                    $relatedImagePath = $relatedProduct->images->first()->image_path;
                                    $isRelatedExternalUrl = filter_var($relatedImagePath, FILTER_VALIDATE_URL);
                                @endphp
                                <img src="{{ $isRelatedExternalUrl ? $relatedImagePath : asset('storage/' . $relatedImagePath) }}" 
                                    class="card-img-top" alt="{{ $relatedProduct->name }}">
                            @else
                                <img src="https://via.placeholder.com/300x300?text=No+Image" class="card-img-top" alt="{{ $relatedProduct->name }}">
                            @endif
                            
                            <div class="card-body">
                                <h5 class="card-title">{{ $relatedProduct->name }}</h5>
                                <div class="d-flex justify-content-between align-items-center">
                                    @if($relatedProduct->sale_price)
                                        <div>
                                            <span class="text-muted text-decoration-line-through">{{ number_format($relatedProduct->price) }} đ</span>
                                            <span class="text-danger fw-bold">{{ number_format($relatedProduct->sale_price) }} đ</span>
                                        </div>
                                    @else
                                        <span class="fw-bold">{{ number_format($relatedProduct->price) }} đ</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="card-footer bg-white border-0 d-flex justify-content-between">
                                <a href="{{ route('product.show', $relatedProduct->slug) }}" class="btn btn-sm btn-outline-primary">Chi tiết</a>
                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $relatedProduct->id }}">
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
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Image thumbnail handling
        $('.product-thumbnail').click(function() {
            let newImageSrc = $(this).data('image');
            $('#main-product-image').attr('src', newImageSrc);
            $('.product-thumbnail').removeClass('active');
            $(this).addClass('active');
        });

        // Quantity selector
        $('#increase-quantity').click(function() {
            let currentVal = parseInt($('#quantity').val());
            $('#quantity').val(currentVal + 1);
            updateAddToCartButton();
        });

        $('#decrease-quantity').click(function() {
            let currentVal = parseInt($('#quantity').val());
            if (currentVal > 1) {
                $('#quantity').val(currentVal - 1);
                updateAddToCartButton();
            }
        });

        $('#quantity').on('change', function() {
            let currentVal = parseInt($(this).val());
            if (currentVal < 1) {
                $(this).val(1);
            }
            updateAddToCartButton();
        });

        // Variant selection
        let selectedSize = '';
        let selectedColor = '';
        let variants = {!! json_encode($product->variants) !!};
        
        $('.size-item').click(function() {
            $('.size-item').removeClass('active');
            $(this).addClass('active');
            selectedSize = $(this).data('size');
            updateSelectedVariant();
        });

        $('.color-item').click(function() {
            $('.color-item').removeClass('active');
            $(this).addClass('active');
            selectedColor = $(this).data('color');
            updateSelectedVariant();
        });

        function updateSelectedVariant() {
            if (selectedSize && selectedColor) {
                let variant = variants.find(v => v.size === selectedSize && v.color === selectedColor);
                
                if (variant) {
                    $('#selected-variant').val(variant.id);
                    
                    if (variant.stock > 0) {
                        $('#stock-status').html(`<span class="text-success">Còn hàng (${variant.stock} sản phẩm)</span>`);
                        $('#add-to-cart-btn').prop('disabled', false);
                    } else {
                        $('#stock-status').html('<span class="out-of-stock">Hết hàng</span>');
                        $('#add-to-cart-btn').prop('disabled', true);
                    }
                    
                    // Update price if variant has additional price
                    let basePrice = {{ $product->sale_price ?? $product->price }};
                    if (variant.additional_price > 0) {
                        let totalPrice = basePrice + variant.additional_price;
                        $('.product-price').text(numberFormat(totalPrice) + ' đ');
                    } else {
                        $('.product-price').text(numberFormat(basePrice) + ' đ');
                    }
                } else {
                    $('#stock-status').html('<span class="text-danger">Không có phiên bản phù hợp</span>');
                    $('#add-to-cart-btn').prop('disabled', true);
                }
            }
        }

        function updateAddToCartButton() {
            if (variants.length > 0 && !$('#selected-variant').val()) {
                $('#add-to-cart-btn').prop('disabled', true);
            }
        }

        // Format number with commas
        function numberFormat(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        
        // Initial disable if we have variants
        if (variants.length > 0) {
            $('#add-to-cart-btn').prop('disabled', true);
        }

        // Wishlist functionality
        $('#add-to-wishlist-btn').click(function() {
            const productId = $(this).data('product-id');
            const button = $(this);
            
            $.ajax({
                url: "{{ route('wishlist.toggle') }}",
                type: "POST",
                data: {
                    product_id: productId,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.status === 'success') {
                        if (response.action === 'added') {
                            button.addClass('active');
                            button.find('.wishlist-icon').removeClass('far').addClass('fas');
                            button.html('<i class="wishlist-icon fas fa-heart me-2"></i>Đã yêu thích');
                            toastr.success(response.message);
                        } else {
                            button.removeClass('active');
                            button.find('.wishlist-icon').removeClass('fas').addClass('far');
                            button.html('<i class="wishlist-icon far fa-heart me-2"></i>Yêu thích');
                            toastr.info(response.message);
                        }
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 401) {
                        // Người dùng chưa đăng nhập
                        window.location.href = xhr.responseJSON.redirect;
                    } else {
                        toastr.error('Có lỗi xảy ra. Vui lòng thử lại sau.');
                    }
                }
            });
        });
    });
</script>
@endsection