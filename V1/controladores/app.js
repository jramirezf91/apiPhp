var app = angular.module('myApp', [
  'ngRoute',
  'empleadoControllers',
  'ngCookies'

]);

app.config(['$routeProvider',
    function($routeProvider) {
      $routeProvider.
          when('/admin',{
              templateUrl: 'htmls/botones.html',
              controller: 'admininiCtrl'
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
          .when('/userini', {
              templateUrl: 'htmls/userini.html',
              controller: 'useriniCtrl'
      })
          .otherwise({
          redirectTo: '/login'
      });
}]);


app.run(['$rootScope', '$location', '$cookieStore', function ($rootScope, $location, $cookieStore) {

    $rootScope.globals = $cookieStore.get('globals') || {};

    $rootScope.$on('$locationChangeStart', function (event, next, current) {
        // redirect to login page if not logged in
        if ($location.path() !== '/login' && !$rootScope.globals.usuario) {
            $location.path('/login');
        }

        if($location.path() == '/'){
            if($rootScope.globals.usuario.permiso == 1){
                $location.path('/admin');
            }else{
                $location.path('/userini');
            }
        }
    });
    
}]);

app.factory("auth", function ($cookies, $cookieStore, $location) {
    return{
        logout : function () {
            $cookieStore.remove("id");
            $cookieStore.remove("permiso");

            $location.path('/login');
        }
    }
    
});