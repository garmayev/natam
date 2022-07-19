    let button = $(".form_tab button:first-child");

    let counter = 0;

    $(".main_inner > .blue, .main_content > .blue").on("click", (e) => {
        if (!$(e.currentTarget).hasClass('next_step')) {
            // e.preventDefault();
            // console.log("CLICK")
            // $("html, body").animate({
            //     scrollTop: $("#product").offset().top
            // });
        }
    })

    $.ajax({
        url: '/cart/get-products',
        success: (response) => {
            products = response;
            rebuild();
        }
    })

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
        counter = 0;

        function build_select(options) {
            let attr = {};
            let select_container = createElement("div", null, {class: 'form_select'});
            let select_element = createElement("select", null, {
                value: options.product_id,
                "name": "Order[orderProduct][" + counter + "][product_id]"
            });
            for (const index in products) {
                // console.log(index == parseInt(options.product_id));
                if (index == parseInt(options.product_id)) {
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
                            name: `Order[orderProduct][${counter}][product_count]`,
                            style: "width: 80%",
                            // disabled: 'disabled',
                        });
                        // let count_hidden = createElement("hidden", response[index].quantity, {
                        //     name: `Order[orderProduct][${counter}][product_count]`,
                        //     value: response[index].quantity,
                        // });
                        let drop = createElement("a", null, {
                            class: "trash",
                            "data-index": response[index].product_id
                        }, {click: remove});
                        $(drop).append(createElement("img", null, {src: "/img/trash.png", style: "width: 36px;"}));
                        let select_container = createElement("div", null);
                        select_container.appendChild(count);
                        // select_container.appendChild(count_hidden);
                        // console.log(count_hidden);
                        select_container.appendChild(drop);
                        console.log(select_container);
                        container.find(".form_item:last-child").append(select_container);
                        counter++;
                    }
                } else {
                    container.find(".form_item:first-child").html("");
                    container.find(".form_item:last-child").html("");
                    container.find(".form_item:first-child").html("<p style='color: white; font-size: 28px;'>Корзина пуста</p><a href='#product' class='btn' style='color: #fff;'>Ознакомтесь с каталогом</a>");
                }
            }
        })
    }

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
        counter--;
    }

    if ($('.alert').html() !== '') {
        setTimeout(function () {
            $('.alert').animate({
                opacity: 0,
            }, 2000, function () {
                $(this).hide();
            })
        }, 5000);
    }

    $("#order-client-phone, #ticket-client-phone").mask("+7(999) 999 9999")

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
        if (button.length) {
            $(".form_tab button:first-child").trigger("click");
            $('html, body').animate({
                scrollTop: $('#form').offset().top
            });
        } else {
            window.location.href = "/#form"
        }
    });

    if ((window.location.pathname !== `/`)) {
        $("body").removeClass("home");
        $(".header").css({"max-height": "200px", "min-height": "200px"});
    } else {
        if (window.location.hash === "#form") {
            if ( button.length ) {
                // console.log(button);

                button.trigger("click");
            }
        }
        let about_slider = $('.about_slider');
        // if (about_slider.length) {
        //     about_slider.slick({
        //         dots: false,
        //         infinite: true,
        //         slidesToShow: 1,
        //         slidesToScroll: 1,
        //         responsive: [
        //             {
        //                 breakpoint: 1000,
        //                 settings: {
        //                     slidesToShow: 1,
        //                 },
        //             },
        //             {
        //                 breakpoint: 630,
        //                 settings: {
        //                     slidesToShow: 1,
        //                 },
        //             },
        //         ],
        //     });
        // }
    }
