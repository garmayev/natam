window.Helper = {
    /**
     * Create bew DOMElement in Native JavaScript
     *
     * @param tagName String - is for
     * @param content Array|DOMElement|string
     * @param attributes object {attributeName: attributeValue}
     * @param events object {eventName: callback}
     * @returns DOMElement
     */
    createElement(tagName, content = undefined, attributes = {}, events = {}) {
        let el = document.createElement(tagName);
        if (content !== undefined) {
            if (Array.isArray(content)) {
                for (const key in content) {
                    el.append(content[key]);
                }
            } else if (typeof content === "object") {
                el.append(content);
            } else {
                el.innerText = content;
            }
        }
        for (const key in attributes) {
            if (Array.isArray(attributes[key])) {
                el.setAttribute(key, attributes[key].join(" "));
            } else {
                el.setAttribute(key, attributes[key]);
            }
        }
        for (const key in events) {
            if (Array.isArray(events[key])) {
                let listeners = events[key];
                for (const index in listeners) {
                    el.addEventListener(key, listeners[index]);
                }
            } else {
                el.addEventListener(key, events[key]);
            }
        }
        return el;
    },

    /**
     * Mapping array
     *
     * @param array
     * @param property1
     * @param property2
     * @returns {*[]}
     */
    map(array, property1, property2) {
        let result = [];
        for (let i = 0; i < array.length; i++) {
            let item = array[i];
            if (item.hasOwnProperty(property1)) {
                result[item[property1]] = item[property2];
            }
        }
        return result;
    },

    /**
     * Create AJAX Request
     *
     * @param url
     * @param data
     * @param options
     * @returns {any}
     */
    ajax(url, data = {}, options = {method: "GET", async: false}) {
        let xhr = new XMLHttpRequest();
        switch (options.method.toUpperCase()) {
            case "GET":
                url += '?' + (new URLSearchParams(data)).toString();
                data = null;
                break;
            case "POST":
                // data = JSON.stringify(data);
                break;
        }
        xhr.open(options.method, url, options.async);
        for (const key in options.headers) {
            xhr.setRequestHeader(key, options.headers[key]);
        }
        try {
            xhr.send(data);
            if (xhr.status !== 200) {
                alert(`Ошибка ${xhr.status}: ${xhr.statusText}`);
            } else {
                return JSON.parse(xhr.response);
            }
        } catch (err) {
            alert("Запрос не удался");
        }
    },

    get(object, property) {
        let keys = property.split(".");
        if (keys.length > 1) {
            let prop = keys.shift();
            return Helper.get(object[prop], keys.join("."));
        } else {
            if (object) {
                if (property === "delivery_at") {
                    return new Date(parseInt(object[property]) * 1000).toLocaleString("ru", {
                        year: "numeric",
                        month: "numeric",
                        day: "numeric",
                        hour: 'numeric',
                        minute: 'numeric'
                    });
                } else {
                    return object[property];
                }
            } else {
                return "";
            }
        }
    },

    generateLink(object, config) {
        if (config.href) {
            let a = [...config.href.matchAll(/\{\{(\w+)\}\}/g)], href;
            for (let i = 0; i < a.length; i++) {
                config.href = config.href.replace(a[i][0], Helper.get(object, a[i][1]));
            }
        }
    }
}

class DispatcherEvent {
    constructor(eventName) {
        this.eventName = eventName;
        this.callbacks = [];
    }

    registerCallback(callback) {
        this.callbacks.push(callback);
    }

    unregisterCallback(callback) {
        const index = this.callbacks.indexOf(callback);
        if (index > -1) {
            this.callbacks.splice(index, 1);
        }
    }

    fire(data) {
        const callbacks = this.callbacks.slice(0);
        callbacks.forEach((callback) => {
            callback(data);
        });
    }
}

export class Dispatcher {
    constructor() {
        this.events = {};
    }

    dispatch(eventName, data) {
        const event = this.events[eventName];
        if (event) {
            event.fire(data);
        }
    }

    on(eventName, callback) {
        let event = this.events[eventName];
        if (!event) {
            event = new DispatcherEvent(eventName);
            this.events[eventName] = event;
        }
        event.registerCallback(callback);
    }

    off(eventName, callback) {
        const event = this.events[eventName];
        if (event && event.callbacks.indexOf(callback) > -1) {
            event.unregisterCallback(callback);
            if (event.callbacks.length === 0) {
                delete this.events[eventName];
            }
        }
    }
}

class User extends Dispatcher {
    static EVENT_LOGGED = 'logged';
    static EVENT_ERROR = 'error';
    static EVENT_FAILED = 'failed';

