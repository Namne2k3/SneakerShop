@extends('layouts.admin')

@section('title', 'Chỉnh sửa thương hiệu - Sneaker Shop Admin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Chỉnh sửa thương hiệu</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.brands.index') }}">Thương hiệu</a></li>
        <li class="breadcrumb-item active">Chỉnh sửa</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-edit me-1"></i>
            Thông tin thương hiệu
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

            <form action="{{ route('admin.brands.update', $brand->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên thương hiệu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $brand->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Slug hiện tại: <strong>{{ $brand->slug }}</strong> (sẽ được cập nhật tự động nếu tên thay đổi)</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5">{{ old('description', $brand->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-header">Logo thương hiệu</div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="logo" class="form-label">Tải lên logo mới</label>
                                    <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo" accept="image/*">
                                    @error('logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted d-block mt-2">
                                        Định dạng hỗ trợ: JPEG, PNG, JPG, GIF<br>
                                        Kích thước tối đa: 2MB<br>
                                        Để trống nếu không muốn thay đổi logo
                                    </small>
                                </div>
                                
                                <div class="text-center mt-3">
                                    <div class="border p-3 rounded">
                                        @if($brand->logo)
                                            <img id="logo-preview" src="{{ asset('storage/' . $brand->logo) }}" class="img-fluid" alt="{{ $brand->name }} Logo">
                                        @else
                                            <img id="logo-preview" src="https://via.placeholder.com/200x100?text=Không+có+logo" class="img-fluid" alt="Không có logo">
                                        @endif
                                    </div>
                                    @if($brand->logo)
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="remove_logo" name="remove_logo" value="1">
                                        <label class="form-check-label" for="remove_logo">
                                            Xóa logo hiện tại
                                        </label>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">Thông tin bổ sung</div>
                            <div class="card-body">
                                <div class="mb-0">
                                    <p class="mb-1"><strong>ID:</strong> {{ $brand->id }}</p>
                                    <p class="mb-1"><strong>Ngày tạo:</strong> {{ $brand->created_at->format('d/m/Y H:i') }}</p>
                                    <p class="mb-0"><strong>Cập nhật lần cuối:</strong> {{ $brand->updated_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.brands.show', $brand->id) }}" class="btn btn-info me-2">
                        <i class="fas fa-eye me-1"></i> Xem chi tiết
                    </a>
                    <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-undo me-1"></i> Hủy
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Lưu thay đổi
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
        // Logo preview
        const logoInput = document.getElementById('logo');
        const logoPreview = document.getElementById('logo-preview');
        const removeLogoCheckbox = document.getElementById('remove_logo');
        
        logoInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    logoPreview.src = e.target.result;
                    
                    // If a new logo is selected, uncheck the remove logo checkbox
                    if (removeLogoCheckbox) {
                        removeLogoCheckbox.checked = false;
                    }
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
        
        // Handle remove logo checkbox
        if (removeLogoCheckbox) {
            removeLogoCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    logoPreview.src = 'https://via.placeholder.com/200x100?text=Không+có+logo';
                    logoInput.value = ''; // Clear the file input
                } else {
                    // Restore the original logo if exists
                    @if($brand->logo)
                    logoPreview.src = "{{ asset('storage/' . $brand->logo) }}";
                    @endif
                }
            });
        }
    });
</script>
@endsection