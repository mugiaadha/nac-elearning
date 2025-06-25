<section class="get-started-area pt-5 pb-5 position-relative bg-light">
    <span class="ring-shape ring-shape-1"></span>
    <span class="ring-shape ring-shape-2"></span>
    <span class="ring-shape ring-shape-3"></span>
    <span class="ring-shape ring-shape-4"></span>
    <span class="ring-shape ring-shape-5"></span>
    <span class="ring-shape ring-shape-6"></span>
    <div class="container">
        <div class="row">
            <!-- Gabung jadi Pemateri -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card card-item text-center shadow-sm border-0">
                    <div class="card-body">
                        <img src="{{ asset('frontend/images/small-img-2.jpg') }}" alt="Gabung jadi Pemateri" class="img-fluid rounded-circle mb-3" loading="lazy">
                        <h5 class="card-title pb-2">Jadi Pemateri NAC</h5>
                        <p class="card-text">Bagikan keahlian Anda di bidang perpajakan bersama <br>NAC Tax Center.</p>
                        <div class="btn-box mt-3">
                            <a href="{{ url('/become-instructor') }}" class="btn btn-outline-primary btn-sm">
                                <i class="la la-user mr-1"></i>Gabung Sekarang
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Belajar Pajak -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card card-item text-center shadow-sm border-0">
                    <div class="card-body">
                        <img src="{{ asset('frontend/images/small-img-3.jpg') }}" alt="Belajar Pajak" class="img-fluid rounded-circle mb-3" loading="lazy">
                        <h5 class="card-title pb-2">Belajar Pajak</h5>
                        <p class="card-text">Pelajari perpajakan secara sistematis dan aplikatif, cocok untuk pemula maupun profesional.</p>
                        <div class="btn-box mt-3">
                            <a href="{{ url('/courses') }}" class="btn btn-outline-success btn-sm">
                                <i class="la la-book mr-1"></i>Mulai Belajar
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Layanan Pajak untuk UMKM/Perusahaan -->
            <div class="col-lg-4 col-md-12 mb-4">
                <div class="card card-item text-center shadow-sm border-0">
                    <div class="card-body">
                        <img src="{{ asset('frontend/images/small-img-4.jpg') }}" alt="Pajak untuk Bisnis" class="img-fluid rounded-circle mb-3" loading="lazy">
                        <h5 class="card-title pb-2">Layanan Bisnis & UMKM</h5>
                        <p class="card-text">Dapatkan pendampingan perpajakan untuk perusahaan, startup, hingga UMKM.</p>
                        <div class="btn-box mt-3">
                            <a href="{{ url('/for-business') }}" class="btn btn-outline-dark btn-sm">
                                <i class="la la-briefcase mr-1"></i>Konsultasi Sekarang
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>