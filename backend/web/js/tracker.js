let map, cars, orders, carCluster, orderCluster, home, objects,
    carsCollection = [], orderCollection = [],
    carPoints = [],
    orderPoints = [],
    interval = 1000,
    initialMapPosition = [51.819879855767255, 107.60937851186925],
    initialMapZoom = 12,
    iteration = 0,
    count = 0, token = '', subscribe = '';
let index = 0, app = {};

let spik_cars = carUnits;

$(() => {
    function ajax(url, data = null) {
        return new Promise(function (resolve, reject) {
            let xhr = new XMLHttpRequest();
            xhr.open('GET', url, true);
            // xhr.timeout = 1500;
            xhr.onload = function () {
                if (this.status === 200) {
                    resolve(this.response);
                } else {
                    let error = new Error(this.statusText);
                    error.code = this.status;
                    reject(error);
                }
            };

            xhr.onerror = function () {
                reject(new Error("Network Error"));
            };

            xhr.ontimeout = function () {
                reject(new Error("Timeout Error"));
            }

            xhr.send(data);
        });
    }

    function init() {
        var customItemContentLayout = ymaps.templateLayoutFactory.createClass(
            // Флаг "raw" означает, что данные вставляют "как есть" без экранирования html.
            '<div class=ballon_body>{{ properties.balloonContentBody|raw }}</div>' +
            '<div class=ballon_footer><i style="color: #7B889A">{{ properties.balloonContentFooter|raw }}</i></div>'
        );

        map = new ymaps.Map('map', {
            center: initialMapPosition,
            zoom: initialMapZoom,
        });
        carCluster = new ymaps.Clusterer({
            preset: "islands#invertedRedClusterIcons",
            groupByCoordinates: false,
            clusterHideIconOnBalloonOpen: true,
            geoObjectHideIconOnBalloonOpen: true,
        })
        map.geoObjects.add(carCluster)
        orderCluster = new ymaps.Clusterer({
            preset: "islands#invertedBlueClusterIcons",
            // Макет метки кластера pieChart.
            clusterIconLayout: 'default#pieChart',
            // Радиус диаграммы в пикселях.
            clusterIconPieChartRadius: 25,
            // Радиус центральной части макета.
            clusterIconPieChartCoreRadius: 10,
            // Ширина линий-разделителей секторов и внешней обводки диаграммы.
            clusterIconPieChartStrokeWidth: 3,

            clusterDisableClickZoom: true,
            clusterOpenBalloonOnClick: true,
            // Устанавливаем стандартный макет балуна кластера "Аккордеон".
            clusterBalloonContentLayout: 'cluster#balloonAccordion',
            // Устанавливаем собственный макет.
            clusterBalloonItemContentLayout: customItemContentLayout,
            // Устанавливаем режим открытия балуна.
            // В данном примере балун никогда не будет открываться в режиме панели.
            clusterBalloonPanelMaxMapArea: 0,
            // Устанавливаем размеры макета контента балуна (в пикселях).
            clusterBalloonContentLayoutWidth: 300,
            clusterBalloonContentLayoutHeight: 200,

            gridSize: 180,
        })
        map.geoObjects.add(orderCluster);
    }

    function getContentBody(order) {
        let result = "<p></p><table style='margin-bottom: 15px;'><thead><td style=\"padding: 0 5px;\"><b>Название</b></td><td style=\"padding: 0 5px;\"><b>Объем</b></td><td style=\"padding: 0 5px;\"><b>Количество</b></td><td style=\"padding: 0 5px;\"><b>Цена</b></td></thead><tbody>";
        for (let i = 0; i < order.length; i++) {
            result += `<tr>
                    <td style="padding: 0 5px;">${order[i].product.title}</td>
                    <td style="padding: 0 5px;">${order[i].product.value}</td>
                    <td style="padding: 0 5px;">${order[i].count}</td>
                    <td style="padding: 0 5px;">${order[i].product.price}</td></tr>`;
        }
        result += "</tbody></table>";
        return result;
    }

    function getStatus(order) {
        switch (order.status) {
            case 1:
                return "<div><b>Статус</b>: Новый заказ</div>";
            case 2:
                return "<div><b>Статус</b>: Подготовлен для доставки</div>";
            case 3:
                return "<div><b>Статус</b>: В процессе доставки</div>";
            case 4:
                return "<div><b>Статус</b>: Выполнен</div>";
            case 5:
                return "<div><b>Статус</b>: Отменен</div>";
        }
    }

    function getClientInfo(client) {
        if ( client ) {
            return `<div><b>ФИО клиента</b>: ${client.name}</div><div><b>Номер телефона</b>: <a href="tel:${client.phone}">${client.phone}</a></div>`;
        } else {
            return `<div></div>`;
        }
    }

    function getAddress(item) {
        if (item.location.title) {
            return `<div><b>Адрес доставки</b>: <a target="_blank" href="https://2gis.ru/ulanude/geo/${item.location.longitude}%2C${item.location.latitude}">${item.location.title}</a></div>`;
        } else {
            return `<div><b>Адрес доставки</b>: ${item.order.address}</div>`;
        }
    }

    function getPreset(order) {
        switch (order.status) {
            case 1:
                return "islands#blueIcon";
            case 2:
                return "islands#darkgreenIcon";
            case 3:
                return "islands#darkorangeIcon";
            case 4:
                return "islands#pinkIcon";
            case 5:
                return "islands#blackIcon";
        }
    }

    function generatePoints(response) {
        response = JSON.parse(response);
        for (const index in response) {
            let item = response[index];
            if (item.order.status < 4) {
                console.log(item);
                if (orderCollection[item.id] === undefined) {
                    if (item.location) {
                        orderCollection[item.id] = {
                            Placemark: new ymaps.Placemark([item.location.latitude, item.location.longitude], {
                                balloonContentHeader: `<h4>Заказ #${item.id}</h4>`,
                                balloonContentBody: getClientInfo(item.client) + getStatus(item.order) + getContentBody(item.cart) + getAddress(item),
                                balloonContentFooter: `<h5>Общая стоимость заказа: ${item.cost}</h5>`,
                            }, {
                                preset: getPreset(item.order),
                            })
                        };
                        orderCollection[item.id].Placemark.orderId = item.id;
                        orderPoints.push(orderCollection[item.id].Placemark);
                    }
                } else {
                    orderCollection[item.id].Placemark.geometry.setCoordinates([item.location.latitude, item.location.longitude])
                }
                // map.geoObjects.add(carsCollection[item.DeviceId.SerialId].Placemark);
            } else {
                // console.log(orderPoints);
                orderPoints.find((element, index) => {
                    if (element.orderId === item.id) {
                        orderCluster.remove(element);
                        orderPoints.splice(index, 1);
                    }
                })
            }
        }
        return orderPoints;
    }

    function openBalloon(e) {
        console.log(e.originalEvent.currentTarget.properties);
    }

    let spik = {
        login: () => {
            $.ajax({
                url: "/admin/cars/login",
                async: false,
                success: (response) => {
                    app.login = response;
                },
                error: (e) => {
                    console.error(e);
                }
            })
        },
        units: () => {
            if (app.login === undefined) {
                spik.login();
            }
            $.ajax({
                url: "/admin/cars/units",
                async: false,
                data: {token: app.login.SessionId},
                success: (response) => {
                    app.allUnits = response.Units;
                },
                error: (e) => {
                    console.error(e);
                }
            })
        },
        subscribe: () => {
            if (app.allUnits === undefined) {
                spik.units();
            }
            let ids = [];
            for (const index in app.allUnits) {
                let item = app.allUnits[index];
                ids.push(item.UnitId);
            }
            $.ajax({
                url: "/admin/cars/subscribe",
                async: false,
                data: {token: app.login.SessionId, ids: JSON.stringify(ids)},
                success: (response) => {
                    app.subscribe = response;
                },
                error: (e) => {
                    console.error(e);
                }
            })
        },
        online: (callback) => {
            if (app.subscribe === undefined) {
                spik.subscribe();
            }
            $.ajax({
                url: "/admin/cars/online",
                data: {token: app.login.SessionId, subscribe: app.subscribe.SessionId.Id},
                success: response => {
                    if (typeof callback == 'function') {
                        callback.call(response);
                    } else {
                        app.collection = response;
                    }
                }
            })
        },
        generateCollection: () => {
            if (app.collection !== undefined) {
                spik.online();
            }
            let units = [];
            for (const index in app.collection.OnlineDataCollection.DataCollection) {
                let collectionItem = app.collection.OnlineDataCollection.DataCollection[index];
                let unitItem = app.allUnits[index];
                if (carPoints[index]) {
                    carPoints[index].geometry.setCoordinates([collectionItem.Navigation.Location.Latitude, collectionItem.Navigation.Location.Longitude]);
                } else {
                    let driver_name = "";
                    if ( (typeof(carUnits[unitItem.UnitId]) !== "undefined") && carUnits[unitItem.UnitId].driver !== null ) {
                        driver_name = `${carUnits[unitItem.UnitId].driver.family} ${carUnits[unitItem.UnitId].driver.name}`;
                    }
                    let placemark = new ymaps.Placemark([collectionItem.Navigation.Location.Latitude, collectionItem.Navigation.Location.Longitude], {
                        balloonContentHeader: `<h4 data-key="${index}">Автомобиль #${unitItem.Name}</h4>`,
                        balloonContentBody: `<p><b>Водитель</b>: ${driver_name}</p><p><b>Текущий адрес</b>: ${(collectionItem.Address !== '') ? collectionItem.Address : 'Unknown'}</p><p><b>Текущая скорость</b>: ${collectionItem.Navigation.Speed}</p>`,
                    }, {
                        iconLayout: 'default#image',
                        iconImageHref: '/img/track_icon.png',
                        iconImageSize: [50, 50],
                        iconImageOffset: [-25, -50],
                    });
                    placemark.events.add("balloonopen", openBalloon)
                    carPoints.push(placemark);
                    units.push({
                        Name: unitItem.Name,
                        UnitId: unitItem.UnitId,
                        CompanyId: unitItem.CompanyId,
                        ConnectionDateTime: collectionItem.ConnectionDateTime,
                        LastMessageTime: collectionItem.LastMessageTime,
                        DeviceId: collectionItem.DeviceId,
                        Navigation: collectionItem.Navigation,
                    });
                }
            }
            return units;
        }
    }

    ymaps.ready(() => {
        init();

        setInterval(() => {
            ajax("/admin/order/get-list").then(response => {
                orderCluster.add(generatePoints(response));
            });
        }, 1000);
        orderCluster.add(orderPoints);
        spik.online();
        setInterval(() => {
            index++;
            let collection = spik.generateCollection();
            carCluster.add(carPoints);
        }, 1000);
    })
})
