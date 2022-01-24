let map, cars, orders, carCluster, orderCluster, home, objects,
    carsCollection = [],
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
            groupByCoordinates: false,
            clusterHideIconOnBalloonOpen: false,
            geoObjectHideIconOnBalloonOpen: false,
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
            if (now > expireDate) {
                ajax("/admin/spik/token").then(response => {
                    console.log("GET TOKEN");
                    console.log(response);
                })
            }
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
        });
    })
})