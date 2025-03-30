@extends('layouts.admin')

@section('title', 'Thêm sản phẩm mới - Sneaker Shop Admin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Thêm sản phẩm mới</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Sản phẩm</a></li>
        <li class="breadcrumb-item active">Thêm mới</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-plus-circle me-1"></i>
            Thông tin sản phẩm
        </div>
        <div class="card-body">
            @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong><i class="fas fa-exclamation-triangle me-1"></i>Lỗi!</strong> Vui lòng kiểm tra lại thông tin.
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <ul class="nav nav-tabs mb-3" id="productTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details-tab-pane" type="button" role="tab" aria-controls="details-tab-pane" aria-selected="true">
                            Chi tiết
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="images-tab" data-bs-toggle="tab" data-bs-target="#images-tab-pane" type="button" role="tab" aria-controls="images-tab-pane" aria-selected="false">
                            Hình ảnh
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="variants-tab" data-bs-toggle="tab" data-bs-target="#variants-tab-pane" type="button" role="tab" aria-controls="variants-tab-pane" aria-selected="false">
                            Biến thể
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo-tab-pane" type="button" role="tab" aria-controls="seo-tab-pane" aria-selected="false">
                            SEO & Khác
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="productTabContent">
                    <!-- Tab Chi tiết sản phẩm -->
                    <div class="tab-pane fade show active" id="details-tab-pane" role="tabpanel" aria-labelledby="details-tab" tabindex="0">
                        <div class="row mt-3">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Slug sẽ được tự động tạo từ tên sản phẩm</small>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Mô tả chi tiết <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="features" class="form-label">Đặc điểm sản phẩm</label>
                                    <textarea class="form-control @error('features') is-invalid @enderror" id="features" name="features" rows="3" placeholder="Các đặc điểm được phân cách bằng dấu chấm phẩy (;)">{{ old('features') }}</textarea>
                                    @error('features')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card mb-3">
                                    <div class="card-header">Phân loại</div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="brand_id" class="form-label">Thương hiệu <span class="text-danger">*</span></label>
                                            <select class="form-select @error('brand_id') is-invalid @enderror" id="brand_id" name="brand_id" required>
                                                <option value="">-- Chọn thương hiệu --</option>
                                                @foreach($brands as $brand)
                                                    <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('brand_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="categories" class="form-label">Danh mục <span class="text-danger">*</span></label>
                                            <select class="form-select @error('categories') is-invalid @enderror" id="categories" name="categories[]" multiple required>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ (is_array(old('categories')) && in_array($category->id, old('categories'))) ? 'selected' : '' }}>
                                                        @if($category->parent_id)
                                                            {{ $category->parent->name }} &raquo; {{ $category->name }}
                                                        @else
                                                            {{ $category->name }}
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('categories')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Giữ phím Ctrl để chọn nhiều danh mục</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-3">
                                    <div class="card-header">Giá & Trạng thái</div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="price" class="form-label">Giá <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" min="0" step="1000" required>
                                                <span class="input-group-text">VNĐ</span>
                                            </div>
                                            @error('price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="sale_price" class="form-label">Giá khuyến mãi</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control @error('sale_price') is-invalid @enderror" id="sale_price" name="sale_price" value="{{ old('sale_price') }}" min="0" step="1000">
                                                <span class="input-group-text">VNĐ</span>
                                            </div>
                                            @error('sale_price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="featured" name="featured" value="1" {{ old('featured') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="featured">Sản phẩm nổi bật</label>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="active" name="active" value="1" {{ old('active', 1) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="active">Hiển thị sản phẩm</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Hình ảnh -->
                    <div class="tab-pane fade" id="images-tab-pane" role="tabpanel" aria-labelledby="images-tab" tabindex="0">
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="images" class="form-label">Hình ảnh sản phẩm <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control @error('images.*') is-invalid @enderror" id="images" name="images[]" accept="image/jpeg,image/png,image/jpg,image/gif" multiple>
                                    @error('images.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Hình đầu tiên sẽ là hình chính. Chỉ hỗ trợ file: JPG, JPEG, PNG, GIF. Có thể chọn nhiều hình (giữ Ctrl).</small>
                                </div>

                                <div class="row" id="image-preview-container"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Biến thể -->
                    <div class="tab-pane fade" id="variants-tab-pane" role="tabpanel" aria-labelledby="variants-tab" tabindex="0">
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between mb-3">
                                    <h5>Biến thể sản phẩm</h5>
                                    <button type="button" class="btn btn-success" id="add-variant-btn">
                                        <i class="fas fa-plus me-1"></i> Thêm biến thể
                                    </button>
                                </div>
                                <p class="text-muted mb-3">Thêm các biến thể cho sản phẩm (kích thước, màu sắc, v.v.)</p>

                                <div id="variants-container">
                                    <!-- Biến thể sẽ được thêm vào đây bằng JavaScript -->
                                </div>
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle me-1"></i> Nếu không thêm biến thể, sản phẩm sẽ được bán như một mặt hàng đơn.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab SEO & Khác -->
                    <div class="tab-pane fade" id="seo-tab-pane" role="tabpanel" aria-labelledby="seo-tab" tabindex="0">
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-1"></i> Các thông tin SEO sẽ được tự động tạo từ tên sản phẩm và mô tả nếu bạn không điền.
                                </div>
                                <div class="mb-3">
                                    <label for="meta_title" class="form-label">Meta Title</label>
                                    <input type="text" class="form-control" id="meta_title" name="meta_title" value="{{ old('meta_title') }}">
                                </div>
                                <div class="mb-3">
                                    <label for="meta_description" class="form-label">Meta Description</label>
                                    <textarea class="form-control" id="meta_description" name="meta_description" rows="3">{{ old('meta_description') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                    <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords') }}">
                                    <small class="text-muted">Phân cách các từ khóa bằng dấu phẩy</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Lưu sản phẩm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý xem trước hình ảnh
        const imagesInput = document.getElementById('images');
        const previewContainer = document.getElementById('image-preview-container');
        
        imagesInput.addEventListener('change', function() {
            previewContainer.innerHTML = '';
            
            if (this.files && this.files.length > 0) {
                let validFiles = true;
                
                // Validate file types before preview
                Array.from(this.files).forEach((file) => {
                    if (!file.type.match('image/(jpeg|jpg|png|gif)')) {
                        alert('Chỉ chấp nhận các file hình ảnh: JPG, JPEG, PNG, GIF');
                        this.value = ''; // Clear the input
                        previewContainer.innerHTML = '';
                        validFiles = false;
                        return;
                    }
                });
                
                if (!validFiles) return;
                
                // Create previews for valid files
                Array.from(this.files).forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const col = document.createElement('div');
                        col.className = 'col-md-3 mb-3';
                        
                        const card = document.createElement('div');
                        card.className = 'card';
                        
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'card-img-top';
                        img.style.height = '150px';
                        img.style.objectFit = 'cover';
                        
                        const cardBody = document.createElement('div');
                        cardBody.className = 'card-body p-2 text-center';
                        
                        const primaryBadge = document.createElement('span');
                        primaryBadge.className = index === 0 ? 'badge bg-primary' : 'badge bg-secondary';
                        primaryBadge.textContent = index === 0 ? 'Hình chính' : `Hình #${index + 1}`;
                        
                        cardBody.appendChild(primaryBadge);
                        card.appendChild(img);
                        card.appendChild(cardBody);
                        col.appendChild(card);
                        previewContainer.appendChild(col);
                    };
                    
                    reader.readAsDataURL(file);
                });
            }
        });
        
        // Xử lý biến thể sản phẩm
        const variantsContainer = document.getElementById('variants-container');
        const addVariantBtn = document.getElementById('add-variant-btn');
        let variantCount = 0;
        
        addVariantBtn.addEventListener('click', function() {
            addVariant();
        });
        
        function addVariant(data = {}) {
            const variantId = Date.now();
            const variantHtml = `
                <div class="card mb-3 variant-card" data-variant-id="${variantId}">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Biến thể #${++variantCount}</span>
                        <button type="button" class="btn btn-sm btn-danger remove-variant-btn">
                            <i class="fas fa-times"></i> Xóa
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Kích cỡ <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="variants[${variantId}][size]" value="${data.size || ''}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Màu sắc <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="variants[${variantId}][color]" value="${data.color || ''}" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label class="form-label">SKU <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="variants[${variantId}][sku]" value="${data.sku || ''}" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label class="form-label">Số lượng <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="variants[${variantId}][stock]" value="${data.stock || '10'}" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label class="form-label">Giá thêm</label>
                                    <input type="number" class="form-control" name="variants[${variantId}][additional_price]" value="${data.additional_price || '0'}" min="0" step="1000">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            variantsContainer.insertAdjacentHTML('beforeend', variantHtml);
            
            // Thêm xử lý sự kiện cho nút xóa biến thể
            const newVariant = variantsContainer.querySelector(`.variant-card[data-variant-id="${variantId}"]`);
            const removeBtn = newVariant.querySelector('.remove-variant-btn');
            
            removeBtn.addEventListener('click', function() {
                newVariant.remove();
                updateVariantNumbers();
            });
        }
        
        function updateVariantNumbers() {
            variantCount = 0;
            variantsContainer.querySelectorAll('.variant-card').forEach(card => {
                card.querySelector('.card-header span').textContent = `Biến thể #${++variantCount}`;
            });
        }
    });
</script>
@endsection