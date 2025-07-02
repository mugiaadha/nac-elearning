@php
$setting = App\Models\SiteSetting::find(1);
@endphp

<!-- Sub Header -->
<div class="sub-header">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-sm-8">
                <div class="left-content">
                    <p>{{ $setting->email }}</p>
                </div>
            </div>
            <div class="col-lg-4 col-sm-4">
                <div class="right-icons">
                    <ul>
                        <li>
                            <a href="{{ $setting->facebook }}"><i class="fa fa-facebook"></i></a>
                        </li>
                        <li>
                            <a href="{{ $setting->twitter }}"><i class="fa fa-twitter"></i></a>
                        </li>
                        <li>
                            <a href="{{ $setting->facebook }}"><i class="fa fa-behance"></i></a>
                        </li>
                        <li>
                            <a
                                href="{{ $setting->linkedin }}"><i class="fa fa-linkedin"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ***** Header Area Start ***** -->
<header class="header-area header-sticky">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="main-nav">
                    <!-- ***** Logo Start ***** -->
                    <a href="{{ route('home') }}" class="logo">
                        <img src="{{ asset($setting->logo)}}" alt="logo">
                    </a>
                    <!-- ***** Logo End ***** -->
                    <!-- ***** Menu Start ***** -->
                    <ul class="nav">
                        <li class="scroll-to-section">
                            <a href="#top" class="active">Beranda</a>
                        </li>
                        <li><a href="layanan.html">Layanan</a></li>
                        <li class="scroll-to-section">
                            <a href="#konsultasi">Konsultasi</a>
                        </li>
                        <li class="has-sub">
                            <a href="javascript:void(0)">Tentang Kami</a>
                            <ul class="sub-menu">
                                <li>
                                    <a href="profil.html">Profil NAC</a>
                                </li>
                                <li><a href="tim.html">Tim Kami</a></li>
                            </ul>
                        </li>
                        <li class="scroll-to-section">
                            <a href="#edukasi">Edukasi Pajak</a>
                        </li>
                        <li class="scroll-to-section">
                            <a href="#kontak">Kontak</a>
                        </li>
                    </ul>
                    <a class="menu-trigger">
                        <span>Menu</span>
                    </a>
                    <!-- ***** Menu End ***** -->
                </nav>
            </div>
        </div>
    </div>
</header>
<!-- ***** Header Area End ***** -->