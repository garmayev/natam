let baseUrl = 'https://spic.scout365.ru:8081/spic/auth/rest/';
let loginServerUrl = baseUrl + 'Login';
let logoutServerUrl = baseUrl + 'Logout';

let authorizationToken = null;

function loginOnServer(request) {
    // return response = requestProvider.POST(loginServerUrl, request);
    return $.ajax({
        type: "POST",
        url: loginServerUrl,
        data: request,
        async: false,
    }).responseText;
}

function logoutOnServer(request) {
    return requestProvider.GETAuthorized(logoutServerUrl,
        null,
        authorizationToken);
}

function generateRequest() {
    return {
        Login: 'garmayev@yandex.ru',
        Password: '12345',
        TimeStampUtc: '/Date(' + new Date().getTime() + ')/',
        TimeZoneOlsonId: 'Asia/Irkutsk',
        CultureName: 'ru-ru',
        UiCultureName: 'ru-ru'
    };
}

function login() {
    //формируем запрос
    let request = generateRequest();

    //отправляем запрос
    let response = loginOnServer(request);

    //получаем токен авторизации
    console.log(response)
    authorizationToken = response.response.SessionId;
    console.log(authorizationToken);
}

function logout() {
    logoutOnServer();
}

login();