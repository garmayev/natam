$(() => {
    let token = "";

    // Это класс, упрощающий отправку запросов на сервер и получение ответа.
    function HttpJsonRequestProvider() {

        let xmlhttp = new XMLHttpRequest();

        let sendRequest = function (url, sendData, method, authorizationToken) {
            if (!url)
                throw new Error('Url required.');
            //если метод не передан, то подразумевается POST
            method = method || "POST";
            //если sendData не строка, то делаем из нее или пустого объекта строку
            if (!(typeof sendData == 'string' || sendData instanceof String)) {
                sendData = JSON.stringify(sendData || {});
            }

            //создаем соединение
            xmlhttp.open(method, url, false);
            //задаем header
            xmlhttp.setRequestHeader("Accept", "json");
            xmlhttp.setRequestHeader("Content-Type", "application/json");
            //если приложен authorizationToken
            if (authorizationToken) {
                xmlhttp.setRequestHeader("ScoutAuthorization", authorizationToken);
            }
            //отправляем
            xmlhttp.send(sendData);

            //возвращаем статус и тело ответа
            let responseText = xmlhttp.responseText;
            if (responseText) {
                try {
                    responseText = JSON.parse(responseText);
                } catch (e) {
                    alert("Incorrect JSON response from server");
                }
            }
            return {
                status: xmlhttp.status,
                response: responseText
            };
        };
        this.GET = function (url, sendData) {
            return sendRequest(url, sendData, 'GET');
        };
        this.POST = function (url, sendData) {
            return sendRequest(url, sendData, 'POST');
        };
        this.GETAuthorized = function (url, sendData, authorizationToken) {
            return sendRequest(url, sendData, 'GET', authorizationToken);
        };
        this.POSTAuthorized = function (url, sendData, authorizationToken) {
            return sendRequest(url, sendData, 'POST', authorizationToken);
        };
    }

    let requestProvider = new HttpJsonRequestProvider();

    ymaps.ready(() => {
        let spik = new SPIK();
    })

    let address = {
        _base: "http://login.scout-gps.ru:8081/spic/",
        // Адрес метода входа в систему
        loginUrl: function () {
            return `${this._base}auth/rest/Login`;
        },
        // Адрес метода получения количества юнитов
        getAllUnitsCountUrl: function () {
            return `${this._base}units/rest/`;
        },
        // Адрес метода получения страницы юнитов
        getAllUnitsPagedUrl: function () {
            return `${this._base}units/rest/getAllUnitsPaged`;
        },
        // Адрес метода создания подписки на получение онлайн-данных
        subscribeUrl: function () {
            return `${this._base}OnlineDataService/rest/Subscribe`
        },
        // Адрес метода получения онлайн-данных по идентификатору подписки
        getOnlineDataUrl: function () {
            return `${this._base}OnlineDataService/rest/GetOnlineData`;
        }
    }

    class SPIK {
        _token;
        _unit_count;
        _units = [];
        _sId;

        get token() {
            return this._token;
        }
        set token(value) {
            this._token = value;
        }
        get unitCount() {
            return this._unit_count;
        }
        get units() {
            return this._units;
        }
        addUnit(unit) {
            this._units[unit.id] = unit;
        }
        findUnit(uid) {
            return this._units[uid];
        }
        removeUnit(uid) {
            this._units.splice(this._units[uid], 1)
        }
        static uids(units)
        {
            let ids = [];
            for (const index in units) {
                ids.push(index);
            }
            return ids;
        }

        subscribe(token)
        {
            this._sessionId = requestProvider.POSTAuthorized(address.subscribeUrl(),
                {
                    UnitIds: SPIK.uids(this._units)
                },
                token).response.SessionId;
            return this._sessionId.Id;
        }

        constructor(authorizationToken = undefined)
        {
            if ( this.token === undefined ) {
                let request = {
                    Login: 'garmayev@yandex.ru',
                    Password: 12345,
                    TimeStampUtc: '/Date(' + new Date().getTime() + ')/',
                    TimeZoneOlsonId: 'Europe/Moscow',
                    CultureName: 'ru-ru',
                    UiCultureName: 'ru-ru'
                };
                let loginResponse = requestProvider.POST(address.loginUrl(), request).response;
                if (!loginResponse || !loginResponse.SessionId) {
                    alert("Login failed");
                    return;
                }
                authorizationToken = loginResponse.SessionId;
                this.token = loginResponse.SessionId;
            }
            this._unit_count = requestProvider.GETAuthorized(address.getAllUnitsCountUrl(), null, authorizationToken).response;
            let units = requestProvider.POSTAuthorized(
                address.getAllUnitsPagedUrl(),
                {
                    Offset: 0,
                    Count: this._unit_count
                },
                authorizationToken
            ).response.Units;
            for (const unitsKey in units) {
                this.addUnit(new Unit(units[unitsKey]));
            }
            this._sId = this.subscribe(this.token);
            for (const index in this._units) {
                this._units[index].getOnline(this._sId, this.token);
            }
        }
    }

    class Unit
    {
        _id;
        _companyId;
        _name;
        _sessionId;

        get id() {
            return this._id;
        }
        set id(value) {
            this._id = value;
        }
        get companyId() {
            return this._companyId;
        }
        set companyId(value) {
            this._companyId = value;
        }
        get name() {
            return this._name;
        }
        set name(value) {
            this._name = value;
        }

        constructor(unit) {
            this.id = unit.UnitId;
            this.companyId = unit.CompanyId;
            this.name = unit.Name;
        }

        getOnline(sId, token)
        {
            return requestProvider.POSTAuthorized(address.getOnlineDataUrl(),
                sId,
                token)
            .response.OnlineDataCollection;
        }
    }
})