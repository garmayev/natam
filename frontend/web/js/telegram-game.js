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
                return object[property];
            } else {
                return "";
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
        let container = Helper.createElement("div", undefined, {class: ["row", "p-3"]}),
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

class Order extends Dispatcher {
    id;
    client;
    location;

    constructor(data) {
        super();
        this.id = data.id;
        this.client = data.client;
        this.location = data.location;
    }

    static buildTable(container, array, columns = []) {
        if (columns.length === 0) {
            columns = ["id", "client.name", "location.title"];
        }
        if (array.length) {
            let table = Helper.createElement("table", undefined, {class: ['table', 'table-striped']}),
                // thead = Helper.createElement("thead"),
                tbody = Helper.createElement("tbody");
            // let trow = Helper.createElement("tr");
            // for (const key of columns) {
            //     trow.append(Helper.createElement("td", key));
            // }
            // thead.append(trow);
            table.append(
                // thead,
                tbody);
            for (const element of array) {
                let row = Helper.createElement("tr", undefined, {"data-key": element.id});
                for (const key in columns) {
                    row.append(Helper.createElement("td", Helper.get(element, columns[key])));
                }
                tbody.append(row);
            }
            container.append(table);
        } else {

        }
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

}

export {User, Order};