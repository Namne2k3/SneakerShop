@extends('layouts.admin')

@section('title', 'Quản lý sản phẩm - Sneaker Shop Admin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý sản phẩm</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Sản phẩm</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-box me-1"></i>
                Danh sách sản phẩm
            </div>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> Thêm sản phẩm
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Filter and Search -->
            <div class="row mb-3">
                <div class="col-md-8">
                    <form action="{{ route('admin.products.index') }}" method="GET" class="d-flex">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên, mã sản phẩm..." value="{{ request('search') }}">
                            <select name="brand" class="form-select" style="max-width: 150px;">
                                <option value="">Tất cả thương hiệu</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                @endforeach
                            </select>
                            <select name="category" class="form-select" style="max-width: 150px;">
                                <option value="">Tất cả danh mục</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <select name="status" class="form-select" style="max-width: 120px;">
                                <option value="">Tất cả trạng thái</option>
                                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Hiển thị</option>
                                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Ẩn</option>
                            </select>
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i> Tìm kiếm
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-md-4 text-end">
                    <div class="btn-group" role="group">
                        <button id="bulkActionsDropdown" type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-cog"></i> Thao tác hàng loạt
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="bulkActionsDropdown">
                            <li><a class="dropdown-item bulk-action" href="#" data-action="active"><i class="fas fa-eye me-1"></i> Hiển thị đã chọn</a></li>
                            <li><a class="dropdown-item bulk-action" href="#" data-action="inactive"><i class="fas fa-eye-slash me-1"></i> Ẩn đã chọn</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item bulk-action text-danger" href="#" data-action="delete"><i class="fas fa-trash-alt me-1"></i> Xóa đã chọn</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Products Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover datatable-table">
                    <thead class="table-light">
                        <tr>
                            <th width="40px">
                                <div class="form-check">
                                    <input class="form-check-input select-all" type="checkbox" id="select-all">
                                </div>
                            </th>
                            <th width="60px">ID</th>
                            <th width="80px">Hình ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Danh mục</th>
                            <th>Thương hiệu</th>
                            <th>Giá</th>
                            <th width="100px">Biến thể</th>
                            <th width="80px">Trạng thái</th>
                            <th width="140px">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input product-checkbox" type="checkbox" value="{{ $product->id }}">
                                </div>
                            </td>
                            <td>{{ $product->id }}</td>
                            <td>
                                @if($product->images->isNotEmpty())
                                    @php
                                        $mainImage = $product->images->where('is_primary', 1)->first() ?? $product->images->first();
                                        $imagePath = $mainImage->image_path;
                                        $isExternalUrl = filter_var($imagePath, FILTER_VALIDATE_URL);
                                    @endphp
                                    <img src="{{ $isExternalUrl ? $imagePath : asset('storage/' . $imagePath) }}" 
                                        class="img-thumbnail" alt="{{ $product->name }}" style="max-height: 50px;">
                                @else
                                    <img src="https://via.placeholder.com/50x50?text=No+Image" class="img-thumbnail" alt="No Image">
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="text-decoration-none">
                                    <strong>{{ $product->name }}</strong>
                                </a>
                                @if($product->featured)
                                    <span class="badge bg-warning">Nổi bật</span>
                                @endif
                                <br>
                                <small class="text-muted">Slug: {{ $product->slug }}</small>
                            </td>
                            <td>
                                @foreach($product->categories as $category)
                                    <span class="badge bg-secondary">{{ $category->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                @if($product->brand)
                                    {{ $product->brand->name }}
                                @else
                                    <span class="text-muted">Không có</span>
                                @endif
                            </td>
                            <td>
                                @if($product->sale_price)
                                    <del class="text-muted">{{ number_format($product->price) }} đ</del><br>
                                    <span class="text-danger">{{ number_format($product->sale_price) }} đ</span>
                                @else
                                    {{ number_format($product->price) }} đ
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $product->variants->count() }} biến thể</span>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input status-switch" type="checkbox" 
                                        data-id="{{ $product->id }}" 
                                        {{ $product->active ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <div class="text-muted mb-2"><i class="fas fa-box fa-3x"></i></div>
                                <h6>Không tìm thấy sản phẩm nào</h6>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $products->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Form for bulk actions -->
<form id="bulk-action-form" action="{{ route('admin.products.bulk-action') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="ids" id="bulk-action-ids">
    <input type="hidden" name="action" id="bulk-action-type">
</form>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select all functionality
        const selectAll = document.getElementById('select-all');
        const productCheckboxes = document.querySelectorAll('.product-checkbox');
        
        selectAll.addEventListener('change', function() {
            productCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectAllStatus();
        });
        
        productCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectAllStatus();
            });
        });
        
        function updateSelectAllStatus() {
            const checkedBoxes = document.querySelectorAll('.product-checkbox:checked').length;
            const totalBoxes = productCheckboxes.length;
            selectAll.checked = checkedBoxes > 0 && checkedBoxes === totalBoxes;
        }
        
        // Bulk actions
        const bulkActions = document.querySelectorAll('.bulk-action');
        bulkActions.forEach(action => {
            action.addEventListener('click', function(e) {
                e.preventDefault();
                
                const selectedProducts = Array.from(document.querySelectorAll('.product-checkbox:checked'))
                    .map(checkbox => checkbox.value);
                
                if (selectedProducts.length === 0) {
                    alert('Vui lòng chọn ít nhất một sản phẩm');
                    return;
                }
                
                const actionType = this.dataset.action;
                let confirmMessage = '';
                
                switch(actionType) {
                    case 'active':
                        confirmMessage = 'Bạn có chắc chắn muốn hiển thị tất cả sản phẩm đã chọn?';
                        break;
                    case 'inactive':
                        confirmMessage = 'Bạn có chắc chắn muốn ẩn tất cả sản phẩm đã chọn?';
                        break;
                    case 'delete':
                        confirmMessage = 'Bạn có chắc chắn muốn xóa tất cả sản phẩm đã chọn? Hành động này không thể hoàn tác!';
                        break;
                }
                
                if (confirm(confirmMessage)) {
                    document.getElementById('bulk-action-ids').value = JSON.stringify(selectedProducts);
                    document.getElementById('bulk-action-type').value = actionType;
                    document.getElementById('bulk-action-form').submit();
                }
            });
        });
        
        // Status switch functionality
        const statusSwitches = document.querySelectorAll('.status-switch');
        statusSwitches.forEach(statusSwitch => {
            statusSwitch.addEventListener('change', function() {
                const productId = this.dataset.id;
                const active = this.checked ? 1 : 0;
                
                fetch(`{{ route('admin.products.update-status') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        active: active
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Optional: Show a small toast notification
                        console.log('Status updated successfully');
                    } else {
                        console.error('Failed to update status');
                        this.checked = !this.checked; // Revert the switch
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.checked = !this.checked; // Revert the switch
                });
            });
        });
    });
</script>
@endsection