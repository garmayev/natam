let map, cars, orders, carCluster, orderCluster, home, objects,
    carsCollection = [],
    points = [],
    orderPoints = [],
    interval = 1000,
    initialMapPosition = [51.819879855767255, 107.60937851186925],
    initialMapZoom = 12,
    iteration = 0;

$(() => {
    function ajax(url) {
        return new Promise(function (resolve, reject) {
            let xhr = new XMLHttpRequest();
            xhr.open('GET', url, true);
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

            xhr.send();
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
        let int = setInterval(() => {
            ajax("/admin/spik/cars").then(response => {
                cars = JSON.parse(response);
                if (cars.length) {
                    for (const carsKey in cars) {
                        let item = cars[carsKey];
                        if ( item.DeviceId.SerialId !== "231790" ) {
                            if (carsCollection[item.DeviceId.SerialId] === undefined) {
                                carsCollection[item.DeviceId.SerialId] = {
                                    DeviceId: item.DeviceId.SerialId,
                                    Navigation: item.Navigation,
                                    Placemark: new ymaps.Placemark([item.Navigation.Location.Latitude, item.Navigation.Location.Longitude], {
                                        balloonContentHeader: `Устройство #${item.DeviceId.SerialId}`,
                                        balloonContent: item.Address,
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
                }
                iteration++;
            });
            ajax("/admin/order/get-list").then(response => {
                let orders = JSON.parse(response);
                console.log(orders);
                for (const index in orders) {
                    if ( orderPoints.length < index ) {
                        let order = orders[index];
                        let content = '';
                        for (const index in order.cart) {
                            let item = order.cart[index];
                            content += `<b>${item.product.title} (${item.product.value})</b>: ${item.count}<br>`;
                        }
                        content += `<br><p><i>Адрес доставки</i>: ${order.location.title}</p>`;

                        orderPoints.push(new ymaps.Placemark([order.location.latitude, order.location.longitude], {
                            balloonContentHeader: `<h3>Заказ #${order.id}</h3>`,
                            balloonContentBody: content,
                            balloonContentFooter: `<h4>Общая стоимость заказа: ${order.cost}</h4>`,
                        }, {
                            preset: "islands#darkBlueIcon"
                        }))
                    }
                }
            });
            orderCluster.add(orderPoints);
            if (iteration > 10) {
                clearInterval()
            }
        }, interval);
    })
})