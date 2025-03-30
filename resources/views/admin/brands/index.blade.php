@extends('layouts.admin')

@section('title', 'Quản lý thương hiệu - Sneaker Shop Admin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Quản lý thương hiệu</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Thương hiệu</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-tags me-1"></i>
                Danh sách thương hiệu
            </div>
            <a href="{{ route('admin.brands.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> Thêm thương hiệu
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

            <div class="table-responsive">
                <table class="table table-bordered table-hover datatable-table">
                    <thead class="table-light">
                        <tr>
                            <th width="60px">ID</th>
                            <th width="80px">Logo</th>
                            <th>Tên thương hiệu</th>
                            <th>Slug</th>
                            <th>Mô tả</th>
                            <th width="120px">Sản phẩm</th>
                            <th width="150px">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($brands as $brand)
                            <tr>
                                <td>{{ $brand->id }}</td>
                                <td>
                                    @if($brand->logo)
                                        <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}" class="img-thumbnail" style="max-height: 50px;">
                                    @else
                                        <div class="text-center">
                                            <i class="fas fa-image text-secondary"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.brands.edit', $brand->id) }}" class="text-decoration-none">
                                        <strong>{{ $brand->name }}</strong>
                                    </a>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $brand->slug }}</small>
                                </td>
                                <td>
                                    <small>{{ \Illuminate\Support\Str::limit($brand->description, 50) }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $brand->products->count() ?? 0 }} sản phẩm</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.brands.edit', $brand->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.brands.show', $brand->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.brands.destroy', $brand->id) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Bạn có chắc chắn muốn xóa thương hiệu này?');">
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
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted mb-2"><i class="fas fa-tag fa-3x"></i></div>
                                    <h6>Không có thương hiệu nào</h6>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const datatablesElements = document.querySelectorAll('.datatable-table');
        if (datatablesElements.length > 0) {
            const table = new simpleDatatables.DataTable(datatablesElements[0], {
                searchable: true,
                fixedHeight: false,
                perPage: 15
            });
        }
    });
</script>
@endsection