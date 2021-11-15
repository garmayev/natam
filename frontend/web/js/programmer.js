//console.log(window.location);
if ( (window.location.pathname !== `/`) ) {
//    (window.location.href !== `http://${window.location.hostname}/`)) {
    $("body").removeClass("home");
    $(".recall").on("click", (e) => {
        e.preventDefault();
        $("#modal").addClass("active");
    });
    $('#modal > .modal_shadow').on("click", (e) => {
        e.preventDefault();
        $("#modal").removeClass("active");
    })
} else {
    $('.recall').on("click", (e) => {
        e.preventDefault();
        // console.log($("#form").offset().top);
        $("html, body").animate({
            scrollTop: $("#form").offset().top
        });
        $(".form_tab > button:first-child").trigger("click");
    });
}

$(".main_inner > .blue").on("click", () => {
    $("html, body").animate({
        scrollTop: $("#product").offset().top
    });
})

$(".product .blue").on("click", () => {
    $("html, body").animate({
        scrollTop: $("#form").offset().top
    });
});

if ( $('.alert').html() !== '' ) {
    setTimeout(function () {
	$('.alert').animate({
	    opacity: 0,
	}, 1000, function () {
		$(this).hide();
	})
    }, 5000)
}

let productSelect = $('[name=\'Order[product][id][]\']').parent().clone();
let productCount = $('[name=\'Order[product][count][]\']').parent().clone();

$('.add_product').on('click', (e) => {
    e.preventDefault();
    productSelect.clone().insertBefore($(e.currentTarget));
    productCount.clone().insertBefore($('.form_btn')[1]);
});
$("[name='Client[phone]']").mask("+7(999)999 9999");

function getSelectedProductItem(id)
{
    let product_item = $("select");
    for (let i = 0; i < product_item.length; i++) {
        if ( $(product_item[i]).val() === id ) {
            let item = product_item[i];
            // console.log($($(item).closest(".form_item").next().find("[type=text]")[i]));
            return {"item": $(item), "count": $($(item).closest(".form_item").next().find("[type=text]")[i])};
        }
    }
    return undefined;
}

$(".product_order > .btn").on("click", (e) => {
    e.preventDefault();
    console.log($(this).hasClass("disabled"));
    if ( !$(this).hasClass("disabled") ) {
        let product_id = $(e.currentTarget).closest(".product_item").attr("data-key");
        let count = $($(e.currentTarget).siblings()).find("[type=text]").val();
        let selected = getSelectedProductItem(product_id);
        let product_item;
        if (selected !== undefined) {
            // product_item = selected.item;
            selected.count.val(parseInt(selected.count.val()) + parseInt(count));
        } else {
            product_item = $("[name='Order[product][id][]']");
            let product_count = $("[name='Order[product][count][]']");
            $(".add_product").trigger("click");
            $(product_item[product_item.length - 1]).val(product_id);
            $(product_count[product_count.length - 1]).val(count);
        }
        $(".form_tab > button:last-child").trigger("click");
    }
})
