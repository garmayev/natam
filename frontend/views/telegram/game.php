<?php

use yii\data\ActiveDataProvider;
use yii\web\View;


/**
 * @var $this View
 * @var $models ActiveDataProvider
 */

?>
<script src="//telegram.org/js/telegram-web-app.js"></script>
<script src="//kit.fontawesome.com/aa23fe1476.js"></script>
<script src="https://api-maps.yandex.ru/2.1/?apikey=0bb42c7c-0a9c-4df9-956a-20d4e56e2b6b&lang=ru_RU" type="text/javascript">
<script type="module">
    class Order {
        _client;
        _location;
        _delivery_date;
        _products;

        get client() {
            return this._client;
        }

        set client(value) {
            this._client = value;
        }

        get location() {
            return this._location;
        }

        set location(value) {
            this._location = value;
        }

        get products() {
            return this._products;
        }

        set products(value) {
            this._products = value;
        }
    }

    class Category {
        _id;
        _title;
        _thumbs;
        _html;

        get id() {
            return this._id;
        }

        set id(value) {
            this._id = value;
        }

        get title() {
            return this._title;
        }

        set title(value) {
            this._title = value;
        }

        get thumbs() {
            return this._thumbs;
        }

        set thumbs(value) {
            this._thumbs = value;
        }

        get link() {
            return `/api/product/by-category?category_id=${this.id}`;
        }

        constructor(options) {
            this.id = options.id;
            this.title = options.title;
            this.thumbs = options.thumbs;
            this._html = Helper.createElement('div', undefined, {
                class: 'item',
                'data-key': this.id,
                style: 'background: --tg-theme-bg-color'
            });
            let thumbs = Helper.createElement('img', undefined, {src: this.thumbs}),
                title = Helper.createElement('p', this.title),
                link = Helper.createElement('a', 'Подробнее', {href: this.link}, {click: this.select.bind(this)});
            this._html.append(thumbs, title, link);
        }

        select(e) {
            e.preventDefault();
            fetch(e.currentTarget.getAttribute('href'))
                .then(response => response.json())
                .then((response) => {
                    let grid = document.querySelector('.grid');
                    grid.innerHTML = '';
                    let container = Helper.createElement('div', undefined, {class: 'item'}),
                        thumbs = Helper.createElement('img', undefined, {src: 'data:image/png;base64,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'}),
                        title = Helper.createElement('a', 'Назад', {href: '#'}, {click: init});
                    container.append(thumbs, title);
                    grid.append(container);

                    response.forEach((element) => {
                        let product = new Product(element);
                        products.push(product);
                        grid.append(product._html);
                    })
                })
        }
    }

    class Product {
        _id;
        _title;
        _value;
        _price;
        _thumbs;
        _html;
        _state = 0;

        get id() {
            return this._id;
        }

        set id(value) {
            this._id = value;
        }

        get title() {
            return this._title;
        }

        set title(value) {
            this._title = value;
        }

        get value() {
            return this._value;
        }

        set value(value) {
            this._value = value;
        }

        get price() {
            return this._price;
        }

        set price(value) {
            this._price = value;
        }

        get thumbs() {
            return this._thumbs;
        }

        set thumbs(value) {
            this._thumbs = value;
        }

        get link() {
            return `/api/product/view?id=${this.id}`;
        }

        constructor(options) {
            this.id = options.id;
            this.title = options.title;
            this.value = options.value;
            this.price = options.price;
            this.thumbs = options.thumbs;
            this._html = Helper.createElement('div', undefined, {class: 'item', 'data-key': this.id});
            let thumbs,
                title = Helper.createElement('p', `${this.title} (${this.value})`),
                price = Helper.createElement('p', this.price),
                link = Helper.createElement('a', 'Заказать', {href: this.link}, {click: this.select.bind(this)});
            if (this.thumbs) {
                thumbs = Helper.createElement('img', undefined, {src: this.thumbs});
            } else {
                thumbs = '';
            }
            this._html.append(thumbs, title, price, link);
        }

        select(e) {
            e.preventDefault();
            if (this._state) {

            } else {
                let thumbs,
                    title = Helper.createElement('p', `${this.title} (${this.value})`),
                    price = Helper.createElement('p', this.price),
                    counter = new Counter({id: this.id}),
                    link = counter._html;
                if (this.thumbs) {
                    thumbs = Helper.createElement('img', undefined, {src: this.thumbs});
                } else {
                    thumbs = '';
                }
                this._html.innerHTML = '';
                this._html.append(thumbs, title, price, link);
            }
        }
    }

    class Counter {
        _id;
        _html;
        _value = 1;

        constructor(data) {
            this._html = Helper.createElement('div', undefined, {class: 'counter'});
            let minus = Helper.createElement('span', '-', {class: 'btn dec'}, {click: this.count.bind(this)}),
                value = Helper.createElement('span', this._value, {class: 'value'}),
                plus = Helper.createElement('span', '+', {class: 'btn inc'}, {click: this.count.bind(this)});
            this._html.append(minus, value, plus);
        }

        count(e) {
            if (e.currentTarget.classList.contains('inc')) {
                this._value += 1;
                this._html.querySelector('.value').innerHTML = this._value;
            } else {
                if (this._value > 1) {
                    this._value -= 1;
                    this._html.querySelector('.value').innerHTML = this._value;
                }
            }
        }
    }

    function init() {
        fetch(`/api/category/index`)
            .then((response) => {
                return response.json()
            })
            .then((response) => {
                let grid = document.querySelector(".grid");
                grid.innerHTML = '';
                response.forEach((element) => {
                    let category = new Category(element);
                    categories.push(category);
                    grid.append(category._html);
                })
            })
    }

    let categories = [],
        products = [],
        History = [],
        Helper = {
            createElement: function (tagName, content = undefined, attributes = {}, events = {}) {
                let el = document.createElement(tagName);
                if (content) {
                    el.innerHTML = content;
                }
                for (let property in attributes) {
                    el.setAttribute(property, attributes[property]);
                }
                for (let property in events) {
                    el.addEventListener(property, events[property]);
                }
                return el;
            }
        };

    let tg = window.Telegram.WebApp;
    // tg.showPopup({title: 'Check', message: 'Some text'});

    document.addEventListener("DOMContentLoaded", () => {
        // customElements.define('custom-product', Product);
        // customElements.define('custom-category', Category);
        // init();
    })

    document.querySelector('.username').innerText = `${tg.initDataUnsafe.user.first_name} ${tg.initDataUnsafe.user.last_name} (${tg.initDataUnsafe.user.username})`;
</script>
<script>
    ymaps.ready(init);
    function init(){
        // Создание карты.
        var myMap = new ymaps.Map("map", {
            // Координаты центра карты.
            // Порядок по умолчанию: «широта, долгота».
            // Чтобы не определять координаты центра карты вручную,
            // воспользуйтесь инструментом Определение координат.
            center: [55.76, 37.64],
            // Уровень масштабирования. Допустимые значения:
            // от 0 (весь мир) до 19.
            zoom: 7
        });
    }
</script>
<link rel="stylesheet" href="/css/webapp.css">
<div class="cart">
    <h1>Приложение находится на стадии разработки. Приносим свои извинения!</h1>
    <div id="map" style="width: 100vw; height: 300px;"></div>
</div>