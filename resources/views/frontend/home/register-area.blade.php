<section class="register-area section-padding dot-bg overflow-hidden">
    <div class="container">
        <div class="row">
            <!-- Form Area -->
            <div class="col-lg-5">
                <div class="card card-item shadow-sm border-0">
                    <div class="card-body">
                        <h3 class="fs-24 font-weight-semi-bold pb-2">Daftar Konsultasi Pajak Gratis</h3>
                        <div class="divider"><span></span></div>
                        <form method="post">
                            <div class="input-box">
                                <label class="label-text">Nama Lengkap</label>
                                <div class="form-group position-relative">
                                    <input class="form-control form--control" type="text" name="name" placeholder="Nama Anda">
                                    <span class="la la-user input-icon"></span>
                                </div>
                            </div>
                            <div class="input-box">
                                <label class="label-text">Email</label>
                                <div class="form-group position-relative">
                                    <input class="form-control form--control" type="email" name="email" placeholder="Alamat Email">
                                    <span class="la la-envelope input-icon"></span>
                                </div>
                            </div>
                            <div class="input-box">
                                <label class="label-text">Nomor Telepon</label>
                                <div class="form-group position-relative">
                                    <input class="form-control form--control" type="text" name="phone" placeholder="Nomor Telepon Aktif">
                                    <span class="la la-phone input-icon"></span>
                                </div>
                            </div>
                            <div class="input-box">
                                <label class="label-text">Topik Konsultasi</label>
                                <div class="form-group position-relative">
                                    <input class="form-control form--control" type="text" name="subject" placeholder="Contoh: PPh Pribadi, UMKM, dll">
                                    <span class="la la-book input-icon"></span>
                                </div>
                            </div>
                            <div class="btn-box pt-2">
                                <button class="btn btn-primary btn-block" type="submit">
                                    Daftar Sekarang <i class="la la-arrow-right icon ml-1"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="col-lg-6 ml-auto d-flex align-items-center">
                <div class="register-content">
                    <div class="section-heading">
                        <h5 class="ribbon ribbon-lg mb-2 text-primary">Pendaftaran</h5>
                        <h2 class="section__title">Tingkatkan Literasi Pajak Bersama NAC</h2>
                        <span class="section-divider"></span>
                        <p class="section__desc">
                            NAC Tax Center memberikan edukasi dan layanan konsultasi pajak untuk individu dan UMKM.
                            Dapatkan informasi terbaru dan terpercaya tentang perpajakan langsung dari para profesional kami.
                            Bergabunglah sekarang untuk menerima tips, seminar, dan informasi penting seputar pajak secara gratis!
                        </p>
                    </div>
                    <div class="btn-box pt-35px">
                        <a href="{{ url('/sign-up') }}" class="btn btn-outline-primary">
                            <i class="la la-user mr-1"></i> Mulai Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>