if ((window.location.pathname !== `/`)) {
    $("body").removeClass("home");
}

$('.about_slider').slick({
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
$(".main_inner > .blue").on("click", () => {
    $("html, body").animate({
        scrollTop: $("#product").offset().top
    });
})

if ($('.alert').html() !== '') {
    setTimeout(function () {
        $('.alert').animate({
            opacity: 0,
        }, 1000, function () {
            $(this).hide();
        })
    }, 5000);
}

$('.product_order > a.btn').on('click', (e) => {
    if ( !$(e.currentTarget).hasClass('disabled') ) {
        let card = $(e.currentTarget).closest('.product_item');
        data = `id=${card.find('.cart_product_id').val()}&count=${card.find('.cart_product_count').val()}`
        console.log(data);
        $.ajax({
            url: '/cart/add',
            data: data,
            type: 'GET',
            success: (response) => {
                console.log(response);
            }
        })
    }
    e.preventDefault();
})

// $(".modal")

setInterval(() => {
    $.pjax.reload({container: '#cart-pjax'});
}, 1000)

