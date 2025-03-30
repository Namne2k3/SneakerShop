<div class="container">
    <div class="row">
        <div class="col-md-4 mb-3 mb-md-0">
            <h5>Về chúng tôi</h5>
            <p>Sneaker Shop - Cửa hàng giày sneaker chính hãng với đa dạng mẫu mã, phong cách và giá cả phải chăng.</p>
        </div>

        <div class="col-md-4 mb-3 mb-md-0">
            <h5>Liên kết nhanh</h5>
            <ul class="list-unstyled">
                <li><a href="{{ route('home') }}" class="text-white">Trang chủ</a></li>
                <li><a href="{{ route('shop') }}" class="text-white">Cửa hàng</a></li>
                <li><a href="{{ route('contact') }}" class="text-white">Liên hệ</a></li>
                @auth
                    <li><a href="{{ route('profile') }}" class="text-white">Tài khoản</a></li>
                @else
                    <li><a href="{{ route('login') }}" class="text-white">Đăng nhập</a></li>
                @endauth
            </ul>
        </div>

        <div class="col-md-4">
            <h5>Liên hệ với chúng tôi</h5>
            <ul class="list-unstyled">
                <li><i class="fas fa-map-marker-alt me-2"></i> 123 Đường ABC, Quận XYZ, TP. HCM</li>
                <li><i class="fas fa-phone me-2"></i> (123) 456-7890</li>
                <li><i class="fas fa-envelope me-2"></i> info@sneakershop.com</li>
            </ul>
            <div class="mt-3">
                <a href="#" class="text-white me-3"><i class="fab fa-facebook fa-lg"></i></a>
                <a href="#" class="text-white me-3"><i class="fab fa-instagram fa-lg"></i></a>
                <a href="#" class="text-white me-3"><i class="fab fa-twitter fa-lg"></i></a>
                <a href="#" class="text-white"><i class="fab fa-youtube fa-lg"></i></a>
            </div>
        </div>
    </div>

    <hr class="my-4 bg-light">

    <div class="row">
        <div class="col-md-12">
            <p class="text-center mb-0">&copy; {{ date('Y') }} Sneaker Shop. All rights reserved.</p>
        </div>
    </div>
</div>
