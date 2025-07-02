<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="TemplateMo" />
    <link
        href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900"
        rel="stylesheet" />

    <title>NAC Tax Center</title>

    <!-- Bootstrap core CSS -->
    <link href="{{ asset('new_frontend/vendor/bootstrap/css') }}/bootstrap.min.css" rel="stylesheet" />

    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="{{ asset('new_frontend/assets/css') }}/fontawesome.css" />
    <link rel="stylesheet" href="{{ asset('new_frontend/assets/css') }}/templatemo-edu-meeting.css" />
    <link rel="stylesheet" href="{{ asset('new_frontend/assets/css') }}/owl.css" />
    <link rel="stylesheet" href="{{ asset('new_frontend/assets/css') }}/lightbox.css" />
    <!--

TemplateMo 569 Edu Meeting

https://templatemo.com/tm-569-edu-meeting

-->
</head>

<body>
    @include('new_frontend.layouts.outter_app_header')

    @yield('content')

    @include('new_frontend.layouts.outter_app_footer')

    <!-- Scripts -->
    <!-- Bootstrap core JavaScript -->
    <script src="{{ asset('new_frontend/vendor/jquery') }}/jquery.min.js"></script>
    <script src="{{ asset('new_frontend/vendor/bootstrap') }}/js/bootstrap.min.js"></script>

    <script src="{{ asset('new_frontend/assets/js') }}/isotope.min.js"></script>
    <script src="{{ asset('new_frontend/assets/js') }}/owl-carousel.js"></script>
    <script src="{{ asset('new_frontend/assets/js') }}/lightbox.js"></script>
    <script src="{{ asset('new_frontend/assets/js') }}/tabs.js"></script>
    <script src="{{ asset('new_frontend/assets/js') }}/video.js"></script>
    <script src="{{ asset('new_frontend/assets/js') }}/slick-slider.js"></script>
    <script src="{{ asset('new_frontend/assets/js') }}/custom.js"></script>
    <script>
        //according to loftblog tut
        $(".nav li:first").addClass("active");

        var showSection = function showSection(section, isAnimate) {
            var direction = section.replace(/#/, ""),
                reqSection = $(".section").filter(
                    '[data-section="' + direction + '"]'
                ),
                reqSectionPos = reqSection.offset().top - 0;

            if (isAnimate) {
                $("body, html").animate({
                        scrollTop: reqSectionPos,
                    },
                    800
                );
            } else {
                $("body, html").scrollTop(reqSectionPos);
            }
        };

        var checkSection = function checkSection() {
            $(".section").each(function() {
                var $this = $(this),
                    topEdge = $this.offset().top - 80,
                    bottomEdge = topEdge + $this.height(),
                    wScroll = $(window).scrollTop();
                if (topEdge < wScroll && bottomEdge > wScroll) {
                    var currentId = $this.data("section"),
                        reqLink = $("a").filter(
                            "[href*=\\#" + currentId + "]"
                        );
                    reqLink
                        .closest("li")
                        .addClass("active")
                        .siblings()
                        .removeClass("active");
                }
            });
        };

        $(".main-menu, .responsive-menu, .scroll-to-section").on(
            "click",
            "a",
            function(e) {
                e.preventDefault();
                showSection($(this).attr("href"), true);
            }
        );

        $(window).scroll(function() {
            checkSection();
        });
    </script>
</body>

</html>