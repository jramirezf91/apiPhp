/**
 * Created by Juanito-PC on 10/12/2016.
 */
var empleadoControllers = angular.module('empleadoControllers', ['uiGmapgoogle-maps', 'chart.js']);

empleadoControllers.controller('datosUsuarioCtrl', ['$scope','$routeParams', '$http', 'auth', function ($scope, $routeParams, $http, auth) {

    console.log($routeParams);
    var id = angular.toJson($routeParams);
    console.log(id);
    datosUsuario(id);
    
    function datosUsuario($idUsuario){
         $http.post('http://localhost/apiPhp/V1/usuarios/obtenerUsuariosId', $idUsuario).then(function (r) {
            console.log(r.data);
            $scope.model = r.data;
        });

        $http.post('http://localhost:80/apiPhp/V1/estructuras/obtenerEstructurasUser', $idUsuario).then(function (r) {
            console.log(r.data);
            $scope.estruc = r.data;
        });
    }

    $scope.logout = function()
    {
        auth.logout();
    }

}]);

empleadoControllers.controller('usuariosListadoCtrl', ['$scope', '$http', 'auth', function ($scope, $http, auth) {

    listadoUsuario();    
    
    function listadoUsuario(){
        $http.get('http://localhost:80/apiPhp/V1/usuarios/obtenerUsuarios').then(function (r) {
            //console.log(r.data);
            $scope.model = r.data;
        });
    }

    $scope.retirar = function (id) {
       var v = 'http://localhost/apiPhp/V1/usuarios/' + id;

        if(confirm('Esta seguro de eliminar este usuario?')){
            $http.delete(v).then(function (r) {
                listadoUsuario();
            });
        }
    };

    $scope.logout = function()
    {
        auth.logout();
    }

}]);

empleadoControllers.controller('listadoEstructurasCtrl', ['$scope', '$http', 'auth', function ($scope,  $http, auth) {

    listadoEstructuras();

    function listadoEstructuras() {
        $http.get('http://localhost:80/apiPhp/V1/estructuras/obtenerEstructuras').then(function (r) {
            $scope.model = r.data;
        });
    }

    $scope.retirar = function (id) {
        var v = 'http://localhost/apiPhp/V1/estructuras/' + id;

        if(confirm('Esta seguro de eliminar esta estructura?')){
            $http.delete(v).then(function (r) {
                listadoEstructuras();
            });
        }
    };

    $scope.logout = function()
    {
        auth.logout();
    }

}]);

empleadoControllers.controller('verEstructurasCtrl', ['$scope','$routeParams', '$http', 'auth', function ($scope, $routeParams, $http, auth) {

    var id = angular.toJson($routeParams);
    console.log(id);
    datosEstructura(id);

    function datosEstructura($idEstructura){
        $http.post('http://localhost/apiPhp/V1/estructuras/obtenerEstructurasId', $idEstructura).then(function (r) {
            console.log(r.data);
            $scope.model = r.data;
        });

        /*$scope.map = {
            center: { latitude: $scope.model.datos.Latitud, longitude: $scope.model.datos.Longitud },
            zoom: 8
        };*/

    }

    $scope.logout = function()
    {
        auth.logout();
    }

}]);

