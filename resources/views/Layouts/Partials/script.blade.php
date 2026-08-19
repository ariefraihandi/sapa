<script src="{{ asset('assets') }}/vendor/jquery/dist/jquery.min.js"></script>
<script src="{{ asset('assets') }}/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets') }}/vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
<script src="{{ asset('assets') }}/vendor/metismenu/dist/metisMenu.min.js"></script>
<script src="{{ asset('assets') }}/vendor/chart-js/chart.bundle.min.js"></script>
<script src="{{ asset('assets') }}/vendor/owl-carousel/owl.carousel.js"></script>
<script src="{{ asset('assets') }}/vendor/peity/jquery.peity.min.js"></script>
<script src="{{ asset('assets') }}/vendor/apexcharts/dist/apexcharts.min.js"></script>

<script src="{{ asset('assets') }}/js/dashboard/dashboard-1.js"></script>
<script src="{{ asset('assets') }}/vendor/i18n/i18n.js"></script>
<script src="{{ asset('assets') }}/js/translator.js"></script>

<script src="{{ asset('assets') }}/js/deznav-init.js"></script>
<script src="{{ asset('assets') }}/js/custom.js"></script>

<script>
    function carouselReview(){
        /* testimonial one function by = owl.carousel.js */
        jQuery('.testimonial-one').owlCarousel({
            nav: true,
            loop: true,
            autoplay: true,
            margin: 30,
            dots: false,
            rtl: true,
            navText: ['<i class="fa fa-chevron-left" aria-hidden="true"></i>', '<i class="fa fa-chevron-right" aria-hidden="true"></i>'],
            responsive: {
                0: { items: 1 },
                576: { items: 1 },
                768: { items: 1 },
                991: { items: 2 },
                1200: { items: 2 },
                1600: { items: 3 }
            }
        });
    }
    
    jQuery(window).on('load', function(){
        setTimeout(function(){
            carouselReview();
        }, 1000);
    });
</script>

@stack('scripts')