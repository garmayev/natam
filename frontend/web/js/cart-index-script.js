ymaps.ready(init);

console.log('RUN!');

$('.order-block > .btn.blue').on('click', (e) => {
    e.preventDefault();
    let target = $(e.currentTarget);
    if (!target.hasClass('finish')) {
        let closest = target.closest('.order-block');
        closest.removeClass('active').next().addClass('active');
    } else {
        $.ajax({})
        target.closest('#create-order').submit();
    }
});

$('[name=\'Client[phone]\']').mask('+7(999)999 9999');

myMap = undefined;
myPlacemark = undefined;

function init() {
    myMap = new ymaps.Map('map', {
        center: [51.76, 107.64],
        zoom: 12
    }, {});

    $('#order-address').suggestions({
        token: '2c9418f4fdb909e7469087c681aac4dd7eca158c',
        type: 'ADDRESS',
        constraints: {
            locations: {region: 'Бурятия'},
        },
        onSelect: function (suggestion) {
            console.log(suggestion)
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

    // Создание метки.
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
        ymaps.geocode(coords).then(function (res) {
            var firstGeoObject = res.geoObjects.get(0);
            let address = firstGeoObject.getAddressLine();

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
    }
}