empleadoControllers.controller('registrarUserCtrl', ['$scope', '$routeParams', '$http', 'auth', function ($scope, $routeParams, $http, auth) {

    $scope.registrar = function() {

        if(!existUsuario($scope.DNI)){

            var permi;
            if($scope.Permiso == 1){
                permi = "Si";
            }else{
                permi = "no";
            }

            if(angular.equals($scope.Pass, $scope.PassR)){

                var user = {
                        DNI: $scope.DNI,
                        nombre: $scope.Nombre,
                        apellido: $scope.Apellido,
                        contrasena: $scope.Pass,
                        direccion: $scope.Direccion,
                        permiso: permi
                };

                $http.post('http://localhost/apiPhp/V1/usuarios/registro', user).then(function (r) {
                    if(r.data.estado == 1){
                        alert(r.data.mensaje);
                        $scope.DNI = "";
                        $scope.Nombre = "";
                        $scope.Apellido = "";
                        $scope.Pass = "";
                        $scope.Direccion = "";
                        $scope.PassR = "";
                        $location.url("/user");
                    }else if(r.data.estado == 6){
                        alert(r.data.mensaje);
                    }
                });

            }else{
                alert("Las contraseñas no coinciden.");
            }
        }else{
            alert("El usuario que desea registrar ya existe en la base de datos.");
        }
    };

    function existUsuario($dni){

        var data = {
            DNI: $dni
        };

        $http.post('http://localhost/apiPhp/V1/usuarios/obtenerUsuariosId', data).then(function (r) {
            if(r.data.estado == 1){
                return true;
            }else{
                return false;
            }
        })
    }

    $scope.logout = function()
    {
        auth.logout();
    }


}]);

empleadoControllers.controller('modificarUserCtrl', ['$scope','$routeParams', '$http', 'auth', function ($scope, $routeParams, $http, auth) {

    var id = angular.toJson($routeParams);
    console.log(id);
    datosUsuario(id);
    var user;

    function datosUsuario($idUsuario){
        $http.post('http://localhost/apiPhp/V1/usuarios/obtenerUsuariosId', $idUsuario).then(function (r) {
            console.log(r.data);
            user = r.data.datos;
            $scope.DNI = r.data.datos.DNI;
            $scope.Nombre = r.data.datos.Nombre;
            $scope.Apellido = r.data.datos.Apellido;
            $scope.Direccion = r.data.datos.Direccion;
             $scope.Permiso = r.data.datos.Permiso;
        })
    }
    
    $scope.modificar = function () {

        var permi;
        if($scope.Permiso == 1){
            permi = "Si";
        }else{
            permi = "no";
        }

        if(angular.equals($scope.Pass, $scope.PassR)) {

            var usuario = {
                DNI: $scope.DNI,
                nombre: $scope.Nombre,
                apellido: $scope.Apellido,
                contrasena: $scope.Pass,
                direccion: $scope.Direccion,
                permiso: permi
            };

            $http.put('http://localhost/apiPhp/V1/usuarios/' + id, usuario).then(function (r) {
                if(r.data.estado == 1){
                    alert(r.data.mensaje);
                    $location.url("/user/" + id);
                }else{
                    alert(r.data.estado + ": " + r.data.mensaje);
                }
            })
        }
    }

    $scope.logout = function()
    {
        auth.logout();
    }

}]);

empleadoControllers.controller('registrarEstructuraCtrl', ['$scope', '$routeParams', '$http', 'auth', function ($scope, $routeParams, $http, auth) {

    $scope.registrarEs = function() {

        if(!existEstructura($scope.Nombre)){

           var user = {
                Nombre: $scope.Nombre,
                Direccion: $scope.Direccion,
                Latitud: $scope.Latitud,
                Longitud: $scope.Longitud
            };

            $http.post('http://localhost/apiPhp/V1/estructuras/registro', user).then(function (r) {
                if(r.data.estado == 1){
                    alert(r.data.mensaje);
                    $scope.Nombre = "";
                    $scope.Direccion = "";
                    $scope.Latitud = "";
                    $scope.Longitud = "";
                    $location.url("/estructuras");
                }else{
                    alert(r.data.mensaje);
                }
            });
        }else{
            alert("La estructura que desea registrar ya existe en la base de datos.");
        }
    };

    function existEstructura($nom){

        var data = {
            Nombre: $nom
        };

        $http.post('http://localhost/apiPhp/V1/estructuras/obtenerEstrNom', data).then(function (r) {
            if(r.data.estado == 0){
                return true;
            }else{
                return false;
            }
        })
    }

    $scope.logout = function()
    {
        auth.logout();
    }


}]);

