var swiper1 = new Swiper(".header-swiper", {
    slidesPerView: 'auto',
    spaceBetween: 10,
    speed: 3000,
    breakpoints: {
        100: {
            speed: 5000,

        },
        769: {
            speed: 3000,

        }
    },
    loop: true,

    //allowTouchMove: false, // можно ещё отключить свайп
    autoplay: {
        delay: 0,
        disableOnInteraction: false // или сделать так, чтобы восстанавливался autoplay после взаимодействия
    }
});

var swiperMentors = new Swiper(".mentors__slider", {
    slidesPerView: 'auto',
    spaceBetween: 0,
    speed: 4000,
    breakpoints: {
        100: {
            speed: 5000,

        },
        769: {
            speed: 4000,

        }
    },
    loop: true,
    allowTouchMove: true, // можно ещё отключить свайп
    autoplay: {
        delay: 0,
        // pauseOnMouseEnter: true,
        reverseDirection:false,
        disableOnInteraction: false // или сделать так, чтобы восстанавливался autoplay после взаимодействия
    }
});

var swiper2 = new Swiper(".nossos-cursos__slider", {
    spaceBetween: 30,
    slidesPerView: 'auto',
    allowTouchMove: 'false',
    loop: true,
    speed: 1000,
    pagination: {
        el: ".swiper-pagination1",
        // clickable: true,
    },
    navigation: {
        nextEl: ".nossos-cursos__next",
        prevEl: ".nossos-cursos__prev",
    },
    breakpoints: {
        100: {
            spaceBetween: 26,
            allowTouchMove: 'false',

        },
        769: {
            spaceBetween: 30,
            allowTouchMove: 'false',

        }
    }
});

var swiper10 = new Swiper(".principais-recursos__slider", {
    spaceBetween: 10,
    slidesPerView: 1,
    speed: 1000,
    loop: true,

    pagination: {
        el: ".swiper-pagination2",
        clickable: true,
    },
    navigation: {
        nextEl: ".principais-recursos__next",
        prevEl: ".principais-recursos__prev",
    },
});


if ($(window).width() < 992) {
    var swiper = new Swiper(".feedback__flex-slider", {
        slidesPerView: 'auto',
        // spaceBetween: 0,
        mousewheel: true,
        keyboard: true,
        speed: 1000,
    });
}

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


// AOS.init({
//     duration: 1500,
//     once: true,
// });



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
