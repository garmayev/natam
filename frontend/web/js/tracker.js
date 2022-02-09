let map, cars, orders, carCluster, orderCluster, home, objects,
    carsCollection = [], orderCollection = [],
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

    function getContentBody(order)
    {
        let result = "<table style='margin-bottom: 15px;'><thead><td style=\"padding: 0 5px;\"><b>Название</b></td><td style=\"padding: 0 5px;\"><b>Объем</b></td><td style=\"padding: 0 5px;\"><b>Количество</b></td><td style=\"padding: 0 5px;\"><b>Цена</b></td></thead><tbody>";
        for ( let i = 0; i < order.length; i++ ) {
            result += `<tr>
                    <td style="padding: 0 5px;">${order[i].product.title}</td>
                    <td style="padding: 0 5px;">${order[i].product.value}</td>
                    <td style="padding: 0 5px;">${order[i].count}</td>
                    <td style="padding: 0 5px;">${order[i].product.price}</td></tr>`;
        }
        result += "</tbody></table>";
        return result;
    }

    function getStatus(order)
    {
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

    function getClientInfo(client)
    {
        return `<div><b>ФИО клиента</b>: ${client.name}</div><div><b>Номер телефона</b>: <a href="tel:${client.phone}">${client.phone}</a></div>`;
    }

    function getAddress(item)
    {
        if ( item.location.title ) {
            return `<div><b>Адрес доставки</b>: <a target="_blank" href="https://2gis.ru/ulanude/geo/${item.location.longitude}%2C${item.location.latitude}">${item.location.title}</a></div>`;
        } else {
            return `<div><b>Адрес доставки</b>: ${item.order.address}</div>`;
        }
    }

    function getPreset(order)
    {
        switch (order.status) {
            case 1: return "islands#blueIcon";
            case 2: return "islands#darkgreenIcon";
            case 3: return "islands#darkorangeIcon";
            case 4: return "islands#pinkIcon";
            case 5: return "islands#blackIcon";
        }
    }

    function generatePoints(response) {
        response = JSON.parse(response);
        for (const index in response) {
            let item = response[index];
            // console.log(item);
            if (item.order.status < 4) {
                if (orderCollection[item.id] === undefined) {
		    if (item.location) {
                    orderCollection[item.id] = {
                        Placemark: new ymaps.Placemark([item.location.latitude, item.location.longitude], {
                            balloonContentHeader: `<h4>Заказ #${item.id}</h4>`,
                            balloonContentBody: getContentBody(item.cart) + getClientInfo(item.client) + getStatus(item.order) + getAddress(item),
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
                    if ( element.orderId === item.id ) {
                        orderCluster.remove(element);
                        orderPoints.splice(index, 1);
                    }
                })
            }
        }
        return orderPoints;
    }

    ymaps.ready(() => {
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
            clusterHideIconOnBalloonOpen: false,
            geoObjectHideIconOnBalloonOpen: false,
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
        let request = [];
        // request.push(ajax("/admin/spik/token"));
        // request.push(ajax("/admin/spik/subscribe"));
        // request.push(ajax("/admin/spik/online"));
        // data = setInterval(() => {
        //     Promise.all(request).then(
        //         response => {
        //             console.log(request);
        //             cars = JSON.parse(response[0]);
        //             console.log(cars);
        //             if (cars !== null) {
        //                 for (const carsKey in cars) {
        //                     let item = cars[carsKey];
        //                     if (item.DeviceId.SerialId !== "231790") {
        //                         if (carsCollection[item.DeviceId.SerialId] === undefined) {
        //                             carsCollection[item.DeviceId.SerialId] = {
        //                                 DeviceId: item.DeviceId.SerialId,
        //                                 Navigation: item.Navigation,
        //                                 Placemark: new ymaps.Placemark([item.Navigation.Location.Latitude, item.Navigation.Location.Longitude], {
        //                                     balloonContentHeader: `Устройство #${item.DeviceId.SerialId}`,
        //                                     balloonContent: "Название: " + item.Name + "<br>" + item.Address,
        //                                 }, {
        //                                     preset: 'islands#redIcon',
        //                                 })
        //                             };
        //                             points.push(carsCollection[item.DeviceId.SerialId].Placemark);
        //                             // map.geoObjects.add(carsCollection[item.DeviceId.SerialId].Placemark);
        //                         } else {
        //                             carsCollection[item.DeviceId.SerialId].Placemark.geometry.setCoordinates([item.Navigation.Location.Latitude, item.Navigation.Location.Longitude])
        //                         }
        //                     }
        //                 }
        //                 carCluster.add(points);
        //             } else {
        //                 console.error(cars);
        //             }
        //         },
        //         reject => {
        //             console.log(reject);
        //         }
        //     )
        // }, interval);
        setInterval(() => {
            ajax("/admin/order/get-list").then(response => {
                orderCluster.add(generatePoints(response));
            });
        }, 1000);
        orderCluster.add(orderPoints);
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
        // setTimeout(() => {
        //     clearInterval(data)
        //     window.location.reload();
        // }, 30000)

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
                                console.log(cars);
                                for (const carsKey in cars) {
                                    let item = cars[carsKey];
                                    if (item.DeviceId.SerialId !== "231790") {
                                        if (carsCollection[item.DeviceId.SerialId] === undefined) {
                                            carsCollection[item.DeviceId.SerialId] = {
                                                DeviceId: item.DeviceId.SerialId,
                                                Navigation: item.Navigation,
                                                Placemark: new ymaps.Placemark([item.Navigation.Location.Latitude, item.Navigation.Location.Longitude],
                                                    {
                                                        balloonContentHeader: `Устройство #${item.DeviceId.SerialId}`,
                                                        balloonContent: "Название: " + item.Name + "<br>" + item.Address,
                                                    }, {
                                                        // Опции.
                                                        // Необходимо указать данный тип макета.
                                                        iconLayout: 'default#image',
                                                        // Своё изображение иконки метки.
                                                        iconImageHref: '/img/track_icon.png',
                                                        // Размеры метки.
                                                        iconImageSize: [50, 50],
                                                        iconImageOffset: [-25, -50],
                                                    }
                                                )
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
