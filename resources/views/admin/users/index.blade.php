@extends('layouts.admin')

@section('title', 'Quản lý người dùng - Sneaker Shop Admin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý người dùng</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Người dùng</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-users me-1"></i>
                Danh sách người dùng
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus me-1"></i> Thêm người dùng
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
                    <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên, email..." value="{{ request('search') }}">
                            <select name="role" class="form-select" style="max-width: 150px;">
                                <option value="">Tất cả vai trò</option>
                                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Quản trị viên</option>
                                <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Khách hàng</option>
                            </select>
                            <select name="status" class="form-select" style="max-width: 150px;">
                                <option value="">Tất cả trạng thái</option>
                                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Đang hoạt động</option>
                                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Đã khóa</option>
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
                            <li><a class="dropdown-item bulk-action" href="#" data-action="active"><i class="fas fa-unlock me-1"></i> Kích hoạt đã chọn</a></li>
                            <li><a class="dropdown-item bulk-action" href="#" data-action="inactive"><i class="fas fa-lock me-1"></i> Khóa đã chọn</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
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
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>Vai trò</th>
                            <th>Ngày đăng ký</th>
                            <th width="100px">Trạng thái</th>
                            <th width="140px">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input user-checkbox" type="checkbox" value="{{ $user->id }}" {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                </div>
                            </td>
                            <td>{{ $user->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-2 bg-light rounded-circle text-center" style="width: 40px; height: 40px; line-height: 40px;">
                                        <span class="text-uppercase">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <div>{{ $user->name }}</div>
                                        @if($user->phone)
                                            <small class="text-muted">{{ $user->phone }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role === 'admin')
                                    <span class="badge bg-primary">Quản trị viên</span>
                                @else
                                    <span class="badge bg-info">Khách hàng</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input status-switch" type="checkbox" 
                                        data-id="{{ $user->id }}" 
                                        {{ $user->active ? 'checked' : '' }}
                                        {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted mb-2"><i class="fas fa-users fa-3x"></i></div>
                                <h6>Không tìm thấy người dùng nào</h6>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $users->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Form for bulk actions -->
<form id="bulk-action-form" action="{{ route('admin.users.bulk-action') }}" method="POST" style="display: none;">
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
        const userCheckboxes = document.querySelectorAll('.user-checkbox:not(:disabled)');
        
        selectAll.addEventListener('change', function() {
            userCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectAllStatus();
        });
        
        userCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectAllStatus();
            });
        });
        
        function updateSelectAllStatus() {
            const checkedBoxes = document.querySelectorAll('.user-checkbox:checked').length;
            const totalBoxes = userCheckboxes.length;
            selectAll.checked = checkedBoxes > 0 && checkedBoxes === totalBoxes;
        }
        
        // Bulk actions
        const bulkActions = document.querySelectorAll('.bulk-action');
        bulkActions.forEach(action => {
            action.addEventListener('click', function(e) {
                e.preventDefault();
                
                const selectedUsers = Array.from(document.querySelectorAll('.user-checkbox:checked'))
                    .map(checkbox => checkbox.value);
                
                if (selectedUsers.length === 0) {
                    alert('Vui lòng chọn ít nhất một người dùng');
                    return;
                }
                
                const actionType = this.dataset.action;
                let confirmMessage = '';
                
                switch(actionType) {
                    case 'active':
                        confirmMessage = 'Bạn có chắc chắn muốn kích hoạt tất cả người dùng đã chọn?';
                        break;
                    case 'inactive':
                        confirmMessage = 'Bạn có chắc chắn muốn khóa tất cả người dùng đã chọn?';
                        break;
                }
                
                if (confirm(confirmMessage)) {
                    document.getElementById('bulk-action-ids').value = JSON.stringify(selectedUsers);
                    document.getElementById('bulk-action-type').value = actionType;
                    document.getElementById('bulk-action-form').submit();
                }
            });
        });
        
        // Status switch functionality
        const statusSwitches = document.querySelectorAll('.status-switch:not(:disabled)');
        statusSwitches.forEach(statusSwitch => {
            statusSwitch.addEventListener('change', function() {
                const userId = this.dataset.id;
                const active = this.checked ? 1 : 0;
                
                fetch(`{{ route('admin.users.update-status') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        user_id: userId,
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