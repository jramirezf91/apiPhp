var app = angular.module('myApp', [
  'ngRoute',
  'empleadoControllers',
  'ngCookies'

]);

app.config(['$routeProvider',
    function($routeProvider) {
      $routeProvider.
          when('/',{
              templateUrl: 'htmls/botones.html'
      }).
       when('/user', {
        templateUrl: 'htmls/listado.html',
        controller: 'usuariosListadoCtrl'
      })
          .when('/user/:idUsuario', {
          templateUrl: 'htmls/ver.html',
          controller: 'datosUsuarioCtrl'
      })
          .when('/estructuras', {
              templateUrl: 'htmls/listEstructuras.html',
              controller: 'listadoEstructurasCtrl'
      })
          .when('/estructuras/:idEstructura', {
              templateUrl: 'htmls/verEstructura.html',
              controller: 'verEstructurasCtrl'
      })
          .when('/registrar',{
              templateUrl: 'htmls/crearUser.html',
              controller: 'registrarUserCtrl'
      })
          .when('/modificar',{
              templateURl: 'htmls/modificarUser.html',
              controller: 'modificarUserCtrl'
      })
          .when('/registrarEs', {
              templateUrl: 'htmls/crearEstructura.html',
              controller: 'registrarEstructuraCtrl'
      })
          .when('/modificarEs', {
              templateUrl: 'htmls/modificarEstructura.html',
              controller: 'modificarEstructuraCtrl'
      })
          .when('/login', {
          templateUrl: 'login.html',
          controller: 'loginCtrl'
      })
          .otherwise({
          redirectTo: '/login'
      });
}]);


app.run(['$rootScope', '$location', '$cookieStore', '$http', function ($rootScope, $location, $cookieStore, $http) {

    $rootScope.globals = $cookieStore.get('globals') || {};

    $rootScope.$on('$locationChangeStart', function (event, next, current) {
        // redirect to login page if not logged in
        if ($location.path() !== '/login' && !$rootScope.globals.usuario) {
            $location.path('/login');
        }
    });
    
}]);