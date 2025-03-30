@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3>Chỉnh sửa sản phẩm
                    <a href="{{ url('admin/products') }}" class="btn btn-danger text-white btn-sm float-end">
                        QUAY LẠI
                    </a>
                </h3>
            </div>
            <div class="card-body">
                @if ($errors->any())
                <div class="alert alert-warning">
                    @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                    @endforeach
                </div>
                @endif

                <form action="{{ url('admin/products/'.$product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details-tab-pane" type="button" role="tab" aria-controls="details-tab-pane" aria-selected="true">
                                Chi tiết
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="image-tab" data-bs-toggle="tab" data-bs-target="#image-tab-pane" type="button" role="tab" aria-controls="image-tab-pane" aria-selected="false">
                                Hình ảnh
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pricing-tab" data-bs-toggle="tab" data-bs-target="#pricing-tab-pane" type="button" role="tab" aria-controls="pricing-tab-pane" aria-selected="false">
                                Giá & Kho hàng
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <!-- Tab Chi tiết sản phẩm -->
                        <div class="tab-pane fade show active" id="details-tab-pane" role="tabpanel" aria-labelledby="details-tab" tabindex="0">
                            <div class="mb-3 mt-3">
                                <label>Danh mục</label>
                                <select name="category_id" class="form-control">
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $category->id == $product->category_id ? 'selected':'' }}>
                                        {{ $category->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Thương hiệu</label>
                                <select name="brand_id" class="form-control">
                                    @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ $brand->id == $product->brand_id ? 'selected':'' }}>
                                        {{ $brand->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Tên sản phẩm</label>
                                <input type="text" name="name" value="{{ $product->name }}" class="form-control" />
                            </div>
                            <div class="mb-3">
                                <label>Mô tả ngắn</label>
                                <textarea name="small_description" class="form-control" rows="3">{{ $product->small_description }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label>Mô tả chi tiết</label>
                                <textarea name="description" class="form-control" rows="5">{{ $product->description }}</textarea>
                            </div>
                        </div>

                        <!-- Tab Hình ảnh -->
                        <div class="tab-pane fade" id="image-tab-pane" role="tabpanel" aria-labelledby="image-tab" tabindex="0">
                            <div class="mb-3 mt-3">
                                <label>Tải lên hình ảnh sản phẩm</label>
                                <input type="file" name="image[]" id="productImages" multiple class="form-control" accept="image/jpeg,image/png,image/jpg,image/gif" />
                                <small class="text-muted">Chỉ hỗ trợ file: JPG, JPEG, PNG, GIF. Có thể chọn nhiều hình (giữ Ctrl).</small>
                            </div>
                            <div class="row" id="image-preview-container"></div>
                            <div>
                                @if($product->productImages)
                                <div class="row mt-3">
                                    <h5>Hình ảnh hiện tại</h5>
                                    @foreach($product->productImages as $image)
                                    <div class="col-md-2 mb-3">
                                        <div class="card">
                                            <img src="{{ asset($image->image) }}" class="card-img-top" alt="Img" style="height: 120px; object-fit: cover;" />
                                            <div class="card-body p-2 text-center">
                                                <a href="{{ url('admin/product-image/'.$image->id.'/delete') }}" class="btn btn-sm btn-danger">Xóa</a>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <h5 class="mt-3">Không có hình ảnh</h5>
                                @endif
                            </div>
                        </div>

                        <!-- Tab Giá & Kho hàng -->
                        <div class="tab-pane fade" id="pricing-tab-pane" role="tabpanel" aria-labelledby="pricing-tab" tabindex="0">
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Giá</label>
                                        <input type="text" name="price" value="{{ $product->price }}" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Số lượng</label>
                                        <input type="number" name="quantity" value="{{ $product->quantity }}" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Trạng thái</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="status" {{ $product->status == '1' ? 'checked':'' }} style="width: 40px; height: 20px;">
                                            <label class="form-check-label">{{ $product->status == '1' ? 'Hiển thị':'Ẩn' }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="py-2 mt-4 text-center">
                        <button type="submit" class="btn btn-primary btn-lg">Cập nhật sản phẩm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Image upload preview and validation
        const imageInput = document.getElementById('productImages');
        const previewContainer = document.getElementById('image-preview-container');
        
        if (imageInput) {
            imageInput.addEventListener('change', function() {
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
                    
                    // Preview valid files
                    Array.from(this.files).forEach((file, index) => {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const col = document.createElement('div');
                            col.className = 'col-md-2 mb-3';
                            
                            const card = document.createElement('div');
                            card.className = 'card';
                            
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'card-img-top';
                            img.style.height = '120px';
                            img.style.objectFit = 'cover';
                            
                            const cardBody = document.createElement('div');
                            cardBody.className = 'card-body p-2 text-center';
                            
                            const badge = document.createElement('span');
                            badge.className = 'badge bg-info';
                            badge.textContent = 'Hình mới #' + (index + 1);
                            
                            cardBody.appendChild(badge);
                            card.appendChild(img);
                            card.appendChild(cardBody);
                            col.appendChild(card);
                            previewContainer.appendChild(col);
                        };
                        
                        reader.readAsDataURL(file);
                    });
                }
            });
        }
    });
</script>
@endsection