    isLogged = false;
    _chat_id = null;
    _token;
    _csrf_param;
    _csrf_token;
    _container;

    constructor(container, chat_id) {
        super();
        this._chat_id = chat_id;
        this._container = container;
    }

    init() {
        let preflight = Helper.ajax(
            "/api/default/options", {
                chat_id: this._chat_id
            }, {
                method: "GET",
                async: false,
            });
        this._csrf_param = preflight.param;
        this._csrf_token = preflight.token;
        if (preflight.ok) {
            this._token = preflight.access_token;
            this.dispatch(User.EVENT_LOGGED, {detail: preflight});
        } else {
            this._container.append(this.buildForm());
        }
    }

    buildForm() {
        let container = Helper.createElement("div", undefined, {class: ["row"]}),
            form = Helper.createElement("form", undefined, {
                class: ["container-fluid", "login-form"]
            }, {
                submit: this.submit.bind(this)
            }),
            csrf = Helper.createElement("input", undefined, {
                type: "hidden",
                name: this._csrf_param,
                value: this._csrf_token
            }),
            chat_id = Helper.createElement("input", undefined, {
                type: "hidden",
                name: "chat_id",
                value: this._chat_id
            }),
            login_container = Helper.createElement("div", Helper.createElement("input", undefined, {
                class: ["form-control", "col-12"],
                type: "text",
                name: "login-form[login]",
                placeholder: "Логин"
            }), {
                class: ["form-group", "col-12"]
            }),
            password_container = Helper.createElement("div", Helper.createElement("input", undefined, {
                class: ["form-control", "col-12"],
                type: "password",
                name: "login-form[password]",
                placeholder: "Пароль"
            }), {
                class: ["form-group", "col-12"]
            }),
            submit_container = Helper.createElement("div", Helper.createElement("button", "Авторизоваться", {
                class: ["btn", "btn-success"],
            }), {
                class: ["form-group", "text-center", "col-12"]
            })
        form.append(csrf, chat_id, login_container, password_container, submit_container);
        container.append(form);
        return container;
    }

    destroyForm() {
        this._container.innerHTML = '';
    }

    submit(e) {
        const formSerialize = formElement => {
            const values = {};
            const inputs = formElement.elements;

            for (let i = 0; i < inputs.length; i++) {
                if (inputs[i].name.length) {
                    values[inputs[i].name] = inputs[i].value;
                }
            }
            return values;
        }

        const dumpValues = form => () => {
            return formSerialize(form);
        }

        e.preventDefault();
        let data = new FormData(e.currentTarget),
            response = Helper.ajax("/api/default/login", data, {method: "POST", async: false});
        if (response.ok) {
            this._token = response.access_token;
            this.destroyForm();
            this.dispatch(User.EVENT_LOGGED, {detail: response});
        }
    }
}

class Cart {
    selected = [];
    totalPrice = 0;
    #table = undefined;

    constructor(container) {
        this.container = container;
        this.rebuild();
    }

    rebuild() {
        if (this.#table === undefined) {
            this.#table = Helper.createElement("table", undefined, {class: ["table", "table-striped"]});
            this.container.append(this.#table);
        } else {
            this.#table.innerHTML = "";
        }
        let thead = Helper.createElement("thead"),
            thRow = Helper.createElement("tr"),
            tbody = Helper.createElement("tbody");
        thRow.append(Helper.createElement("th", "Продукт"), Helper.createElement("th", "Количество"), Helper.createElement("Действия"));
	this.totalPrice = 0;
        for (const element of this.selected) {
            let tr = Helper.createElement("tr"),
                product = Helper.ajax("/api/product/view?id=" + element.product_id),
		// category = Helper.ajax("/api/category/search?id=" + product.category_id),
                td_product = Helper.createElement("td", [Helper.createElement("div", product.category.title), Helper.createElement("div", `${product.title}`)], {style: "vertical-align: middle"}),
                td_action = Helper.createElement("td", Helper.createElement("span", undefined, {class: ["fas", "fa-trash"]}, {click: this.remove.bind(this, element.product_id)}), {class: "text-right", style: "vertical-align: middle"}),
                td_count = Helper.createElement("td", `${element.product_count} шт`, {style: "vertical-align: middle"});
            tr.append(td_product, td_count, td_action);
	    this.totalPrice += (Number.parseInt(product.price) * Number.parseInt(element.product_count));
	    // console.log(product.price);
            tbody.append(tr);
        }
        this.#table.append(thead, tbody);
	if (this.totalPrice > 0) {
	    this.#table.append(Helper.createElement('p', ['Общая стоимость: ', Helper.createElement('b', `${this.totalPrice} руб`), ' (Без учета доставки)'], {class: 'mt-3'}))
	}
    }

    append(object) {
        for (const element of this.selected) {
            if (element.product_id === object.product_id && element.category_id === object.category_id) {
                element.product_count = object.product_count;
                return true;
            }
        }
        this.selected.push(object);
        this.rebuild();
        return true;
    }

    remove(product_id) {
        this.selected = this.selected.filter(element => element.product_id !== product_id);
        this.rebuild();
        return this.selected;
    }
}

class Order extends Dispatcher {
    id;
    client;
    location;
    statusName;
    class;

