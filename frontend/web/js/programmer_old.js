$.fn.maskVal = function () {
    return $(this).mask();
};

if ((window.location.pathname !== `/`)) {
    $("body").removeClass("home");
    $(".header").css({"max-height": "200px"});
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
$(".main_inner > .blue").on("click", (e) => {
    if (!$(e.currentTarget).hasClass('next_step')) {
        e.preventDefault();
        console.log("CLICK")
        // $("html, body").animate({
        //     scrollTop: $("#product").offset().top
        // });
    }
})

if ($('.alert').html() !== '') {
    setTimeout(function () {
        $('.alert').animate({
            opacity: 0,
        }, 2000, function () {
            $(this).hide();
        })
    }, 5000);
}

// console.log( $($(".form_block > .step")[0]) );
$($(".form_block > .step")[0]).attr("style", "display: flex");

function createElement(tag, value, attr, event) {
    let el = document.createElement(tag);
    for (const attrKey in attr) {
        el.setAttribute(attrKey, attr[attrKey]);
    }
    for (const eventKey in event) {
        el.addEventListener(eventKey, event[eventKey]);
    }
    el.innerHTML = value;
    return el;
}

function rebuild() {
    function build_select(options) {
        // console.log(options);
        let attr = {};
        let select_container = createElement("div", null, {class: 'form_select'});
        let select_element = createElement("select", null, {value: options.product_id, "name": "Order[product][id][]"});
        for (const index in products) {
            if (index == options.product_id) {
                attr = {value: index, "selected": "selected"};
            } else {
                attr = {value: index};
            }
            let option = createElement("option", products[index], attr);
            select_element.appendChild(option);
        }
        select_container.appendChild(select_element);
        return select_container;
    }

    $.ajax({
        url: '/cart/get-cart',
        type: 'GET',
        success: (response) => {
            let container = $('.step[data-index=1]');
            if (response.length > 0) {
                container.find(".form_item:first-child").html("");
                container.find(".form_item:last-child").html("");
                for (const index in response) {
                    let select = build_select(response[index]);
                    $(select).val(response[index].product_id);
                    container.find(".form_item:first-child").append(select);
                    let count = createElement("input", null, {
                        type: 'text',
                        value: response[index].quantity,
                        // name: "Order[product][count][]",
                        style: "width: 80%",
                    });
                    let count_hidden = createElement("hidden", response[index].quantity, {
                        name: "Order[orderProduct]["+counter+"][product_count]"
                    });
                    let drop = createElement("a", null, {
                        class: "trash",
                        "data-index": response[index].product_id
                    }, {click: remove});
                    $(drop).append(createElement("img", null, {src: "/img/trash.png", style: "width: 36px;"}));
                    let select_container = createElement("div", null);
                    select_container.appendChild(count);
                    select_container.appendChild(count_hidden);
                    select_container.appendChild(drop);
                    container.find(".form_item:last-child").append(select_container);
                }
                let btn_container = createElement("div", null, {class: 'form_btn'});
                let btn = createElement("a", "Следующий шаг", {
                    class: 'btn blue next',
                    style: "float: right;"
                }, {click: next});
                btn_container.appendChild(btn);
                container.find(".form_item:last-child").append(btn_container);
            } else {
                container.find(".form_item:first-child").html("");
                container.find(".form_item:last-child").html("");
                container.find(".form_item:first-child").html("<p style='color: white; font-size: 28px;'>Корзина пуста</p><a href='#product' class='btn' style='color: #fff;'>Ознакомтесь с каталогом</a>");
            }
        }
    })
}

$("[data-index] .form_btn a.next").on('click', next);
$("[data-index] .form_btn a.prev").on('click', (e) => {
    e.preventDefault();

    let currentElement = $(e.currentTarget).closest(".step");
    let previousElement = currentElement.prev();
    if (previousElement.length) {
        currentElement.attr("style", "display: none");
        previousElement.attr("style", "display: flex;");
    }
    step--;
})
$("#order-client-phone, #ticket-client-phone").mask("+7(999)999 9999");

function remove(e) {
    e.preventDefault();
    let target = $(e.currentTarget);
    let id = target.attr("data-index");
    $.ajax({
        url: "/cart/remove",
        data: {id: id},
        type: "GET",
        success: () => {
            rebuild();
            target.parent().remove();
        }
    });
}

let myMap = undefined,
    myPlacemark = undefined;

function next(e) {
    e.preventDefault();
    console.log( $(e.currentTarget) );
    let container = $(e.currentTarget).closest("[data-index]");
    let required = container.find("input[data-required=true]");

    for (let i = 0; i < required.length; i++) {
        if ( $(required[i]).val() === "" ) {
            $(required[i]).val()
            return false;
        }
    }

    let prev_element = $(`.step[data-index=${step}]`);
    let next_element = $(`.step[data-index=${step + 1}]`);

    let delivery_field = $("#order-delivery_date");
    let address_field = $("#order-address");
    let phone_field = $("#order-client-phone");
    let name_field = $("#order-client-name");
    if (next_element.length) {
        switch (step) {
            case 1:
                prev_element.attr("style", "display: none");
                next_element.attr("style", "display: flex;");
                step++;
                break;
            case 2:
                console.log(name_field.val());
                if (name_field.val() === '') {
                    e.preventDefault();
                    alert("Требуется заполнить поле \"Ваше ФИО\"");
                    return false;
                }
                if ( phone_field.val() === '' ) {
                        e.preventDefault();
                        alert("Требуется заполнить поле \"Ваш номер телефона\"");
                        return false;
                }
                prev_element.attr("style", "display: none");
                next_element.attr("style", "display: flex;");
                if (myMap === undefined) {
                    myMap = new ymaps.Map('map', {
                        center: [51.76, 107.64],
                        zoom: 12
                    }, {});
                }
                $('#order-address').suggestions({
                    token: '2c9418f4fdb909e7469087c681aac4dd7eca158c',
                    type: 'ADDRESS',
                    constraints: {
                        locations: {region: 'Бурятия'},
                    },
                    onSelect: function (suggestion) {
                        ymaps.geocode(suggestion.value, {
                            results: 1
                        }).then(function (res) {
                            let placemark = res.geoObjects.get(0),
                                coords = placemark.geometry.getCoordinates(),
                                bounds = placemark.properties.get('boundedBy');

                            if (myPlacemark) {
                                myPlacemark.geometry.setCoordinates(coords);
                            } else {
                                myPlacemark = createPlacemark(coords);
                                myMap.geoObjects.add(myPlacemark);
                                myPlacemark.events.add('dragend', function () {
                                    getAddress(myPlacemark.geometry.getCoordinates());
                                });
                            }
                            myMap.setBounds(bounds, {
                                checkZoomRange: true
                            });
                            getAddress(coords);
                        });
                    }
                });

                myMap.events.add('click', function (e) {
                    let coords = e.get('coords');

                    if (myPlacemark) {
                        myPlacemark.geometry.setCoordinates(coords);
                    } else {
                        myPlacemark = createPlacemark(coords);
                        myMap.geoObjects.add(myPlacemark);
                        myPlacemark.events.add('dragend', function () {
                            getAddress(myPlacemark.geometry.getCoordinates());
                        });
                    }
                    getAddress(coords);
                });

                // Создание метки.
            function createPlacemark(coords) {
                return new ymaps.Placemark(coords, {
                    iconCaption: 'поиск...'
                }, {
                    preset: 'islands#violetDotIconWithCaption',
                    draggable: true
                });
            }

            function getAddress(coords) {
                myPlacemark.properties.set('iconCaption', 'поиск...');
                ymaps.geocode(coords).then(function (res) {
                    var firstGeoObject = res.geoObjects.get(0);
                    let address = firstGeoObject.getAddressLine();

                    myPlacemark.properties
                        .set({
                            iconCaption: [
                                firstGeoObject.getLocalities().length ? firstGeoObject.getLocalities() : firstGeoObject.getAdministrativeAreas(),
                                firstGeoObject.getThoroughfare() || firstGeoObject.getPremise()
                            ].filter(Boolean).join(', '),
                            balloonContent: firstGeoObject.getAddressLine()
                        });
                    $('#order-address').val(address);
                    $('#location-title').val(address);
                });
                $('#location-latitude').val(coords[0]);
                $('#location-logintude').val(coords[1]);
            }

                prev_element.attr("style", "display: none");
                next_element.attr("style", "display: flex;");
                step++;
                break;
            case 3:
                break;
        }
    } else {
        console.log("DIE!!!");
    }
}

$('.product_order > a.btn').on('click', (e) => {
    if (!$(e.currentTarget).hasClass('disabled')) {
        let card = $(e.currentTarget).closest('.product_item');
        data = `id=${card.find('.cart_product_id').val()}&count=${card.find('.cart_product_count').val()}`
        $.ajax({
            url: '/cart/add',
            data: data,
            type: 'GET',
            success: (response) => {
                window.location.href = '/#form';
                rebuild();
            }
        })
    }
    e.preventDefault();
})

let step = 1;
var products;

$.ajax({
    url: '/cart/get-products',
    success: (response) => {
        products = response;
        rebuild();
    }
})

$('.header_inner .blue').on('click', (e) => {
    if (window.location.href === window.location.hostname) {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: $('#form').offset().top
        });
        $('.form_tab > button:last-child').trigger('click');
    } else {
        window.location.href = '/#product';
    }
})

$(".cart_product_id").on('change', (e) => {
    let target = $(e.currentTarget);
    let product_id = target.val();
    $.ajax({
        url: '/product/get-product',
        data: {id: product_id},
        success: (response) => {
            console.log(response);
            target.next().html(response.price + "<span> руб.</span>");
        }
    })
});

$(".btn.recall").on("click", (e) => {
	e.preventDefault();
	$(".form_tab button:first-child").trigger("click");
        $('html, body').animate({
            scrollTop: $('#form').offset().top
        });
});

$(".form-order .form_btn .next").on("click", (e) => {
})