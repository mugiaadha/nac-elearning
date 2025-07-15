@php
$setting = App\Models\SiteSetting::find(1);
@endphp

<footer class="footer-main" style="background:#fff; color:#444; border-top:1px solid #e5e5e5; padding-top:48px;">
    <div class="container">
        <div class="row gy-5 align-items-start">
            <!-- Kiri: Logo & Kontak -->
            <div class="col-12 col-md-4 mb-4 mb-md-0">
                <a href="{{ route('home') }}" class="logo">
                    <img src="{{ asset($setting->logo ?? 'new_frontend/assets/images/logo.png') }}" alt="footer logo" style="height:40px; width:95px;">
                </a>
                <div class="mt-4 mb-3" style="font-size:1.1em; color:#6c757d;">
                    <div>{{ $setting->phone }}</div>
                    <div>{{ $setting->email }}</div>
                    <div>{{ $setting->address }}</div>
                </div>
                <div class="mt-4">
                    <div class="fw-bold mb-2" style="font-size:1.2em; color:#223a5f;">We are on</div>
                    <div class="d-flex gap-2">
                        <a href="{{ $setting->facebook ?? '#' }}" class="footer-social rounded-circle d-flex align-items-center justify-content-center" style="background:#435a8b;width:38px;height:38px;"><i class="fa fa-facebook text-white"></i></a>
                        <a href="{{ $setting->twitter ?? '#' }}" class="footer-social rounded-circle d-flex align-items-center justify-content-center" style="background:#42a5f5;width:38px;height:38px;"><i class="fa fa-twitter text-white"></i></a>
                        <a href="#" class="footer-social rounded-circle d-flex align-items-center justify-content-center" style="background:#1769ff;width:38px;height:38px;"><i class="fa fa-behance text-white"></i></a>
                        <a href="{{ $setting->linkedin ?? '#' }}" class="footer-social rounded-circle d-flex align-items-center justify-content-center" style="background:#378fe9;width:38px;height:38px;"><i class="fa fa-linkedin text-white"></i></a>
                    </div>
                </div>
            </div>
            <!-- Tengah: Company & Courses -->
            <div class="col-6 col-md-4 mb-4 mb-md-0">
                <div class="row">
                    <div class="col-6">
                        <div class="fw-bold mb-2" style="font-size:1.3em; color:#223a5f;">Company</div>
                        <ul class="list-unstyled" style="color:#6c757d; font-size:1.08em;">
                            <li><a href="#about" class="footer-link">About us</a></li>
                            <li><a href="#contact" class="footer-link">Contact us</a></li>
                            <li><a href="#" class="footer-link">Become a Teacher</a></li>
                            <li><a href="#" class="footer-link">Support</a></li>
                            <li><a href="#" class="footer-link">FAQs</a></li>
                            <li><a href="#blog" class="footer-link">Blog</a></li>
                        </ul>
                    </div>
                    <div class="col-6">
                        <div class="fw-bold mb-2" style="font-size:1.3em; color:#223a5f;">Courses</div>
                        <ul class="list-unstyled" style="color:#6c757d; font-size:1.08em;">
                            <li><a href="#" class="footer-link">Web Development</a></li>
                            <li><a href="#" class="footer-link">Hacking</a></li>
                            <li><a href="#" class="footer-link">PHP Learning</a></li>
                            <li><a href="#" class="footer-link">Spoken English</a></li>
                            <li><a href="#" class="footer-link">Self-Driving Car</a></li>
                            <li><a href="#" class="footer-link">Garbage Collectors</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- Kanan: Download App -->
            <div class="col-6 col-md-4">
                <div class="fw-bold mb-2" style="font-size:1.3em; color:#223a5f;">Category</div>
                <ul class="list-unstyled" style="color:#6c757d; font-size:1.08em;">
                    <li><a href="#" class="footer-link">Programming</a></li>
                    <li><a href="#" class="footer-link">Design</a></li>
                    <li><a href="#" class="footer-link">Marketing</a></li>
                    <li><a href="#" class="footer-link">Business</a></li>
                    <li><a href="#" class="footer-link">Photography</a></li>
                    <li><a href="#" class="footer-link">Language</a></li>
                </ul>
            </div>
        </div>
        <div class="row mt-5 pt-4 border-top align-items-center" style="font-size:1.08em; color:#6c757d;">
            <div class="col-12 text-center" style="font-size:1em;">
                &copy; {{ date('Y') }} nactaxcenter. All rights reserved.
            </div>
        </div>
    </div>
    <style>
        .footer-link {
            color: #6c757d;
            text-decoration: none;
            transition: color .2s;
        }

        .footer-link:hover {
            color: #223a5f;
            text-decoration: underline;
        }

        .footer-social:hover {
            opacity: 0.8;
        }
    </style>
</footer>