<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<main>
    <section class="form" id="form">
        <div class="container-fluid">
            <div class="form_inner">
                <img
                        src="/img/journal.png"
                        class="journal"
                        alt="journal"
                />
                <div class="form_tab">
                    <button class="active">
                        ОСТАВИТЬ <br />
                        ЗАЯВКУ
                    </button>
                    <button>ЗАКАЗАТЬ</button>
                </div>
                <form class="form_block form_submit active">
                    <label>
                        Наш менеджер свяжется с вами в ближайшее время
                    </label>
                    <div class="form_content">
                        <div class="form_item">
                            <input type="text" placeholder="Ваше ФИО" />
                            <input
                                    type="text"
                                    placeholder="+ 7 ( ____ ) - ___ - __ - __"
                            />
                            <input
                                    type="email"
                                    placeholder="Ваш E-mail"
                            />
                            <input
                                    type="text"
                                    placeholder="Название организации"
                            />
                        </div>
                        <div class="form_item">
                            <div class="form_select">
                                <select>
                                    <option>Товары</option>
                                    <option>1</option>
                                    <option>2</option>
                                </select>
                            </div>
                            <div class="form_select">
                                <select>
                                    <option>Количество</option>
                                    <option>1</option>
                                    <option>2</option>
                                </select>
                            </div>
                            <div class="form_btn">
                                <div class="form_policy">
                                    <input
                                            type="checkbox"
                                            id="form_policy"
                                    />
                                    <label for="form_policy">
                                        Даю согласие на обработку
                                        персональных данных
                                    </label>
                                </div>
                                <button type="submit" class="btn blue">
                                    отправить
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <form class="form_block form_order">
                    <label>
                        Таб Заказать Наш менеджер свяжется с вами в
                        ближайшее время
                    </label>
                    <div class="form_content">
                        <div class="form_item">
                            <input type="text" placeholder="Ваше ФИО" />
                            <input
                                    type="text"
                                    placeholder="+ 7 ( ____ ) - ___ - __ - __"
                            />
                            <input
                                    type="email"
                                    placeholder="Ваш E-mail"
                            />
                            <input
                                    type="text"
                                    placeholder="Название организации"
                            />
                        </div>
                        <div class="form_item">
                            <div class="form_select">
                                <select>
                                    <option>Товары</option>
                                    <option>1</option>
                                    <option>2</option>
                                </select>
                            </div>
                            <div class="form_select">
                                <select>
                                    <option>Количество</option>
                                    <option>1</option>
                                    <option>2</option>
                                </select>
                            </div>
                            <div class="form_btn">
                                <div class="form_policy">
                                    <input
                                            type="checkbox"
                                            id="form_policy"
                                    />
                                    <label for="form_policy">
                                        Даю согласие на обработку
                                        персональных данных
                                    </label>
                                </div>
                                <button type="submit" class="btn blue">
                                    отправить
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <section class="product">
        <div class="container">
            <div class="product_top">
                <h2 class="title">ОФОРМИТЬ ЗАКАЗ</h2>
                <a href="#" class="more">СМОТРЕТЬ ВСЕ</a>
            </div>
            <div class="product_inner">
                <div class="product_item">
                    <div class="product_backface">
                                <span
                                        title="more information"
                                        class="product_more"
                                >
                                    <img src="/img/info.svg" alt="info" />
                                </span>
                        <p>
                            Lorem ipsum dolor sit amet consectetur
                            adipisicing elit. Expedita iste natus
                            officiis? Dolores excepturi vel explicabo
                            debitis aliquid, molestiae, quam sunt sequi
                            expedita omnis porro ad consectetur at quos
                            dolore?
                        </p>
                    </div>
                    <div class="product_frontface">
                                <span
                                        title="more information"
                                        class="product_more"
                                >
                                    <img src="/img/info.svg" alt="info" />
                                </span>
                        <div class="product_img">
                            <img
                                    src="/img/product-1.png"
                                    alt="product"
                            />
                        </div>
                        <p class="product_item_title">
                            Баллон с аргоном
                        </p>
                        <p class="product_text">Объем/Масса: 6,4</p>
                        <p class="product_price">
                            1800
                            <span>руб.</span>
                        </p>
                        <div class="product_order">
                            <div class="product_count">
                                <button class="plus">+</button>
                                <input type="text" value="1" />
                                <button class="minus">-</button>
                            </div>
                            <a href="#" class="btn blue">заказать</a>
                        </div>
                        <span class="product_info">Есть в наличии</span>
                    </div>
                </div>
                <div class="product_item">
                    <div class="product_backface">
                                <span
                                        title="more information"
                                        class="product_more"
                                >
                                    <img src="/img/info.svg" alt="info" />
                                </span>
                        <p>
                            Lorem ipsum dolor sit amet consectetur
                            adipisicing elit. Expedita iste natus
                            officiis? Dolores excepturi vel explicabo
                            debitis aliquid, molestiae, quam sunt sequi
                            expedita omnis porro ad consectetur at quos
                            dolore?
                        </p>
                    </div>
                    <div class="product_frontface">
                                <span
                                        title="more information"
                                        class="product_more"
                                >
                                    <img src="/img/info.svg" alt="info" />
                                </span>
                        <div class="product_img">
                            <img
                                    src="/img/product-2.png"
                                    alt="product"
                            />
                        </div>
                        <p class="product_item_title">
                            Баллон с аргоном высокой чистоты
                        </p>
                        <p class="product_text">Объем/Масса: 6,4</p>
                        <p class="product_price">
                            3000
                            <span>руб.</span>
                        </p>
                        <div class="product_order">
                            <div class="product_count">
                                <button class="plus">+</button>
                                <input type="text" value="1" />
                                <button class="minus">-</button>
                            </div>
                            <a href="#" class="btn blue">заказать</a>
                        </div>
                        <span class="product_info">Есть в наличии</span>
                    </div>
                </div>
                <div class="product_item">
                    <div class="product_backface">
                                <span
                                        title="more information"
                                        class="product_more"
                                >
                                    <img src="/img/info.svg" alt="info" />
                                </span>
                        <p>
                            Lorem ipsum dolor sit amet consectetur
                            adipisicing elit. Expedita iste natus
                            officiis? Dolores excepturi vel explicabo
                            debitis aliquid, molestiae, quam sunt sequi
                            expedita omnis porro ad consectetur at quos
                            dolore?
                        </p>
                    </div>
                    <div class="product_frontface">
                                <span
                                        title="more information"
                                        class="product_more"
                                >
                                    <img src="/img/info.svg" alt="info" />
                                </span>
                        <div class="product_img">
                            <img
                                    src="/img/product-1.png"
                                    alt="product"
                            />
                        </div>
                        <p class="product_item_title">
                            Баллон c азотом газообразным
                        </p>
                        <p class="product_text">Объем/Масса: 6,4</p>
                        <p class="product_price">
                            700
                            <span>руб.</span>
                        </p>
                        <div class="product_order">
                            <div class="product_count">
                                <button class="plus">+</button>
                                <input type="text" value="1" />
                                <button class="minus">-</button>
                            </div>
                            <a href="#" class="btn blue">заказать</a>
                        </div>
                        <span class="product_info">Есть в наличии</span>
                    </div>
                </div>
                <div class="product_item">
                    <div class="product_backface">
                                <span
                                        title="more information"
                                        class="product_more"
                                >
                                    <img src="/img/info.svg" alt="info" />
                                </span>
                        <p>
                            Lorem ipsum dolor sit amet consectetur
                            adipisicing elit. Expedita iste natus
                            officiis? Dolores excepturi vel explicabo
                            debitis aliquid, molestiae, quam sunt sequi
                            expedita omnis porro ad consectetur at quos
                            dolore?
                        </p>
                    </div>
                    <div class="product_frontface">
                                <span
                                        title="more information"
                                        class="product_more"
                                >
                                    <img src="/img/info.svg" alt="info" />
                                </span>
                        <div class="product_img">
                            <img
                                    src="/img/product-2.png"
                                    alt="product"
                            />
                        </div>
                        <p class="product_item_title">
                            Баллон c жидким азотом
                        </p>
                        <p class="product_text">Объем/Масса: 6,4</p>
                        <p class="product_price lowprice">
                            150
                            <span>руб.</span>
                        </p>
                        <div class="product_order">
                            <div class="product_count">
                                <button class="plus">+</button>
                                <input type="text" value="1" />
                                <button class="minus">-</button>
                            </div>
                            <a href="#" class="btn blue">заказать</a>
                        </div>
                        <span class="product_info">Есть в наличии</span>
                    </div>
                </div>
                <div class="product_item">
                    <div class="product_backface">
                                <span
                                        title="more information"
                                        class="product_more"
                                >
                                    <img src="/img/info.svg" alt="info" />
                                </span>
                        <p>
                            Lorem ipsum dolor sit amet consectetur
                            adipisicing elit. Expedita iste natus
                            officiis? Dolores excepturi vel explicabo
                            debitis aliquid, molestiae, quam sunt sequi
                            expedita omnis porro ad consectetur at quos
                            dolore?
                        </p>
                    </div>
                    <div class="product_frontface">
                                <span
                                        title="more information"
                                        class="product_more"
                                >
                                    <img src="/img/info.svg" alt="info" />
                                </span>
                        <div class="product_img">
                            <img
                                    src="/img/product-1.png"
                                    alt="product"
                            />
                        </div>
                        <p class="product_item_title">
                            Баллон с аргоном
                        </p>
                        <p class="product_text">Объем/Масса: 6,4</p>
                        <p class="product_price">
                            1800
                            <span>руб.</span>
                        </p>
                        <div class="product_order">
                            <div class="product_count">
                                <button class="plus">+</button>
                                <input type="text" value="1" />
                                <button class="minus">-</button>
                            </div>
                            <a href="#" class="btn blue">заказать</a>
                        </div>
                        <span class="product_info">Есть в наличии</span>
                    </div>
                </div>
                <div class="product_item">
                    <div class="product_backface">
                                <span
                                        title="more information"
                                        class="product_more"
                                >
                                    <img src="/img/info.svg" alt="info" />
                                </span>
                        <p>
                            Lorem ipsum dolor sit amet consectetur
                            adipisicing elit. Expedita iste natus
                            officiis? Dolores excepturi vel explicabo
                            debitis aliquid, molestiae, quam sunt sequi
                            expedita omnis porro ad consectetur at quos
                            dolore?
                        </p>
                    </div>
                    <div class="product_frontface">
                                <span
                                        title="more information"
                                        class="product_more"
                                >
                                    <img src="/img/info.svg" alt="info" />
                                </span>
                        <div class="product_img">
                            <img
                                    src="/img/product-2.png"
                                    alt="product"
                            />
                        </div>
                        <p class="product_item_title">
                            Баллон с аргоном высокой чистоты
                        </p>
                        <p class="product_text">Объем/Масса: 6,4</p>
                        <p class="product_price">
                            3000
                            <span>руб.</span>
                        </p>
                        <div class="product_order">
                            <div class="product_count">
                                <button class="plus">+</button>
                                <input type="text" value="1" />
                                <button class="minus">-</button>
                            </div>
                            <a href="#" class="btn blue">заказать</a>
                        </div>
                        <span class="product_info">Есть в наличии</span>
                    </div>
                </div>
                <div class="product_item">
                    <div class="product_backface">
                                <span
                                        title="more information"
                                        class="product_more"
                                >
                                    <img src="/img/info.svg" alt="info" />
                                </span>
                        <p>
                            Lorem ipsum dolor sit amet consectetur
                            adipisicing elit. Expedita iste natus
                            officiis? Dolores excepturi vel explicabo
                            debitis aliquid, molestiae, quam sunt sequi
                            expedita omnis porro ad consectetur at quos
                            dolore?
                        </p>
                    </div>
                    <div class="product_frontface">
                                <span
                                        title="more information"
                                        class="product_more"
                                >
                                    <img src="/img/info.svg" alt="info" />
                                </span>
                        <div class="product_img">
                            <img
                                    src="/img/product-1.png"
                                    alt="product"
                            />
                        </div>
                        <p class="product_item_title">
                            Баллон c азотом газообразным
                        </p>
                        <p class="product_text">Объем/Масса: 6,4</p>
                        <p class="product_price">
                            700
                            <span>руб.</span>
                        </p>
                        <div class="product_order">
                            <div class="product_count">
                                <button class="plus">+</button>
                                <input type="text" value="1" />
                                <button class="minus">-</button>
                            </div>
                            <a href="#" class="btn blue">заказать</a>
                        </div>
                        <span class="product_info">Есть в наличии</span>
                    </div>
                </div>
                <div class="product_item">
                    <div class="product_backface">
                                <span
                                        title="more information"
                                        class="product_more"
                                >
                                    <img src="/img/info.svg" alt="info" />
                                </span>
                        <p>
                            Lorem ipsum dolor sit amet consectetur
                            adipisicing elit. Expedita iste natus
                            officiis? Dolores excepturi vel explicabo
                            debitis aliquid, molestiae, quam sunt sequi
                            expedita omnis porro ad consectetur at quos
                            dolore?
                        </p>
                    </div>
                    <div class="product_frontface">
                                <span
                                        title="more information"
                                        class="product_more"
                                >
                                    <img src="/img/info.svg" alt="info" />
                                </span>
                        <div class="product_img">
                            <img
                                    src="/img/product-2.png"
                                    alt="product"
                            />
                        </div>
                        <p class="product_item_title">
                            Баллон c жидким азотом
                        </p>
                        <p class="product_text">Объем/Масса: 6,4</p>
                        <p class="product_price lowprice">
                            150
                            <span>руб.</span>
                        </p>
                        <div class="product_order">
                            <div class="product_count">
                                <button class="plus">+</button>
                                <input type="text" value="1" />
                                <button class="minus">-</button>
                            </div>
                            <a href="#" class="btn blue">заказать</a>
                        </div>
                        <span class="product_info">Есть в наличии</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="services">
        <div class="container-fluid">
            <div class="services_inner">
                <div
                        class="services_item"
                        style="
                                background: url('/img/services-1.png') no-repeat
                                    #fff;
                            "
                >
                    <h2 class="title">
                        ДОПОЛНИТЕЛЬНЫЕ <br />
                        УСЛУГИ
                    </h2>
                </div>
                <a
                        href="#form"
                        class="services_item"
                        style="
                                background: center/cover
                                    url('/img/services-2.png');
                            "
                >
                    <h2 class="services_title">
                        ПОСТАВКА ОБОРУДОВАНИЯ И КОМПЛЕКТУЮЩИХ
                    </h2>
                </a>
                <a
                        href="#form"
                        class="services_item"
                        style="
                                background: center/cover
                                    url('/img/services-3.png');
                            "
                >
                    <h2 class="services_title">
                        ПЕРЕАТТЕСТАЦИЯ И РЕМОНТ БАЛЛОНОВ
                        <span> без остаточного давления </span>
                    </h2>
                </a>
                <a
                        href="#form"
                        class="services_item"
                        style="
                                background: center/cover
                                    url('/img/services-4.png');
                            "
                >
                    <h2 class="services_title">
                        ПЕРЕВОЗКА ОПАСНЫХ ГРУЗОВ
                    </h2>
                </a>
                <a
                        href="#form"
                        class="services_item"
                        style="
                                background: center/cover
                                    url('/img/services-5.png');
                            "
                >
                    <h2 class="services_title">
                        РАЗРАБОТКА ПРОЕКТА И МОНТАЖ ОБОРУДОВАНИЯ
                    </h2>
                </a>
            </div>
        </div>
    </section>
    <section class="buy">
        <div class="container">
            <div class="buy_inner">
                <h2 class="title white">
                    ПОКУПАЕМ Б/У <br />
                    ГАЗОВЫЕ БАЛЛОНЫ
                </h2>
                <p class="buy_text">
                    Это пример текста, создан для того, чтобы было
                    понятно, где будет текст. Это пример текста, создан
                    для того, чтобы было понятно, где будет текст. Это
                    пример текста, создан для того, чтобы было понятно,
                    где будет текст. Это пример текста, создан для того,
                    чтобы было понятно, где будет текст.
                </p>
                <div class="buy_inner_content">
                    <a href="+71234567890" class="buy_tel"
                    >+7 123 456 78 90</a
                    >
                    <a href="#" class="btn blue recall">
                        <img src="/img/phone.svg" alt="phone" />
                        ЗАКАЗАТЬ ЗВОНОК
                    </a>
                </div>
                <img src="/img/gaz.png" class="gaz" alt="gas" />
            </div>
        </div>
    </section>
    <section class="news">
        <div class="container">
            <div class="news_top">
                <h2 class="title">НОВОСТИ И ВАКАНСИИ</h2>
                <a href="#" class="more">СМОТРЕТЬ ВСЕ</a>
            </div>
            <div class="news_slider">
                <div class="news_item">
                    <img src="/img/news-1.png" alt="news" />
                    <div class="news_content">
                        <p class="news_title">
                            Заголовок новости может быть длинным, в две
                            и более строки
                        </p>
                        <span class="date"> 13.04.2022 </span>
                    </div>
                </div>
                <div class="news_item">
                    <img src="/img/news-2.png" alt="news" />
                    <div class="news_content">
                        <p class="news_title">
                            Заголовок новости может быть длинным, в две
                            и более строки
                        </p>
                        <span class="date"> 13.04.2022 </span>
                    </div>
                </div>
                <div class="news_item">
                    <img src="/img/news-3.png" alt="news" />
                    <div class="news_content">
                        <p class="news_title">
                            Заголовок новости может быть длинным, в две
                            и более строки
                        </p>
                        <span class="date"> 13.04.2022 </span>
                    </div>
                </div>
                <div class="news_item">
                    <img src="/img/news-2.png" alt="news" />
                    <div class="news_content">
                        <p class="news_title">
                            Заголовок новости может быть длинным, в две
                            и более строки
                        </p>
                        <span class="date"> 13.04.2022 </span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
