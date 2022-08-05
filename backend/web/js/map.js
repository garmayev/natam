let Helper = {
    placemark: null,
}

class Map {
    _container;
    get container() {
        this._container = document.querySelector(this.settings.container);
        return this._container;
    };

    static _objectManager;

    settings = {};
    source = undefined;

    static downloadContent(geoObjects, id, isCluster) {
        let array = geoObjects.filter(function (geoObject) {
                return geoObject.properties.balloonContent === 'Идет загрузка...' ||
                    geoObject.properties.balloonContent === 'Not found';
            }),
            ids = array.map(function (geoObject) {
                return geoObject.options.id;
            });
        if (ids.length) {
            console.log(ids);
            $.ajax({
                url: '/admin/location/orders',
                type: 'POST',
                data: {id: ids},
            }).done((response) => {
                console.log(response);
                for (let i = 0; i < response.length; i++) {
                    let item = response[i];
                    geoObjects.forEach(function (geoObject) {
                        // Содержимое балуна берем из данных, полученных от сервера.
                        // Сервер возвращает массив объектов вида:
                        // [ {"balloonContent": "Содержимое балуна"}, ...]
                        if ( item.id === geoObject.options.id ) {
                            let products = "", price = 0;
                            for (let j = 0; j < item.products.length; j++) {
                                let product = item.products[j];
                                products += `<p>&nbsp;&nbsp;&nbsp;<a href="/admin/product/view?id=${product.product.id}">${product.product.title}</a> ${product.count} * ${product.product.price}</p>`;
                                price += product.count*product.product.price;
                            }
                            price += item.deliveryPrice;
                            geoObject.properties.balloonContent = `<p><strong>ФИО Клиента</strong>: <a href="/admin/client/view?id=${item.client.id}">${item.client.name}</a></p>
<p><strong>Номер телефона</strong>: <a href="phone:+${item.client.phone}">${item.client.phone}</a></p>
<p><strong>Адрес доставки</strong>: <a href="/admin/location/view?id=${item.location.id}">${item.location.title}</a></p>
<p><strong>Содержимое заказа</strong>:</p>${products}
<p><strong>Стоимость доставки</strong>: ${item.deliveryPrice}</p>
<p><strong>Стоимость заказа</strong>: ${item.price}</p>`;
                        }
                    });
                }
                setNewData(response);
            })
        }

        function setNewData(){
            if (isCluster && Map._objectManager.clusters.balloon.isOpen(id)) {
                Map._objectManager.clusters.balloon.setData(Map._objectManager.clusters.balloon.getData());
            } else if (Map._objectManager.objects.balloon.isOpen(id)) {
                Map._objectManager.objects.balloon.setData(Map._objectManager.objects.balloon.getData());
            }
        }
    }

    init() {

        function createObjectManager(points)
        {
            let objectManager = new ymaps.ObjectManager({
                clusterize: true,
                gridSize: 64,
                clusterIconLayout: "default#pieChart"
            });
            objectManager.add(points);
            objectManager.objects.events.add('balloonopen', function (e) {
                let objectId = e.get('objectId'),
                    geoObject = Map._objectManager.objects.getById(objectId);
                Map.downloadContent([geoObject], objectId);
            })
            objectManager.clusters.events.add('balloonopen', (e) => {
                let objectId = e.get("objectId"),
                    cluster = Map._objectManager.clusters.getById(objectId),
                    geoObjects = cluster.properties.geoObjects;
                Map.downloadContent(geoObjects, objectId, true)
            });
            return objectManager;
        }

        this.settings.map = new ymaps.Map(this.container, this.settings.map, {
            searchControlProvider: "yandex#search",
        });
        Map._objectManager = createObjectManager(this.settings.points);
        this.settings.map.geoObjects.add(Map._objectManager);

        this._listBox = this.settings.filter.items
            .map((item) => {
                return new ymaps.control.ListBoxItem({
                    data: {
                        content: item.title,
                    },
                    state: {
                        selected: item.selected
                    },
                    options: {
                        index: item.index
                    }
                })
            });
        let reducer = function (filters, filter) {
            filters[filter.data.get('content')] = filter.isSelected();
            return filters;
        };
        let listBoxControl = new ymaps.control.ListBox({
            data: {
                content: this.settings.filter.label,
                title: this.settings.filter.label
            },
            items: this._listBox,
            state: {
                expanded: true,
                filters: this._listBox.reduce(reducer, {})
            }
        });

        this.settings.map.controls.add(listBoxControl);

        listBoxControl.events.add(['select', 'deselect'], function (e) {
            let listBoxItem = e.get('target');
            let filters = ymaps.util.extend({}, listBoxControl.state.get('filters'));
            filters[listBoxItem.data.get('content')] = listBoxItem.isSelected();
            listBoxControl.state.set('filters', filters);
        });

        let filterMonitor = new ymaps.Monitor(listBoxControl.state);
        filterMonitor.add('filters', function (filters) {
            Map._objectManager.setFilter(getFilterFunction(filters));
        });
        function getFilterFunction(categories) {
            return function (obj) {
                console.log(obj.options.status);
                return categories[statusList[obj.options.status]];
            };
        }
    };

    constructor(options) {
        this.settings = Object.assign(this.settings, options);
    }

    geocode(search) {

    }
}

let script = document.createElement('script');
script.src = '//api-maps.yandex.ru/2.1/?apikey=0bb42c7c-0a9c-4df9-956a-20d4e56e2b6b&lang=ru_RU';
document.head.appendChild(script);