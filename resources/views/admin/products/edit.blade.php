@extends('layouts.admin')

@section('title', 'Chỉnh sửa sản phẩm - Admin Sneaker Shop')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Chỉnh sửa sản phẩm</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Sản phẩm</a></li>
        <li class="breadcrumb-item active">Chỉnh sửa sản phẩm</li>
    </ol>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="card mb-4">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general-pane" type="button" role="tab" aria-controls="general-pane" aria-selected="true">Thông tin chung</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="images-tab" data-bs-toggle="tab" data-bs-target="#images-pane" type="button" role="tab" aria-controls="images-pane" aria-selected="false">Hình ảnh</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="variants-tab" data-bs-toggle="tab" data-bs-target="#variants-pane" type="button" role="tab" aria-controls="variants-pane" aria-selected="false">Biến thể & Kho hàng</button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- Tab thông tin chung -->
                    <div class="tab-pane fade show active" id="general-pane" role="tabpanel" aria-labelledby="general-tab" tabindex="0">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Mô tả <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="description" name="description" rows="5" required>{{ old('description', $product->description) }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="features" class="form-label">Tính năng / Đặc điểm</label>
                                    <textarea class="form-control" id="features" name="features" rows="3">{{ old('features', $product->features) }}</textarea>
                                    <div class="form-text">Nhập các tính năng nổi bật của sản phẩm, mỗi tính năng cách nhau bằng dấu chấm phẩy (;)</div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="brand_id" class="form-label">Thương hiệu <span class="text-danger">*</span></label>
                                    <select class="form-select" id="brand_id" name="brand_id" required>
                                        <option value="">-- Chọn thương hiệu --</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}" {{ (old('brand_id', $product->brand_id) == $brand->id) ? 'selected' : '' }}>{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="categories" class="form-label">Danh mục <span class="text-danger">*</span></label>
                                    <select class="form-select" id="categories" name="categories[]" multiple required>
                                        @foreach($categories->where('parent_id', null) as $parent)
                                            <option value="{{ $parent->id }}" {{ in_array($parent->id, old('categories', $product->categories->pluck('id')->toArray())) ? 'selected' : '' }}>{{ $parent->name }}</option>
                                            @foreach($parent->children as $child)
                                                <option value="{{ $child->id }}" {{ in_array($child->id, old('categories', $product->categories->pluck('id')->toArray())) ? 'selected' : '' }}>&nbsp;&nbsp;-- {{ $child->name }}</option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                    <div class="form-text">Giữ Ctrl để chọn nhiều danh mục</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="price" class="form-label">Giá gốc (VNĐ) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="price" name="price" value="{{ old('price', $product->price) }}" required min="0" step="1000">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="sale_price" class="form-label">Giá bán (VNĐ)</label>
                                    <input type="number" class="form-control" id="sale_price" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" min="0" step="1000">
                                    <div class="form-text">Để trống nếu không giảm giá</div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="featured" name="featured" value="1" {{ old('featured', $product->featured) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="featured">Sản phẩm nổi bật</label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="active" name="active" value="1" {{ old('active', $product->active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="active">Hiển thị sản phẩm</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tab hình ảnh -->
                    <div class="tab-pane fade" id="images-pane" role="tabpanel" aria-labelledby="images-tab" tabindex="0">
                        <div class="mb-3">
                            <label for="new_images" class="form-label">Thêm hình ảnh mới</label>
                            <input class="form-control" type="file" id="new_images" name="new_images[]" multiple accept="image/*">
                            <div class="form-text">Có thể chọn nhiều hình ảnh (giữ Ctrl). Hình ảnh đầu tiên sẽ là ảnh chính nếu chưa có ảnh chính.</div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="row" id="image-preview"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Hình ảnh hiện tại</label>
                            @if($product->images->isNotEmpty())
                                <div class="row">
                                    @foreach($product->images as $image)
                                        <div class="col-md-3 col-sm-4 mb-3">
                                            <div class="card h-100">
                                                <img src="{{ asset('storage/' . $image->image_path) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 150px; object-fit: contain;">
                                                <div class="card-body p-2">
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="radio" name="primary_image" id="primary_image_{{ $image->id }}" value="{{ $image->id }}" {{ $image->is_primary ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="primary_image_{{ $image->id }}">
                                                            Ảnh chính
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="remove_images[]" id="remove_image_{{ $image->id }}" value="{{ $image->id }}">
                                                        <label class="form-check-label text-danger" for="remove_image_{{ $image->id }}">
                                                            Xóa ảnh
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-info">
                                    Sản phẩm chưa có hình ảnh
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Tab biến thể & kho hàng -->
                    <div class="tab-pane fade" id="variants-pane" role="tabpanel" aria-labelledby="variants-tab" tabindex="0">
                        <div class="mb-3">
                            <button type="button" class="btn btn-success" id="add-variant-btn">
                                <i class="fas fa-plus"></i> Thêm biến thể mới
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="variants-table">
                                <thead>
                                    <tr>
                                        <th>Kích thước</th>
                                        <th>Màu sắc</th>
                                        <th>Mã SKU</th>
                                        <th>Số lượng</th>
                                        <th>Giá thêm</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($product->variants->isNotEmpty())
                                        @foreach($product->variants as $index => $variant)
                                            <tr class="variant-row">
                                                <td>
                                                    <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">
                                                    <input type="text" class="form-control" name="variants[{{ $index }}][size]" value="{{ $variant->size }}" required>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="variants[{{ $index }}][color]" value="{{ $variant->color }}" required>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="variants[{{ $index }}][sku]" value="{{ $variant->sku }}" required>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control" name="variants[{{ $index }}][stock]" value="{{ $variant->stock }}" required min="0">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control" name="variants[{{ $index }}][additional_price]" value="{{ $variant->additional_price }}" min="0" step="1000" required>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger remove-variant-btn">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <input type="hidden" name="remove_variants[]" value="" disabled>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr id="no-variants-row">
                                            <td colspan="6" class="text-center">Chưa có biến thể nào</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Lưu sản phẩm
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Hủy
                </a>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý thêm biến thể mới
        let variantIndex = {{ $product->variants->count() }};
        const addVariantBtn = document.getElementById('add-variant-btn');
        const variantsTable = document.getElementById('variants-table');
        const noVariantsRow = document.getElementById('no-variants-row');
        
        addVariantBtn.addEventListener('click', function() {
            if (noVariantsRow) {
                noVariantsRow.remove();
            }
            
            const newRow = document.createElement('tr');
            newRow.className = 'variant-row';
            newRow.innerHTML = `
                <td>
                    <input type="text" class="form-control" name="variants[new_${variantIndex}][size]" required>
                </td>
                <td>
                    <input type="text" class="form-control" name="variants[new_${variantIndex}][color]" required>
                </td>
                <td>
                    <input type="text" class="form-control" name="variants[new_${variantIndex}][sku]" required>
                </td>
                <td>
                    <input type="number" class="form-control" name="variants[new_${variantIndex}][stock]" value="0" required min="0">
                </td>
                <td>
                    <input type="number" class="form-control" name="variants[new_${variantIndex}][additional_price]" value="0" min="0" step="1000" required>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger remove-variant-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            
            variantsTable.querySelector('tbody').appendChild(newRow);
            variantIndex++;
            
            // Gắn sự kiện cho nút xóa mới
            const removeBtn = newRow.querySelector('.remove-variant-btn');
            removeBtn.addEventListener('click', removeVariant);
        });
        
        // Xử lý xóa biến thể
        const removeVariantBtns = document.querySelectorAll('.remove-variant-btn');
        removeVariantBtns.forEach(function(btn) {
            btn.addEventListener('click', removeVariant);
        });
        
        function removeVariant() {
            const row = this.closest('.variant-row');
            const variantIdInput = row.querySelector('input[name^="variants"][name$="[id]"]');
            
            if (variantIdInput) {
                // Nếu là variant đã tồn tại, thêm vào danh sách xóa
                const removeInput = row.querySelector('input[name="remove_variants[]"]');
                removeInput.value = variantIdInput.value;
                removeInput.disabled = false;
                row.style.display = 'none'; // Ẩn dòng thay vì xóa
            } else {
                // Nếu là variant mới, xóa luôn
                row.remove();
            }
            
            // Kiểm tra nếu không còn variant nào, hiển thị thông báo
            const visibleRows = document.querySelectorAll('.variant-row:not([style*="display: none"])');
            if (visibleRows.length === 0) {
                const tbody = variantsTable.querySelector('tbody');
                const noVariantRow = document.createElement('tr');
                noVariantRow.id = 'no-variants-row';
                noVariantRow.innerHTML = '<td colspan="6" class="text-center">Chưa có biến thể nào</td>';
                tbody.appendChild(noVariantRow);
            }
        }
        
        // Xem trước hình ảnh mới
        const newImagesInput = document.getElementById('new_images');
        const imagePreview = document.getElementById('image-preview');
        
        newImagesInput.addEventListener('change', function() {
            imagePreview.innerHTML = '';
            
            if (this.files) {
                Array.from(this.files).forEach((file, index) => {
                    if (!file.type.match('image.*')) {
                        return;
                    }
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const col = document.createElement('div');
                        col.className = 'col-md-3 col-sm-4 mb-3';
                        
                        const card = document.createElement('div');
                        card.className = 'card h-100';
                        
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'card-img-top';
                        img.style.height = '150px';
                        img.style.objectFit = 'contain';
                        img.alt = 'Preview';
                        
                        const cardBody = document.createElement('div');
                        cardBody.className = 'card-body p-2';
                        
                        const text = document.createElement('p');
                        text.className = 'card-text text-center mb-0';
                        text.textContent = 'Ảnh mới ' + (index + 1);
                        
                        cardBody.appendChild(text);
                        card.appendChild(img);
                        card.appendChild(cardBody);
                        col.appendChild(card);
                        imagePreview.appendChild(col);
                    };
                    
                    reader.readAsDataURL(file);
                });
            }
        });
    });
</script>
@endpush