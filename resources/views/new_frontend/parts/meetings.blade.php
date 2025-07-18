<section class="upcoming-meetings" id="meetings">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-heading">
                    <h2>Pelatihan Online</h2>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="categories">
                    <h4>Kategori</h4>
                    <ul>
                        @foreach($categories as $cat)
                        <li><a href="{{ url('course/category/'.$cat->id.'/'.$cat->category_slug) }}">{{ $cat->category_name }}</a></li>
                        @endforeach
                    </ul>
                    <div class="main-button-red">
                        <a href="#">Lihat Semua</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="row">
                    @foreach($courses as $course)
                    <div class="col-lg-4">
                        <div class="meeting-item">
                            <div class="thumb">
                                @if($course->is_free)
                                <div class="price">
                                    <span>Gratis</span>
                                </div>
                                @endif
                                <a href="{{ url('course/details/'.$course->id.'/'.$course->course_name_slug) }}">
                                    <img src="{{ asset($course->course_image) }}" alt="{{ $course->course_name }}" />
                                </a>
                            </div>
                            <div class="down-content">
                                <a href="{{ url('course/details/'.$course->id.'/'.$course->course_name_slug) }}">
                                    <h4>{{ $course->course_name }}</h4>
                                </a>
                                <p>{{ $course->short_description ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>