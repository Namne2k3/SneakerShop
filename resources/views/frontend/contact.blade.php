@extends('layouts.app')

@section('title', 'Liên hệ - Sneaker Shop')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h2 class="h4 mb-0">Liên hệ với chúng tôi</h2>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('contact.send') }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Họ tên</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" 
                                    name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" 
                                    name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" 
                                name="phone" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Tiêu đề</label>
                            <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" 
                                name="subject" value="{{ old('subject') }}" required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="message" class="form-label">Nội dung</label>
                            <textarea class="form-control @error('message') is-invalid @enderror" id="message" 
                                name="message" rows="5" required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Gửi tin nhắn</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mt-4 mt-lg-0">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h4>Thông tin liên hệ</h4>
                    <hr>
                    <div class="mb-3">
                        <i class="fas fa-map-marker-alt me-2 text-primary"></i> 
                        <span>22/4c Khu phố 6, Phường Tân Mai, Thành phố Biên Hòa</span>
                    </div>
                    <div class="mb-3">
                        <i class="fas fa-phone-alt me-2 text-primary"></i> 
                        <span>(+84) 387 805 723</span>
                    </div>
                    <div class="mb-3">
                        <i class="fas fa-envelope me-2 text-primary"></i> 
                        <span>nhpn2003@gmail.com</span>
                    </div>
                    <div class="mb-3">
                        <i class="far fa-clock me-2 text-primary"></i> 
                        <span>09:00 - 21:00, Thứ 2 - Chủ nhật</span>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4>Kết nối với chúng tôi</h4>
                    <hr>
                    <div class="d-flex gap-3 fs-4">
                        <a href="#" class="text-dark"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-dark"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-dark"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="text-dark"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Google Map -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="ratio ratio-21x9">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3920.0098087681258!2d106.6956817147544!3d10.73076069234428!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f62a90e5dbd%3A0x674d5126513db295!2zVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBUw7RuIMSQ4bupYyBUaOG6r25n!5e0!3m2!1svi!2s!4v1654499728174!5m2!1svi!2s" 
                            allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Form validation script using Bootstrap's built-in validation
    (function() {
        'use strict';
        
        var forms = document.querySelectorAll('.needs-validation');
        
        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>
@endsection