@php
$courses = App\Models\Course::where('status', 1)
->where('featured', 1)
->orderBy('id', 'ASC')
->limit(5)
->get();
@endphp

<section class="course-area pb-90px">
    <div class="course-wrapper">
        <div class="container">
            <div class="section-heading text-center">
                <h5 class="ribbon ribbon-lg mb-2">Pelatihan Pajak Terbaik</h5>
                <h2 class="section__title">Kelas Populer di NAC Tax Center</h2>
                <span class="section-divider"></span>
            </div>

            <div class="course-carousel owl-action-styled owl--action-styled mt-30px">
                @foreach ($courses as $course)
                <div class="card card-item card-preview">
                    <div class="card-image">
                        <a href="{{ url('course/details/'.$course->id.'/'.$course->course_name_slug) }}" class="d-block">
                            <img class="card-img-top" src="{{ asset($course->course_image) }}" alt="{{ $course->course_name }}">
                        </a>

                        @php
                        $amount = $course->selling_price - $course->discount_price;
                        $discount = ($amount / $course->selling_price) * 100;
                        @endphp

                        <div class="course-badge-labels">
                            <div class="course-badge">Unggulan</div>
                            @if ($course->discount_price == NULL)
                            <div class="course-badge blue">Baru</div>
                            @else
                            <div class="course-badge blue">{{ round($discount) }}%</div>
                            @endif
                        </div>
                    </div>

                    @php
                    $reviewcount = App\Models\Review::where('course_id', $course->id)->where('status', 1)->latest()->get();
                    $avarage = App\Models\Review::where('course_id', $course->id)->where('status', 1)->avg('rating');
                    @endphp

                    <div class="card-body">
                        <h6 class="ribbon ribbon-blue-bg fs-14 mb-3">{{ $course->label }}</h6>
                        <h5 class="card-title">
                            <a href="{{ url('course/details/'.$course->id.'/'.$course->course_name_slug) }}">
                                {{ $course->course_name }}
                            </a>
                        </h5>
                        <p class="card-text">
                            <a href="{{ route('instructor.details', $course->instructor_id) }}">
                                {{ $course['user']['name'] }}
                            </a>
                        </p>

                        <div class="rating-wrap d-flex align-items-center py-2">
                            <div class="review-stars">
                                <span class="rating-number">{{ round($avarage, 1) }}</span>
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <=$avarage)
                                    <span class="la la-star"></span>
                                    @else
                                    <span class="la la-star-o"></span>
                                    @endif
                                    @endfor
                            </div>
                            <span class="rating-total pl-1">({{ count($reviewcount) }})</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            @if ($course->discount_price == NULL)
                            <p class="card-price text-black font-weight-bold">Rp {{ number_format($course->selling_price, 0, ',', '.') }}</p>
                            @else
                            <p class="card-price text-black font-weight-bold">
                                Rp {{ number_format($course->discount_price, 0, ',', '.') }}
                                <span class="before-price font-weight-medium">Rp {{ number_format($course->selling_price, 0, ',', '.') }}</span>
                            </p>
                            @endif

                            <div class="icon-element icon-element-sm shadow-sm cursor-pointer" title="Tambah ke Favorit">
                                <i class="la la-heart-o"></i>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>