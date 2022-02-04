let map, cars, orders, carCluster, orderCluster, home, objects,
    carsCollection = [],
    orderCollection = [],
    points = [],
    orderPoints = [],
    interval = 1000,
    initialMapPosition = [51.819879855767255, 107.60937851186925],
    initialMapZoom = 12,
    iteration = 0,
    count = 0, token = '', subscribe = '';

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

    ymaps.ready(() => {
        /**
         * Начальная инициализация глобальных переменных
         */
        function init() {
            let customItemContentLayout = ymaps.templateLayoutFactory.createClass(
                // Флаг "raw" означает, что данные вставляют "как есть" без экранирования html.
                '<h2 class=ballon_header>{{ properties.balloonContentHeader|raw }}</h2>' +
                '<div class=ballon_body>{{ properties.balloonContentBody|raw }}</div>' +
                '<div class=ballon_footer>{{ properties.balloonContentFooter|raw }}</div>'
            );
            map = new ymaps.Map('map', {
                center: initialMapPosition,
                zoom: initialMapZoom,
            });
            carCluster = new ymaps.Clusterer({
                preset: "islands#invertedRedClusterIcons",
                groupByCoordinates: false,
                clusterHideIconOnBalloonOpen: false,
                geoObjectHideIconOnBalloonOpen: false,
            })
            map.geoObjects.add(carCluster)
            orderCluster = new ymaps.Clusterer({
                // Макет метки кластера pieChart.
                clusterIconLayout: 'default#pieChart',
                // Радиус диаграммы в пикселях.
                clusterIconPieChartRadius: 25,
                // Радиус центральной части макета.
                clusterIconPieChartCoreRadius: 10,
                // Ширина линий-разделителей секторов и внешней обводки диаграммы.
                clusterIconPieChartStrokeWidth: 3,
                // Определяет наличие поля balloon.
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
                clusterBalloonContentLayoutWidth: 250,
                clusterBalloonContentLayoutHeight: 200,
                gridSize: 110,
            })
            map.geoObjects.add(orderCluster);
        }

        function createPlacemark(coordinates, properties = null, options = null) {
            return new ymaps.Placemark(coordinates, properties, options);
        }

        function updatePlacemark(placemark, options) {
            placemark.options.set(options.name, options.value);
        }

        /**
         * Получение информации обо всех заказах
         * @param callback
         */
        function getOrderList(callback) {
            ajax("/admin/order/get-list").then(callback);
        }

        /**
         * Создание коллекции меток на основе информации о заказах
         * @param orders
         * @returns {*[]}
         */
        function buildOrderCollection(orders) {
            /**
             * Генерация названия настроек метки в зависимости от статуса заказа
             * @returns {string}
             */
            function getPreset(order) {
                switch (order.status) {
                    case 1:
                        return "blue";
                    case 2:
                        return "orange";
                    case 3:
                        return "violet";
                    case 4:
                        return "green";
                    case 5:
                        return "red";
                }
            }

            let mark = {
                clientInfo: (client) => {
                    return `<div style="padding: 5px;"><b>Клиент</b>: <div>Имя: ${client.name}</div><div>Номер: ${client.phone}</div></div>`;
                },
                addressInfo: (location) => {
                    return `<div style="padding: 5px;"><b>Адрес доставки</b>: ${location.title}</div>`
                },
                cartInfo: (cart) => {
                    let result = '<div style="padding: 5px"><b>Содержимое заказа</b>: <table width="100%">';
                    for (const index in cart) {
                        let item = cart[index];
                        result += `<tr><td style="padding: 0 10px">${item.product.title}</td><td style="padding: 0 10px">${item.product.value}</td><td style="padding: 0 10px">${item.product.price}</td></tr>`;
                    }
                    result += '</table></div>';
                    return result;
                },
                orderInfo: (order) => {
                    return `<div style="padding: 5px"><b>Дата доставки</b>: ${new Date(order.delivery_date).toLocaleString("ru-RU")}</div>`;
                },
                orderStatus: (order) => {
                    switch (order.status) {
                        case 1:
                            return `<div style="padding: 5px"><b>Статус</b>: Новый заказ</div>`;
                        case 2:
                            return `<div style="padding: 5px"><b>Статус</b>: Подготовлен для доставки</div>`;
                        case 3:
                            return `<div style="padding: 5px"><b>Статус</b>: В процессе доставки</div>`;
                        case 4:
                            return `<div style="padding: 5px"><b>Статус</b>: Выполнен</div>`;
                        case 5:
                            return `<div style="padding: 5px"><b>Статус</b>: Отменен</div>`;
                    }

                }
            }
            // orderPoints = [];
            // for (const index in orders) {
            //     let item = orders[index];
            //     if ( orderCollection[index] !== undefined ) {
            //         updatePlacemark(orderCollection[index], {
            //             'balloonContentBody': `${mark.clientInfo(item.client)}${mark.addressInfo(item.location)}${mark.orderStatus(item.order)}${mark.orderInfo(item.order)}${mark.cartInfo(item.cart)}`,
            //             'iconColor': getPreset(item.order)
            //         })
            //     } else {
            //         item.placemark = createPlacemark([item.location.latitude, item.location.longitude], {
            //             balloonContentHeader: `<h4>Заказ #${item.id}</h4>`,
            //             balloonContentBody: `${mark.clientInfo(item.client)}${mark.addressInfo(item.location)}${mark.orderStatus(item.order)}${mark.orderInfo(item.order)}${mark.cartInfo(item.cart)}`,
            //             balloonContentFooter: `<h5>Общая сумма заказа: ${item.cost}</h5>`,
            //         }, {
            //             preset: "islands#circleDotIcon",
            //             iconColor: getPreset(item.order),
            //         });
            //     }
            //     orderCollection[item.id] = item.placemark;
            //     orderPoints.push( item.placemark);
            // }
            for (const index in orders) {
                let item = orders[index];
                if (orderCollection[item.id] === undefined) {
                    orderCollection[item.id] = item;
                    orderCollection[item.id].placemark = createPlacemark([item.location.latitude, item.location.longitude], {
                        balloonContentHeader: `<h4>Заказ #${item.id}</h4>`,
                        balloonContentBody: `${mark.clientInfo(item.client)}${mark.addressInfo(item.location)}${mark.orderStatus(item.order)}${mark.orderInfo(item.order)}${mark.cartInfo(item.cart)}`,
                        balloonContentFooter: `<h5>Общая сумма заказа: ${item.cost}</h5>`,
                    }, {
                        preset: "islands#circleDotIcon",
                        iconColor: getPreset(item.order),
                    })
                    orderPoints.push(orderCollection[item.id].placemark);
                } else {
                    orderCollection[item.id].placemark.options.set("balloonContentBody", `${mark.clientInfo(item.client)}${mark.addressInfo(item.location)}${mark.orderStatus(item.order)}${mark.orderInfo(item.order)}${mark.cartInfo(item.cart)}`);
                    orderCollection[item.id].placemark.options.set("iconColor", getPreset(item.order));
                }
            }
            orderCluster.add(orderPoints);
        }

        function buildOrderCluster(collection) {
            console.log(orderPoints);
            // orderCluster.removeAll();
            // orderCluster.add(orderPoints);
        }

        let request = [];
        init();
        setInterval(() => {
            getOrderList((response) => {
                let orders = JSON.parse(response);
                buildOrderCluster(buildOrderCollection(orders));
            });
        }, 1000);
        // ajax("/admin/order/get-list").then(response => {
        //     let orders = JSON.parse(response);
        //     for (const index in orders) {
        //         if (orderPoints.length < orders.length) {
        //             let order = orders[index];
        //             let content = '';
        //             for (const index in order.cart) {
        //                 let item = order.cart[index];
        //                 content += `<b>${item.product.title} (${item.product.value})</b>: ${item.count}<br>`;
        //             }
        //             let date = new Date(order.order.delivery_date * 1000);
        //             console.log(order.location !== null);
        //             if (order.location !== null)
        //                 if (order.location.hasOwnProperty("title")) {
        //                     content += `<br><p><i>Адрес доставки</i>: ${order.location.title}</p><p><i>Дата доставки</i>: ${date}</p>`;
        //                     // if (order.location.title !== undefined) content += `<br><p><i>Адрес доставки</i>: ${order.location.title}</p><p><i>Дата доставки</i>: ${date}</p>`;
        //                     orderPoints.push(new ymaps.Placemark([order.location.latitude, order.location.longitude], {
        //                         balloonContentHeader: `<h3>Заказ #${order.id}</h3>`,
        //                         balloonContentBody: content,
        //                         balloonContentFooter: `<h4>Общая стоимость заказа: ${order.cost}</h4>`,
        //                     }, {
        //                         preset: "islands#darkBlueIcon"
        //                     }))
        //                 }
        //         }
        //     }
        //     orderCluster.add(orderPoints);
        // });

        ajax("/admin/spik/test").then(response => {
            let regexp = /Date\(([0-9]*)/;
            let now = new Date(Date.now())
            let expireDate = new Date(parseInt(regexp.exec(JSON.parse(response).token.expireDate)[1]));
            console.log(now, expireDate);
            ajax("/admin/spik/token").then(response => {
                console.log("GET TOKEN");
                console.log(response);
                ajax("/admin/spik/get-units-page").then(response => {
                    console.log("GET UNITS PAGE");
                    console.log(JSON.parse(response))
                    ajax("/admin/spik/subscribe").then(response => {
                        console.log("GET SUBSCRIBE");
                        console.log(JSON.parse(response));
                    }).then(() => {
                        let data = setInterval(() => {
                            ajax("/admin/spik/online").then(response => {
                                cars = JSON.parse(response);
                                for (const carsKey in cars) {
                                    let item = cars[carsKey];
                                    if (item.DeviceId.SerialId !== "231790") {
                                        if (carsCollection[item.DeviceId.SerialId] === undefined) {
                                            carsCollection[item.DeviceId.SerialId] = {
                                                DeviceId: item.DeviceId.SerialId,
                                                Navigation: item.Navigation,
                                                Placemark: new ymaps.Placemark([item.Navigation.Location.Latitude, item.Navigation.Location.Longitude], {
                                                    balloonContentHeader: `Устройство #${item.DeviceId.SerialId}`,
                                                    balloonContent: "Название: " + item.Name + "<br>" + item.Address,
                                                }, {
                                                    preset: 'islands#redIcon',
                                                })
                                            };
                                            points.push(carsCollection[item.DeviceId.SerialId].Placemark);
                                            // map.geoObjects.add(carsCollection[item.DeviceId.SerialId].Placemark);
                                        } else {
                                            carsCollection[item.DeviceId.SerialId].Placemark.geometry.setCoordinates([item.Navigation.Location.Latitude, item.Navigation.Location.Longitude])
                                        }
                                    }
                                }
                                carCluster.add(points);
                            })
                        }, interval)
                    });
                })
            })

        });
    })
})