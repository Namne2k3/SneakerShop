@extends('layouts.admin')

@section('title', 'Quản lý đơn hàng')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý đơn hàng</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Đơn hàng</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-shopping-cart me-1"></i>
                    Danh sách đơn hàng
                </div>
                <div>
                    <!-- Export options would go here -->
                    <a href="#" class="btn btn-sm btn-outline-secondary me-1">
                        <i class="fas fa-download"></i> Xuất Excel
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <form action="{{ route('admin.orders.index') }}" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control me-2" placeholder="Tìm theo mã đơn hàng hoặc email..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">Tìm</button>
                    </form>
                </div>
                <div class="col-md-6 text-end">
                    <div class="btn-group">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-{{ request('status') ? 'outline-' : '' }}primary">Tất cả</a>
                        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="btn btn-{{ request('status') == 'pending' ? '' : 'outline-' }}warning">Chờ xử lý</a>
                        <a href="{{ route('admin.orders.index', ['status' => 'processing']) }}" class="btn btn-{{ request('status') == 'processing' ? '' : 'outline-' }}info">Đang xử lý</a>
                        <a href="{{ route('admin.orders.index', ['status' => 'completed']) }}" class="btn btn-{{ request('status') == 'completed' ? '' : 'outline-' }}success">Hoàn thành</a>
                        <a href="{{ route('admin.orders.index', ['status' => 'declined']) }}" class="btn btn-{{ request('status') == 'declined' ? '' : 'outline-' }}secondary">Từ chối</a>
                        <a href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}" class="btn btn-{{ request('status') == 'cancelled' ? '' : 'outline-' }}danger">Đã hủy</a>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form id="bulk-action-form" action="{{ route('admin.orders.bulk-action') }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="40">
                                    <input type="checkbox" id="select-all">
                                </th>
                                <th>Mã đơn hàng</th>
                                <th>Khách hàng</th>
                                <th>Ngày đặt</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th width="150">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="order_ids[]" value="{{ $order->id }}" class="order-checkbox">
                                    </td>
                                    <td>{{ $order->order_number }}</td>
                                    <td>
                                        {{ $order->user->name ?? 'N/A' }}<br>
                                        <small>{{ $order->user->email ?? '' }}</small>
                                    </td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ number_format($order->total) }}đ</td>
                                    <td>
                                        @php
                                            $statusClass = [
                                                'pending' => 'warning',
                                                'processing' => 'info',
                                                'completed' => 'success',
                                                'declined' => 'danger',
                                                'cancelled' => 'danger'
                                            ][$order->status];
                                            
                                            $statusText = [
                                                'pending' => 'Chờ xử lý',
                                                'processing' => 'Đang xử lý',
                                                'completed' => 'Hoàn thành',
                                                'declined' => 'Từ chối',
                                                'cancelled' => 'Đã hủy'
                                            ][$order->status];
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#updateStatusModal" data-order-id="{{ $order->id }}" data-status="{{ $order->status }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        @if($order->status === 'cancelled')
                                            <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Không có đơn hàng nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- @if($orders->count() > 0)
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-inline">
                                <label class="me-2">Với các đơn hàng đã chọn:</label>
                                <div class="input-group">
                                    <select name="action" id="bulk-action" class="form-select">
                                        <option value="">-- Chọn hành động --</option>
                                        <option value="processing">Đánh dấu đang xử lý</option>
                                        <option value="completed">Đánh dấu hoàn thành</option>
                                        <option value="declined">Đánh dấu từ chối</option>
                                        <option value="cancelled">Đánh dấu đã hủy</option>
                                        <option value="delete">Xóa (chỉ đơn đã hủy)</option>
                                    </select>
                                    <button type="submit" class="btn btn-outline-secondary" id="apply-bulk-action" disabled>Áp dụng</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            {{ $orders->appends(request()->query())->links() }}
                        </div>
                    </div>
                @endif -->
                
                <input type="hidden" name="ids" id="selected-ids">
            </form>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST" id="update-status-form">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Cập nhật trạng thái đơn hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="status">Trạng thái</label>
                        <select name="status" id="status" class="form-control">
                            <option value="pending">Chờ xử lý</option>
                            <option value="processing">Đang xử lý</option>
                            <option value="completed">Hoàn thành</option>
                            <option value="declined">Từ chối</option>
                            <option value="cancelled">Đã hủy</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle select all checkbox
        const selectAllCheckbox = document.getElementById('select-all');
        const orderCheckboxes = document.querySelectorAll('.order-checkbox');
        const bulkActionButton = document.getElementById('apply-bulk-action');
        const selectedIdsInput = document.getElementById('selected-ids');
        
        selectAllCheckbox.addEventListener('change', function() {
            orderCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            updateBulkActionButton();
        });
        
        orderCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectAllCheckbox();
                updateBulkActionButton();
            });
        });
        
        function updateSelectAllCheckbox() {
            const checkedCount = document.querySelectorAll('.order-checkbox:checked').length;
            selectAllCheckbox.checked = checkedCount === orderCheckboxes.length && orderCheckboxes.length > 0;
        }
        
        function updateBulkActionButton() {
            const checkedCount = document.querySelectorAll('.order-checkbox:checked').length;
            bulkActionButton.disabled = checkedCount === 0;
            
            // Update hidden input with selected IDs
            const selectedIds = Array.from(document.querySelectorAll('.order-checkbox:checked'))
                .map(checkbox => checkbox.value);
            selectedIdsInput.value = JSON.stringify(selectedIds);
        }
        
        // Handle status update modal
        const updateStatusModal = document.getElementById('updateStatusModal');
        if (updateStatusModal) {
            updateStatusModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const orderId = button.getAttribute('data-order-id');
                const currentStatus = button.getAttribute('data-status');
                
                const form = updateStatusModal.querySelector('#update-status-form');
                form.action = `{{ route('admin.orders.index') }}/${orderId}/status`;
                
                const statusSelect = updateStatusModal.querySelector('#status');
                statusSelect.value = currentStatus;
            });
        }
        
        // Handle bulk action form submission
        document.getElementById('bulk-action-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const action = document.getElementById('bulk-action').value;
            if (!action) {
                alert('Vui lòng chọn hành động!');
                return;
            }
            
            if (action === 'delete' && !confirm('Bạn có chắc chắn muốn xóa các đơn hàng đã chọn? Chỉ các đơn hàng đã hủy mới bị xóa.')) {
                return;
            }
            
            this.submit();
        });
    });
</script>
@endsection