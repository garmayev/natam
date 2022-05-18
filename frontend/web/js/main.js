$(document).ready(function () {
    AOS.init();
    window.onload = function () {
        document.querySelector(".preloader").classList.add("active");
    };
    $(".news_slider").slick({
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
    if ($(".about_slider").length > 0) {
	$(".about_slider").slick({
    	    dots: false,
    	    infinite: true,
    	    slidesToShow: 1,
    	    slidesToScroll: 1,
    	    responsive: [
        	{
            	    breakpoint: 1000,
            	    settings: {
                	slidesToShow: 1,
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
    $(".plus").on("click", function () {
        num = parseInt($(this).prev().val());
        console.log(num);
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
