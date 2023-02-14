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
<script>
    class Slide extends HTMLElement {

    }

    class Popup extends HTMLElement {
        _shadow;

        constructor() {
            super();
        }

        connectedCallback() {
            this._shadow = this.attachShadow({mode: "open"});
            this._shadow.innerHTML = '<template id="item"><div class="item"><slot name="thumbs"></slot><slot name="title"></slot></div></template>';
            let header = Helper.createElement('div', Helper.createElement('span', this.dataset.title, {class: 'title'}), {class: 'header'});
            if (this.dataset.iscloseable) {
                header.append(Helper.createElement('span', 'X', {class: 'close'}, {'click': this.hide.bind(this)}));
            }
            this._shadow.append(header, Helper.createElement('div', undefined, {class: 'content'}));
        }

        update(callback) {
            let regex = new RegExp("{{index}}", 'g');
            fetch(this.dataset.url.replaceAll(regex, this.dataset.index))
                .then(response => response.json())
                .then((response) => {
                    this.shadowRoot.querySelector('.content').innerHTML = '';
                    response.forEach(element => {
                        let clone = this.shadowRoot.getElementById("item").content.cloneNode(true),
                            item = clone.querySelector('.item');
                        item.dataset.key = element.id;
                        item.addEventListener('click', callback.bind(item))
                        item.append(Helper.createElement('img', undefined, {
                            name: 'thumbs',
                            src: element.thumbs
                        }));
                        item.append(Helper.createElement('p', element.title, {name: 'title'}));
                        this.shadowRoot.querySelector('.content').append(clone);
                    })
                })
        }

        show(e) {
            this.update(e);
            this.classList.add("active");
        }

        hide(e) {
            this.classList.remove("active");
        }
    }

    let Helper = {
        createElement: (tagName, content = undefined, attributes = {}, events = {}) => {
            let el = document.createElement(tagName);
            if (content !== undefined) {
                if (typeof content === "object") {
                    el.append(content);
                } else {
                    el.innerText = content;
                }
            }
            for (const property in attributes) {
                if (Array.isArray(attributes[property])) {
                    el.setAttribute(property, attributes[property].join(' '));
                } else {
                    el.setAttribute(property, attributes[property]);
                }
            }
            for (const eventName in events) {
                el.addEventListener(eventName, events[eventName]);
            }
            return el;
        }
    };

    document.addEventListener("DOMContentLoaded", () => {
        customElements.define('custom-slide', Slide);
        customElements.define('custom-popup', Popup);
    })

    function openProductPopup() {
        let target = document.querySelector('#categories');
        target.show.call(target, function () {
            let productPopup = document.querySelector('#products');
            console.log(this);
            productPopup.dataset.index = this.dataset.key;
            productPopup.show.call(productPopup, function (e) {
                console.log(e)
            })
        })
    }
</script>
<link rel="stylesheet" href="/css/webapp.css">
<custom-slide class="cart">
    <div class="inner">
        <span onclick="openProductPopup()">Add Product</span>
    </div>
    <custom-popup id="categories" class="category popup" data-url="/api/category/index" data-title="Categories"
                  data-iscloseable="true">
    </custom-popup>
    <custom-popup id="products" class="products popup" data-url="/api/product/by-category?category_id={{index}}"
                  data-index="" data-title="Products" data-iscloseable="true"></custom-popup>
    <custom-popup id="product-view" class="info popup" data-url="/api/product/view?id={{index}}" data-index=""
                  data-title="Product" data-iscloseable="true"></custom-popup>
</custom-slide>