    constructor(data) {
        super();
        this.class = "order";
        this.id = data.id;
        this.client = data.client;
        this.location = data.location;
        this.statusName = data.statusName;
        this.delivery_at = data.delivery_at;
        this.delivery_type = data.delivery_type;
        this.comment = data.comment;
        this.store = data.store;
        this.distance = data.distance;
        this.products = data.products;
    }

    static view(container, element) {
        container.innerHTML = '';
        let clientCardContainer = Helper.createElement('div', undefined, {class: 'card', style: 'margin-bottom: 10px'}),
            clientCardHeader = Helper.createElement('div', 'Информация о клиенте', {class: 'card-header'}),
            clientCardBody = Helper.createElement('div', undefined, {class: 'card-body'}),
            deliveryCardContainer = Helper.createElement('div', undefined, {
                class: 'card',
                style: 'margin-bottom: 10px'
            }),
            deliveryCardHeader = Helper.createElement('div', 'Информация о доставке', {class: 'card-header'}),
            deliveryCardBody = Helper.createElement('div', undefined, {class: 'card-body'}),
            productsCardContainer = Helper.createElement('div', undefined, {
                class: 'card',
                style: 'margin-bottom: 10px'
            }),
            productsCardHeader = Helper.createElement('div', 'Содержимое заказа', {class: 'card-header'}),
            productsCardBody = Helper.createElement('div', undefined, {class: 'card-body'}),
            productsCardFooter = Helper.createElement('div', undefined, {class: 'card-footer'}),
            backBtn = Helper.createElement('span', 'Назад', {class: ['btn', 'btn-success', 'm-2']}, {
                click: () => {
                    let orders = Order.get(user);
                    Order.buildTable(document.querySelector("body > .main"), orders);
                }
            }),
            totalPrice = 0, date = new Date(element.delivery_at * 1000);
        container.append(
            backBtn,
            Helper.createElement("span", `Заказ #${element.id}`, {style: 'font-weight: bolder'}),
            Helper.createElement(
                "span",
                `Повторить заказ`,
                {
                    class: ['btn', 'btn-primary', 'ml-3'],
                    "data-toggle": "modal",
                    "data-target": "#clone-order"
                }, {
                    "click": (e) => {
                        document.getElementById("clone-delivery_at").setAttribute("data-key", element.id);
                    }
                })
        );
        clientCardBody.append(
            Helper.createElement('p', [
                Helper.createElement('b', 'Имя клиента: '),
                Helper.createElement('span', element.client.name)
            ]),
            Helper.createElement('p', [
                Helper.createElement('b', 'Номер телефона: '),
                Helper.createElement('span', element.client.phone)
            ]),
            Helper.createElement('p', [
                Helper.createElement('b', 'E-mail: '),
                Helper.createElement('span', element.client.email ?? '')
            ]),
            Helper.createElement('p', [
                Helper.createElement('b', 'Компания: '),
                Helper.createElement('span', element.client.company.title ?? '')
            ])
        );
        clientCardContainer.append(clientCardHeader, clientCardBody);
        deliveryCardBody.append(
            Helper.createElement('p', [
                Helper.createElement('b', 'Адрес доставки: '),
                Helper.createElement('span', element.location.title)
            ]),
            Helper.createElement('p', [
                Helper.createElement('b', 'Дата доставки: '),
                Helper.createElement('span', date.toLocaleDateString('ru-RU', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                }))
            ]),
            Helper.createElement('p', [
                Helper.createElement('b', 'Расстояние: '),
                Helper.createElement('span', new Intl.NumberFormat('ru-RU').format(element.distance / 1000) + " километров")
            ]),
	    Helper.createElement('input', undefined, {type: 'hidden', id: 'delivery_city'})
        );
        deliveryCardContainer.append(deliveryCardHeader, deliveryCardBody);
        let table = Helper.createElement('table', undefined, {class: ['table', 'table-stripped']}),
            thead = Helper.createElement('thead'),
            tbody = Helper.createElement('tbody');
        thead.append(
            Helper.createElement('th', 'Категория'),
            Helper.createElement('th', 'Продукт'),
            Helper.createElement('th', 'Количество'),
        );
        for (const item of element.products) {
            totalPrice += item.product.price * item.count;
            tbody.append(
                Helper.createElement("tr", [
                    Helper.createElement("td", item.product.category_title),
                    Helper.createElement("td", item.product.title),
                    Helper.createElement("td", item.count),
                ])
            );
        }
        table.append(thead, tbody);
        productsCardBody.append(table);
        productsCardFooter.append(`Общая стоимость заказа: ${totalPrice}`);
        productsCardContainer.append(productsCardHeader, productsCardBody, productsCardFooter);
        container.append(clientCardContainer, deliveryCardContainer, productsCardContainer);
    }