empleadoControllers.controller('modificarUserCtrl', ['$scope','$routeParams', '$http', 'auth', function ($scope, $routeParams, $http, auth) {

    var id = angular.toJson($routeParams);
    console.log(id);
    datosUsuario(id);
    var estruct;

    function datosUsuario($idEstructura){
        $http.post('http://localhost/apiPhp/V1/estructuras/obtenerEstructurasId', $idEstructura).then(function (r) {
            console.log(r.data);
            estruct = r.data.datos;
            $scope.Nombre = r.data.datos.Nombre;
            $scope.Latitud = r.data.datos.Latitud;
            $scope.Direccion = r.data.datos.Direccion;
            $scope.Longitud = r.data.datos.Longitud;
        })
    }

    $scope.modificar = function () {

        if(confirm('Esta seguro de modificar esta estructura?')){
            var usuario = {
                Nombre: $scope.Nombre,
                Direccion: $scope.Direccion,
                Latitud: $scope.Latitud,
                Longitud: $scope.Longitud
            };

            $http.put('http://localhost/apiPhp/V1/estructuras/' + id, usuario).then(function (r) {
                if (r.data.estado == 1) {
                    alert(r.data.mensaje);
                    $location.url("/estructuras/" + id);
                } else {
                    alert(r.data.estado + ": " + r.data.mensaje);
                }
            })
        }
    }

    $scope.logout = function()
    {
        auth.logout();
    }

}]);

empleadoControllers.controller('loginCtrl',['$scope', '$rootScope', '$location', '$cookieStore', '$http',
    function ($scope, $rootScope, $location, $cookieStore, $http) {
            // reset login status
            $rootScope.globals = {};
            $cookieStore.remove('globals');
            //$http.defaults.headers.common.Authorization = 'Basic ';

            $scope.login = function () {
                $scope.dataLoading = true;
                var credenciales = {
                    DNI : $scope.DNI,
                    contrasena : $scope.password
                };

                $http.post('http://localhost/apiPhp/V1//usuarios/login', credenciales).then(function(r) {
                    console.log("rootscope" + r.data.estado);
                    if(r.data.estado == 1) {
                        $rootScope.globals = {
                            usuario: {
                                id: r.data.usuario.idUsuario,
                                permiso: r.data.usuario.Permiso
                            }
                        };
                        console.log("salir rootscope");
                        $cookieStore.put('globals', $rootScope.globals);
                        if(r.data.usuario.Permiso == 1){
                            console.log("permiso = 1");
                            $location.path('/admin');
                        }else {
                            console.log("permiso = 0");
                            $location.path('/userini');
                        }
                    } else {
                        $scope.error = r.data.message;
                        $scope.dataLoading = false;
                    }
                });
            };
    }]);

empleadoControllers.controller('useriniCtrl', ['$scope', '$http', 'auth', '$rootScope', function ($scope,  $http, auth, $rootScope) {

    listadoEstructurasUser($rootScope.globals.usuario.id);

    function listadoEstructurasUser($id) {

        var user = {
            idUser: $id
        };

        $http.post('http://localhost:80/apiPhp/V1/estructuras/obtenerEstructurasUser', user).then(function (r) {
            $scope.model = r.data;
        });
    }

    $scope.logout = function()
    {
        auth.logout();
    }

}]);

empleadoControllers.controller('admininiCtrl', ['$scope', '$http', 'auth', function ($scope,  $http, auth) {


    $scope.logout = function()
    {
        auth.logout();
    }

}]);

