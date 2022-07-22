$(() => {
    let myMap, myPlacemark, multiRoute, target, index = 0, base = [51.835488, 107.683083];
    function createPlacemark(coords) {
        return new ymaps.Placemark(coords, {
            iconCaption: 'поиск...'
        }, {
            preset: 'islands#violetDotIconWithCaption',
            draggable: true
        });
    }
    function getAddress(coords) {
        myPlacemark.properties.set('iconCaption', 'поиск...');
        if ( multiRoute !== undefined ) {
            myMap.geoObjects.remove(multiRoute);
        }
        multiRoute = new ymaps.multiRouter.MultiRoute({
            referencePoints: [base, coords],
            params: {
                results: 10,
            }
        }, {
            boundsAutoApply: true,
        });
        ymaps.geocode(coords).then(function (res) {
            var firstGeoObject = res.geoObjects.get(0);
            let address = firstGeoObject.getAddressLine();

            target = firstGeoObject.properties.get('metaDataProperty');

            myPlacemark.properties
                .set({
                    iconCaption: [
                        firstGeoObject.getLocalities().length ? firstGeoObject.getLocalities() : firstGeoObject.getAdministrativeAreas(),
                        firstGeoObject.getThoroughfare() || firstGeoObject.getPremise()
                    ].filter(Boolean).join(', '),
                    balloonContent: firstGeoObject.getAddressLine()
                });
            $('#order-address').val(address);
            $('#location-title').val(address);
        });
        $('#location-latitude').val(coords[0]);
        $('#location-logintude').val(coords[1]);
        searchShortest(coords);
    }
    function searchShortest(coords)
    {
        multiRoute.model.events.add('requestsuccess', function() {
            let routes = multiRoute.getRoutes();
            let shortest = undefined;
            routes.each((route) => {
                if ( typeof shortest === "undefined" ) {
                    shortest = route;
                } else {
                    if (shortest.properties.get("distance").value > route.properties.get("distance").value)
                    {
                        shortest = route;
                    }
                }
            })
            if ( typeof shortest !== "undefined") {
                multiRoute.setActiveRoute(shortest);
            }
            myMap.geoObjects.add(multiRoute);
        });
    }
    function initMap() {
        if (myMap === undefined) {
            myMap = new ymaps.Map('map', {
                center: [51.76, 107.64],
                zoom: 12
            }, {});
        }
        $('#order-address').suggestions({
            token: '2c9418f4fdb909e7469087c681aac4dd7eca158c',
            type: 'ADDRESS',
            constraints: {
                locations: {region: 'Бурятия'},
            },
            onSelect: function (suggestion) {
                ymaps.geocode(suggestion.value, {
                    results: 1
                }).then(function (res) {
                    let placemark = res.geoObjects.get(0),
                        coords = placemark.geometry.getCoordinates(),
                        bounds = placemark.properties.get('boundedBy');

                    if (myPlacemark) {
                        myPlacemark.geometry.setCoordinates(coords);
                    } else {
                        myPlacemark = createPlacemark(coords);
                        myMap.geoObjects.add(myPlacemark);
                        myPlacemark.events.add('dragend', function () {
                            getAddress(myPlacemark.geometry.getCoordinates());
                        });
                    }
                    myMap.setBounds(bounds, {
                        checkZoomRange: true
                    });
                    getAddress(coords);
                });
            }
        });
        myMap.events.add('click', function (e) {
            let coords = e.get('coords');

            if (myPlacemark) {
                myPlacemark.geometry.setCoordinates(coords);
            } else {
                myPlacemark = createPlacemark(coords);
                myMap.geoObjects.add(myPlacemark);
                myPlacemark.events.add('dragend', function () {
                    getAddress(myPlacemark.geometry.getCoordinates());
                });
            }
            getAddress(coords);
        });
    }
    initMap();
})
