var swiper1 = new Swiper(".header-swiper", {
    slidesPerView: 'auto',
    spaceBetween: 10,
    // speed: 300,
    breakpoints: {
        100: {
            speed: 10000,

        },
        769: {
            speed: 8000,

        }
    },
    loop: true,

    //allowTouchMove: false, // можно ещё отключить свайп
    autoplay: {
        delay: 0,
        disableOnInteraction: false // или сделать так, чтобы восстанавливался autoplay после взаимодействия
    }
});


let swiperMentors = new Swiper(".mentors__slider", {
    slidesPerView: 'auto',
    spaceBetween: 0,
    speed: 5000,
    loop: true,
    allowTouchMove: true, // можно ещё отключить свайп
    autoplay: {
        delay: 0,
        // pauseOnMouseEnter: true,
        reverseDirection: false,
        disableOnInteraction: false // или сделать так, чтобы восстанавливался autoplay после взаимодействия
    }
});

// swiperMentors.on('slideChange', function () {
//     let nextSlide = swiperMentors.activeIndex + 1;
//     let slideNoClass = $('.mentors__slider .swiper-wrapper').find('.mentors-one-animation');
//     // let nexrinside = nextSlide.firstChild;
//     let nextAfterSlide = swiperMentors.activeIndex + 2;
//     let nextAfterAfterSlide = swiperMentors.activeIndex + 3;
//     let activeSlide = $('.mentors__slider .swiper-wrapper').find('.mentors-one-animation').get(swiperMentors.activeIndex)
//     let slide = $('.mentors__slider .swiper-wrapper').find('.mentors-one-animation').get(nextAfterSlide);
//     let slide2 = $('.mentors__slider .swiper-wrapper').find('.mentors-one-animation').get(nextSlide);
//     let slide3 = $('.mentors__slider .swiper-wrapper').find('.mentors-one-animation').get(nextAfterAfterSlide);
//     $(slide2).addClass("show");
//     // $(slide2).removeClass("show");
//     // $(slide3).addClass("show");
//     // $(slide2).removeClass("slide3");
//    if ($('.mentors__slide7').hasClass('swiper-slide-active')) {
//        // console.log( $('.swiper-slide').find('.mentors__slide-block'))
//        $('.swiper-slide-duplicate').addClass('show');
//        $('.swiper-slide').find('.mentors__slide-block').removeClass('mentors__slide-animation11');
//        $('.swiper-slide').find('.mentors__slide-block').removeClass('mentors__slide-animation12');
//        $('.swiper-slide').find('.mentors__slide-block').removeClass('mentors__slide-animation13');
//        $('.swiper-slide').find('.mentors__slide-block').removeClass('mentors__slide-animation21');
//        $('.swiper-slide').find('.mentors__slide-block').removeClass('mentors__slide-animation22');
//        $('.swiper-slide').find('.mentors__slide-block').removeClass('mentors__slide-animation23');
//        $('.swiper-slide').find('.mentors__slide-block').removeClass('mentors__slide-animation31');
//        $('.swiper-slide').find('.mentors__slide-block').removeClass('mentors__slide-animation32');
//        $('.swiper-slide').find('.mentors__slide-block').removeClass('mentors__slide-animation33');
//        // $('.swiper-slide').removeClass('mentors-one-animation');
//    }
//   else  if ($('.mentors__slide11').hasClass('swiper-slide-active')) {
//         $('.swiper-slide').removeClass('mentors-one-animation');
//        $('.swiper-slide').addClass('show');
//     }
//
// });


if($(window).width() < 998){
    var swiper2 = new Swiper(".js-nossos-cursos-formacao-slider", {
        spaceBetween: 30,
        slidesPerView: 'auto',
        allowTouchMove: 'false',
        // loop: true,
        speed: 1000,
    });

    var swiper2 = new Swiper(".js-nossos-cursos-block-slider", {
        // spaceBetween: 15,
        slidesPerView: 'auto',
        // loop: true,
        speed: 1000,

    });
}

var swiperPR = new Swiper(".principais-recursos__slider", {
    spaceBetween: 10,
    slidesPerView: 1,
    speed: 1000,
    // loop: true,
    pagination: {
        el: ".swiper-pagination2",
        clickable: true,
    },
    navigation: {
        nextEl: ".principais-recursos__next",
        prevEl: ".principais-recursos__prev",
    },
});

var swiper = new Swiper(".feedback__flex-slider", {
    slidesPerView: 'auto',
    // spaceBetween: 0,
    keyboard: true,
    speed: 1000,
    pagination: {
        el: ".feedback__flex-slider-pagination",
        clickable: true,
    },
    navigation: {
        nextEl: ".feedback__flex-slider-next",
        prevEl: ".feedback__flex-slider-prev",
    },
});

if ($(window).width() < 769) {
    $(document).on('click', '.curso-functiona__titles-block', function (e) {
        var blockFaq = $(this).parents(".curso-functiona__block");
        if (!$(blockFaq).hasClass("active")) {
            $(".curso-functiona__block").removeClass("active");
            $(".curso-functiona__text-block").hide(300);
            $(blockFaq).addClass("active");
            $(blockFaq).find(".curso-functiona__text-block").show(300);
        } else {
            $(".curso-functiona__block").removeClass("active");
            $(".curso-functiona__text-block").hide(300);
        }
    });
}

$(document).ready(function () {
    $(".js-header__link").on("click", "a", function (event) {
        event.preventDefault();
        var id = $(this).attr('href'),
            top = $(id).offset().top;
        $('body,html').animate({scrollTop: top}, 1500);
    });
});

$(document).ready(function () {
    $(".feedback__btn").on("click", "a", function (event) {
        event.preventDefault();
        var id = $(this).attr('href'),
            top = $(id).offset().top;
        $('body,html').animate({scrollTop: top}, 1500);
    });
});

$("#spec-input").inputmask("+55 (99) 99999-9999");

AOS.init({
    // duration: 1500,
    once: true,
});

// $(document).ready ( function(){
//     if ($(".swiper-slide").hasClass('.swiper-slide-next')){
//         let w = $(this).children('.mentors__slide-cover');
//         console.log(w);
//     }
// });
