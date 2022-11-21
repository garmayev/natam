$(document).ready(function () {

    AOS.init();
    window.onload = function () {
        document.querySelector(".preloader").classList.add("active");
    };
    let newsSlider = $(".news_slider");
    if (newsSlider) {
        newsSlider.slick({
            dots: false,
            infinite: true,
            slidesToShow: 3,
            slidesToScroll: 1,
            responsive: [
                {
                    breakpoint: 1000,
                    settings: {
                        slidesToShow: 2,
                    },
                },
                {
                    breakpoint: 630,
                    settings: {
                        slidesToShow: 1,
                    },
                },
            ],
        });
    }
    let aboutSlider = $(".about_slider");
    if (aboutSlider) {
        aboutSlider.slick({
            dots: false,
            infinite: true,
            slidesToShow: 1,
            slidesToScroll: 1,
        });
    }
    $(".nav_toggle, .close, .shadow").on("click", function () {
        $(".nav").toggleClass("active");
        $(".shadow").toggleClass("active");
    });
    $(".product_more, .product_img > img").on("click", function () {
        $(this).closest(".product_item").toggleClass("active");
    });

    $(".form_tab button").on("click", function () {
        $(".form_tab button").removeClass("active");
        $(this).addClass("active");
        if ($(".form_tab button:last-child").hasClass("active")) {
            $(".form_block").removeClass("active");
            $(".form_order").addClass("active");
        } else {
            $(".form_block").removeClass("active");
            $(".form_submit").addClass("active");
        }
    });

    let num;
    $(".plus").on("click", function (e) {
        num = parseInt($(this).prev().val());
        console.log($(e));
        $(this)
            .prev()
            .val(num + 1);
    });
    $(".minus").on("click", function () {
        num = parseInt($(this).next().val());
        if (num > 1) {
            $(this)
                .next()
                .val(num - 1);
        }
    });
});

let drill = document.querySelector('.journal')

window.addEventListener("scroll", function () {
    let value = -window.scrollY;

    if (drill != undefined) {
        if (value > -1020) {
            drill.style.top = 0 + value * -0.03 + "%";

        }
    }


});

let gaz = document.querySelector('.gaz')

window.addEventListener("scroll", function () {
    let value = -window.scrollY;


    if (value > -4200) {
        if (value < -3550) {
            gaz.style.top = -210 + value * -0.05 + "%";

        }

    }
    console.log('gaz ' + value)

});