    static buildTable(container, array, columns = []) {
        container.innerHTML = '';
        if (columns.length === 0) {
            columns = [
                {"key": "id", "title": "Номер заказа", "class": "Order", "href": "/api/{{class}}/view?id={{id}}"},
                {
                    "key": "location.title",
                    "title": "Адрес доставки",
                    "class": "Location",
                    "href": "/api/{{class}}/view?id={{id}}"
                },
                {
                    "key": "delivery_at",
                    "title": "Дата доставки"
                    // },
                    // {
                    //     "key": "statusName",
                    //     "title": "Статус",
                    // },
                    // {
                    //     "key": "action",
                    //     "title": "",
                }
            ]
            // columns = {"id": "Номер заказа", "location.title": "Адрес доставки", "statusName": "Статус"};
        }
        let table = Helper.createElement("table", undefined, {class: ['table', 'table-striped']}),
            thead = Helper.createElement("thead"),
            tbody = Helper.createElement("tbody"),
            trow = Helper.createElement("tr"),
            createBtn = Helper.createElement("span", "Создать заказ", {
                class: ["btn", "btn-success", "m-2"],
                "data-toggle": "modal",
                "data-target": "#exampleModal"
            }, {
                click: Order.create
            });
        for (const column of columns) {
            trow.append(Helper.createElement("th", column.title));
        }
        thead.append(trow);
        table.append(
            thead,
            tbody);
        if (array.length) {
            for (const element of array) {
                let row = Helper.createElement("tr", undefined, {"data-key": element.id});
                for (const column of columns) {
                    if (column.key !== 'id') {
                        row.append(Helper.createElement("td", Helper.createElement("p", Helper.get(element, column.key)), {'data-key': element.id}, {'click': Order.view.bind(this, container, element)}));
                    } else {
                        row.append(Helper.createElement("td", Helper.createElement("p", Helper.get(element, column.key))));
                    }
                }
                let btn = Helper.createElement("button", undefined, {
                        class: ["btn", "btn-success", "fas", "fa-plus"],
                        type: "button",
                        id: `dropdown_${element.id}`,
                        "data-toggle": "dropdown",
                        "aria-haspopup": "true",
                        "aria-expanded": "Dropdown",
                    }),
                    itemClone = Helper.createElement("a", "Повторить заказ", {
                        class: "dropdown-item",
                        href: "#",
                        "data-toggle": "modal",
                        "data-target": "#clone-order",
                        "data-key": element.id,
                    }, {click: Order.clone}),
                    btnMenu = Helper.createElement("div", [itemClone], {
                        class: ["dropdown-menu", "dropdown-menu-right"],
                        "aria-labelledby": `dropdown_${element.id}`
                    }),
                    btnGroup = Helper.createElement("p", [btn, btnMenu], {
                        class: "btn-group",
                        role: "group",
                    }),
                    tdAction = Helper.createElement("td", btnGroup);
                // row.append(tdAction);
                row.querySelector("td:first-child").append(btnGroup);
                tbody.append(row);
            }
        } else {
            let row = Helper.createElement('tr'),
                column = Helper.createElement('td', "Ничего не найдено", {colspan: columns.length});
            row.append(column);
            tbody.append(row);
        }
        container.append(createBtn, table);
        this.table = table;
    }

    static get(user) {
        let orders = Helper.ajax("/api/order/index", undefined, {
                method: "GET",
                async: false,
                headers: {Authorization: `Bearer ${user._token}`}
            }),
            result = [];

        for (const key of orders) {
            result.push(new Order(key));
        }
        return result;
    }

    static create(e) {
        console.log(this);
    }

    static clone(e) {
        document.querySelector("#clone-delivery_at").setAttribute("data-key", e.target.getAttribute("data-key"))
    }
}

export {User, Order, Cart};
