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

    init() {
        function getOrders(order_id)
        {
            ymaps.vow.resolve($.ajax({
                url: `/admin/location/orders?id=${order_id}`,
                dataType: 'json',
                data: {id: order_id},
                processData: false
            })).then((data) => {
                if ( data.length > 1 ) {
                    self._objectManager.clusters.balloon.setData(`
                    <h3>Заказ #${data[0].id}</h3><p><strong>Адрес</strong>: ${data[0].location.title}</p>
                    `);
                }
                if (isCluster && self._objectManager.clusters.balloon.isOpen(id)) {
                } else if (self._objectManager.objects.balloon.isOpen(id)) {
                    self._objectManager.objects.balloon.setData(self._objectManager.objects.balloon.getData());
                }
            })
        }
        this.settings.map = new ymaps.Map(this.container, this.settings.map, {
            searchControlProvider: "yandex#search",
        });
        self._objectManager = new ymaps.ObjectManager({
            clusterize: true,
            gridSize: 64,
            clusterIconLayout: "default#pieChart"
        });
        self._objectManager.objects.events.add('click', function (e) {
            let objectId = e.get('objectId');
            self._objectManager.objects.balloon.open(objectId);
        }).add('balloonopen', (e) => {
            console.log(e.get('objectId'));
            getOrders(e.get('objectId'));
        });

        this.settings.map.geoObjects.add(self._objectManager);

        $.ajax({
            url: "/admin/location/features",
        }).done(function(data) {
            console.log(self._objectManager);
            self._objectManager.add(data);
        });
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