empleadoControllers.controller('addestrucCtrl', ['$scope', '$http', 'auth', '$routeParams', function ($scope,  $http, auth, $routeParams) {


    var id = angular.toJson($routeParams);

    $scope.idu = $routeParams.idUsuario;
    console.log("idusuario:");
    console.log($scope.idu);

    listadoEstructurassinusuario();

    function listadoEstructurassinusuario() {
        $http.get('http://localhost:80/apiPhp/V1/estructuras/estructurasSinUsuario').then(function (r) {
            $scope.estruc = r.data;
        });
    }

    $scope.anadir = function (idEstruc, iduser) {
        var v =
            {
                idUsuario: iduser,
                estructura: idEstruc
            };

        if(confirm('Esta seguro añadir esta estructura?')){
            $http.post('http://localhost/apiPhp/V1/estructuras/anadirEstrUser', v ).then(function (r) {
                listadoEstructurassinusuario();
            });
        }
    };


    $scope.logout = function()
    {
        auth.logout();
    }

}]);

empleadoControllers.controller('delestrucCtrl', ['$scope', '$http', 'auth', '$routeParams', function ($scope,  $http, auth, $routeParams) {

    var id = angular.toJson($routeParams);
    $scope.idu = $routeParams.idUsuario;
    listadoEstructurasUsuario(id);

    function listadoEstructurasUsuario($idUsuario) {
        $http.post('http://localhost:80/apiPhp/V1/estructuras/obtenerEstructurasUser', $idUsuario).then(function (r) {
            $scope.estruc = r.data;
        });
    }

    $scope.delete = function (idEstruc, iduser) {
        var v =
            {
                idUsuario: iduser,
                estructura: idEstruc
            };

        if(confirm('Esta seguro eliminar esta estructura?')){
            $http.post('http://localhost/apiPhp/V1/estructuras/eliminarEstrUser', v ).then(function (r) {
                listadoEstructurasUsuario(iduser);
            });
        }
    };



    $scope.logout = function()
    {
        auth.logout();
    }

}]);

empleadoControllers.controller('userverEstructurasCtrl', ['$scope','$routeParams', '$http', 'auth', function ($scope, $routeParams, $http, auth){

    var id = angular.toJson($routeParams);
    console.log(id);
    datosEstructura(id);

    function datosEstructura($idEstructura){

        var prueba;

        $http.post('http://localhost/apiPhp/V1/estructuras/obtenerEstruc', $idEstructura).then(function (r) {
            console.log(r.data);
            prueba = r.data;
            $scope.model = r.data;


            $scope.map = {center: {latitude: prueba.datos.Latitud, longitude: prueba.datos.Longitud }, zoom: 14 };
            $scope.options = {scrollwheel: false};
            $scope.markers= {
                idKey: 1,
                coords: {
                    latitude: prueba.datos.Latitud,
                    longitude: prueba.datos.Longitud
                },
                options: { draggable: true }
            };


            var n = prueba.datos.Vibraciones;

            console.log($scope.map);


            var ejex = [];
            var ejey = [];
            var ejez = [];
            var labels = [];

            for(var i = 0; i < n.length; i++){

                ejex[i] = n[i].X;
                ejey[i] = n[i].Y;
                ejez[i] = n[i].Z;
                labels[i] = "" + n[i].Fecha + " " + n[i].Hora;

            }

            var datas = {
                labels: labels,
                datasets: [
                    {
                        label: "My First dataset",
                        fill: false,
                        lineTension: 0.1,
                        backgroundColor: "rgba(75,192,192,0.4)",
                        borderColor: "#4BC0C0",
                        borderCapStyle: 'butt',
                        borderDash: [],
                        borderDashOffset: 0.0,
                        borderJoinStyle: 'miter',
                        pointBorderColor: "rgba(75,192,192,1)",
                        pointBackgroundColor: "#fff",
                        pointBorderWidth: 1,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(75,192,192,1)",
                        pointHoverBorderColor: "rgba(220,220,220,1)",
                        pointHoverBorderWidth: 2,
                        pointRadius: 1,
                        pointHitRadius: 10,
                        data: ejex,
                        spanGaps: false
                    }
                ]
            };
                var ctx = "myChart";
            var myLineChart = new Chart(ctx, {
                type: 'line',
                data: datas
            });
        });

    }

    $scope.logout = function()
    {
        auth.logout();
    }

}]);