// console.log(window.location.hostname);
if ( (window.location.href !== `http://${window.location.hostname}/`) ||
     (window.location.href !== 'https://${window.location.hostname}/') ) {
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

let productSelect = $('[name=\'Order[product][id][]\']').parent().clone();
let productCount = $('[name=\'Order[product][count][]\']').parent().clone();
$('.add_product').on('click', (e) => {
    e.preventDefault();
    productSelect.clone().insertBefore($(e.currentTarget));
    productCount.clone().insertBefore($('.form_btn')[1]);
});
$("[name='Client[phone]']").mask("+7(999)999 9999");
$(".product_order > .btn").on("click", (e) => {
    e.preventDefault();
    let product_id = $(e.currentTarget).closest(".product_item").attr("data-key");
    let count = $($(e.currentTarget).siblings()).find("[type=text]").val();
    let product_item = $("[name='Order[product][id][]']");
    let product_count = $("[name='Order[product][count][]']");
    $(product_item[product_item.length - 1]).val(product_id);
    $(product_count[product_count.length - 1]).val(count);
    console.log(product_count.length);
    $(".form_tab > button:last-child").trigger("click");
    $(".add_product").trigger("click");
})
