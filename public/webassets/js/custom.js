$(function () {
    "use strict";
    /*---------------sticky header on scroll js ---------------*/
    
    
        
    function fAqactive(){
        $(".faq_accordian_two .card").click(function(){
            $(".faq_accordian_two .card").removeClass("active");
            $(this).addClass('active');
        });
    }
    fAqactive();
    
    
    function showNav() {
        $('.navbar-expand-lg.navbar .navbar-nav .nav-item a').click(function(){
            $('.navbar-expand-lg.navbar .navbar-nav .nav-item .sub-menu').toggleClass('show');
        });
    }
    showNav();
    
    
    $(".navbar").sticky({topSpacing: 0});
    $(window).on("scroll", function () {
        if ($(document).scrollTop() > 70) {
            $('.transparent-nav').addClass('shrink');
        } else {
            $('.transparent-nav').removeClass('shrink');
        }
    });
    $(".navbar").sticky({topSpacing: 0});
    if (Modernizr.mq('(max-width: 991px)')) {

        $(window).on("scroll", function () {
            if ($(document).scrollTop() > 0) {
                $('.transparent-nav').addClass('shrink');
            } else {
                $('.transparent-nav').addClass('shrink');
            }
        });
    }

    $('input[type="file"]').change(function(e){
        var fileName = e.target.files[0].name;
        $('.custom-file-label').html(fileName);
    });
     /*---------------navbar js ---------------*/
    $('.navbar li a').on('click', function(event) {
        
        $('html, body').stop().animate({
            scrollTop: $(this.hash).offset().top - 150
        }, 1000, 'easeInOutExpo');
        $('.navbar-collapse').removeClass('show');
        $('.navbar-toggler').addClass('collapsed');
        
        event.preventDefault();
    });
//     var scrollLink = $('.scroll');

// // :: 5.0 SMOOTH SCROLLING ACTIVE CODE
// scrollLink.on('click', function (e) {
//   e.preventDefault();
//   $('body,html').animate({
//     scrollTop: $(this.hash).offset().top - 100
//   }, 1000);
// });

    /*---------------testimonial-sliderjs --------*/
    function testimoni(){
        var testimonials = $(".testimonial-carousel");
        if( testimonials.length ){
            testimonials.owlCarousel({
                loop:true,
                margin:0,
                items:2,
                autoplay:true,
                autoplayHoverPause: true,
                smartSpeed:1000,
                nav: false,
                responsiveClass:true,
                responsive:{
                    0:{
                        items:1, 
                    },
                    720:{
                        items:2, 
                    }, 
                },
                
            });
        }
    }
    testimoni();
    
    /*---------------testimonial_slider-eight js --------*/
    function testimonialTwo(){
        var testimonialstwo = $(".testimonial_slider_eight");
        if( testimonialstwo.length ){
            testimonialstwo.owlCarousel({
                loop:true,
                margin:0,
                items:1,
                autoplay:true,
                autoplayHoverPause: true,
                smartSpeed:1000,
                nav: false,
                responsiveClass:true,
                responsive:{
                    0:{
                        items:1, 
                    },
                    720:{
                        items:1, 
                    }, 
                },
                
            });
        }
    }
    testimonialTwo();
    
     /*=============================================== 
      Skrollr Init
    ================================================*/
    var mySkrollr = skrollr.init({
        forceHeight: false,
        easings: {
            easeOutBack: function (p, s) {
                s = 1.70158;
                p = p - 1;
                return (p * p * ((s + 1) * p + s) + 1);
            }
        },
        mobileCheck: function() {
            //hack - forces mobile version to be off
            return false;
        }
    });

    
    /*-------------------------------
        GALLERY SLIDER 
    --------------------------------*/
    function getSlide() {
        var wW = $(window).width();
        if (wW < 601) {
            return 1;
        }
        return 3;
    }
    var mySwiper = $('.swiper-container').swiper({
        mode: 'horizontal',
        autoplay: 3000,
        loop: true,
        speed: 400,
        slidesPerView: 1,
        effect: 'coverflow',
        slidesPerView: getSlide(),
        grabCursor: true,
        nextButton: '.swiper-button-next',
        prevButton: '.swiper-button-prev',
        keyboardControl: true,
        pagination:'.swiper-pagination',
        paginationClickable: true,
        coverflow: {
            rotate: 0,
            stretch: 0,
            depth: 170,
            modifier: 1,
            slideShadows: false
        }
    });

    
    /*==========Start player js ===========*/
    // poster frame click event
    $(".js-videoPoster").on('click',function(ev) {
        ev.preventDefault();
        var $poster = $(this);
        var $wrapper = $poster.closest('.js-videoWrapper');
        videoPlay($wrapper);
    });

    // play the targeted video (and hide the poster frame)
    function videoPlay($wrapper) {
        var $iframe = $wrapper.find('.js-videoIframe');
        var src = $iframe.data('src');
        // hide poster
        $wrapper.addClass('videoWrapperActive');
        // add iframe src in, starting the video
        $iframe.attr('src',src);
    }
    // stop the targeted/all videos (and re-instate the poster frames)
    $(".play-btn").on("click",function(ev){
        var $wrapper = $('.js-videoWrapper');
        var $iframe = $wrapper.find('.js-videoIframe');
        var src = $iframe.data('src'); 
        if( $wrapper.hasClass('videoWrapperActive')){
            $wrapper.removeClass('videoWrapperActive');
            $iframe.attr('src','');
        }
        else{
            $wrapper.addClass('videoWrapperActive');
            $iframe.attr('src',src);
        }
        return false;
    });
    
    /*===========Start clients logo js ===========*/
    function clientsSlider(){
        var clientslg_slider = $(".clients-lg-slider");
        if( clientslg_slider.length){
            clientslg_slider.owlCarousel({
                loop:true,
                margin:0,
                items:4,
                autoplay:true,
                smartSpeed:1000,
                autoplayHoverPause: true,
                nav: false,
                responsiveClass:true,
                responsive:{
                    0:{
                        items:1, 
                    },
                    420:{
                        items:2, 
                    },
                    550:{
                        items:3, 
                    }, 
                    992:{
                        items:4,   
                    }
                },
            })
        }
    }
    clientsSlider();
    /*===========End clients logo js ===========*/
    
    
    /*===========Start clients logo js ===========*/
    function teamSlider(){
        var team = $(".team-slider");
        if( team.length){
            team.owlCarousel({
                loop:true,
                margin:0,
                items:3,
                autoplay:true,
                center: true,
                smartSpeed:1000,
                autoplayHoverPause: true,
                nav: false,
                responsiveClass:true,
                responsive:{
                    0:{
                        items:1, 
                    },
                    420:{
                        items:2, 
                    },
                    550:{
                        items:3, 
                    }
                },
            })
        }
    }
    teamSlider();
    /*===========End clients logo js ===========*/

    
    // preloader js
    $(window).on('load',function() { // makes sure the whole site is loaded
		$('.loader-container').fadeOut(); // will first fade out the loading animation
		$('.loader').delay(150).fadeOut('slow'); // will fade out the white DIV that covers the website.
		$('body').delay(150).css({'overflow':'visible'})
    })
    
    var slider_text = $('.text_slider');

    function text_slider() {
        if (slider_text.length) {
            slider_text.owlCarousel({
                loop: false,
                margin: 0,
                dots: false,
                autoplay: true,
                mouseDrag: true,
                touchDrag: true,
                animateOut: 'slideOutUp',
                animateIn: 'fadeInUp',
                navSpeed: 500,
                items: 1,
                smartSpeed: 2500,
            })
        }
    }
    text_slider();
    var slider_bg = $('.screenshot_carousel');

    function home_slider() {
        if (slider_bg.length) {
            slider_bg.owlCarousel({
                loop: false,
                margin: 68,
                dots: false,
                autoplay: true,
                mouseDrag: true,
                touchDrag: true,
                items: 3,
                smartSpeed: 2500,
                responsiveClass:true,
                responsive:{
                    0:{
                        items:1
                    },
                    500:{
                        items:3,
                        margin:10
                    },
                    768:{
                        items:3,
                        margin:30
                    },
                    1199:{
                        items:2,
                        margin :150,
                    },
                    1450:{
                        items:3, 
                    }
                }
            })
        }
    }
    home_slider();
    $('.home_screen_nav .testi_next').on('click', function() {
        slider_text.trigger('next.owl.carousel');
        slider_bg.trigger('next.owl.carousel');
    });
    $('.home_screen_nav .testi_prev').on('click', function() {
        slider_text.trigger('prev.owl.carousel');
        slider_bg.trigger('prev.owl.carousel');
    });
    
    //open and close popup
	$(document).on('click', '.open-popup', function(){
		$('.popup-content').removeClass('active');
		$('.popup-wrapper, .popup-content[data-rel="'+$(this).data('rel')+'"]').addClass('active');
		$('html').addClass('overflow-hidden');
        $('.simple-input').focus();
		return false;
	});

	$(document).on('click', '.popup-wrapper .button-close, .popup-wrapper .layer-close', function(){
		$('.popup-wrapper, .popup-content').removeClass('active');
		$('html').removeClass('overflow-hidden');
		setTimeout(function(){
			$('.ajax-popup').remove();
		},300);
		return false;
	});
    
    // video Popup
    if ($("#video-item").length > 0){
        $("#video-item").magnificPopup({
            type: "iframe"
        });
    }
    
    function bgVideo(){
        $("#bgndVideo").YTPlayer();
    }
    bgVideo();
    
    
    $('.testimonial_slider_five').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        dots: true,
        asNavFor: '.testimonial-img'
    });
    $('.testimonial-img').slick({
        slidesToShow: 1,
        Padding: '0px',
        slidesToScroll: 1,
        asNavFor: '.testimonial_slider_five',
        dots: false,
        arrows: false,
        fade: true,
    });
    
});

