/**
 * Created by Juanito-PC on 10/12/2016.
 */
var empleadoControllers = angular.module('empleadoControllers', ['uiGmapgoogle-maps', 'chart.js', 'ui.bootstrap', 'ngAnimate']);

empleadoControllers.controller('datosUsuarioCtrl', ['$scope','$routeParams', '$http', 'auth', function ($scope, $routeParams, $http, auth) {

    console.log($routeParams);
    var id = angular.toJson($routeParams);
    console.log(id);
    datosUsuario(id);
    
    function datosUsuario($idUsuario){
         $http.post('http://localhost/apiPhp/V1/usuarios/obtenerUsuariosId', $idUsuario).then(function (r) {
            console.log(r.data);
            $scope.model = r.data;
             estructuser($idUsuario);
        });
    }

    function estructuser($idUsuario) {
        $http.post('http://localhost/apiPhp/V1/estructuras/obtenerEstructurasUser', $idUsuario).then(function (r) {
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
        $http.get('http://localhost/apiPhp/V1/usuarios/obtenerUsuarios').then(function (r) {
            console.log(r.data);
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
        $http.get('http://localhost/apiPhp/V1/estructuras/obtenerEstructuras').then(function (r) {
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

empleadoControllers.controller('registrarUserCtrl', ['$scope', '$routeParams', '$http', 'auth','$location', function ($scope, $routeParams, $http, auth, $location) {
   // $scope.foto={value:'0'};

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

                console.log(user);

                $http.post('http://localhost/apiPhp/V1/usuarios/registro', user).then(function (r) {
                    if(r.data.estado == 1){
                        alert(r.data.mensaje);
                        /*$scope.DNI = "";
                        $scope.Nombre = "";
                        $scope.Apellido = "";
                        $scope.Pass = "";
                        $scope.Direccion = "";
                        $scope.PassR = "";*/
                        $location.url("/user");

                    }else if(r.data.estado == 6){
                        alert(r.data.mensaje);
                    }
                });
                for(var i = 0; i<2000; i++){}
                //
                /*//$scope.foto={value:'0'};
                console.log("Check:  " + $scope.foto.value);
                if(angular.equals($scope.foto.value, "1")){
                    $location.url("/fotoUser/" + $scope.DNI);
                }else{

                    location.href = "http://localhost:63342/apiPhp/V1/controladores/#!/user";
                }*/

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

empleadoControllers.controller('modificarUserCtrl', ['$scope','$routeParams', '$http', 'auth', '$location', function ($scope, $routeParams, $http, auth, $location) {
    console.log($routeParams);
    var id = angular.toJson($routeParams);
    console.log(id);
    modUsuario(id);
    var user;

    function modUsuario($idUsuario){
        $http.post('http://localhost/apiPhp/V1/usuarios/obtenerUsuariosId', $idUsuario).then(function (r) {
            console.log(r.data);
            user = r.data.datos;
            $scope.DNI = r.data.datos.DNI;
            $scope.Nombre = r.data.datos.Nombre;
            $scope.Apellido = r.data.datos.Apellido;
            $scope.Direccion = r.data.datos.Direccion;
            $scope.Permiso = r.data.datos.Permiso;
            $scope.idUsuario = r.data.datos.idUsuario;
            $scope.Pass = "";
            $scope.PassR = "";
        })
    }
    
    $scope.modificar = function (id) {

        var permi;
        if($scope.Permiso == 1){
            permi = "Si";
        }else{
            permi = "No";
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
            var v = 'http://localhost/apiPhp/V1/usuarios/' + id;

            console.log(v);
            console.log(usuario);

            $http.put(v, usuario).then(function (r) {
                if(r.data.estado == 1){
                    alert(r.data.mensaje);
                    $location.url("/user/" + id);
                }else{
                    alert(r.data.estado + ": " + r.data.mensaje);
                }
            })
        }else{
            alert("Las contraseñas no coinciden.");

        }
    };

    $scope.logout = function()
    {
        auth.logout();
    }

}]);

empleadoControllers.controller('registrarEstructuraCtrl', ['$scope', '$routeParams', '$http', 'auth', '$location', 'upload', function ($scope, $routeParams, $http, auth, $location, upload) {

    $scope.registrarEs = function() {

        console.log($scope.file);

        if(!existEstructura($scope.Nombre)){

           var user = {
                Nombre: $scope.Nombre,
                Direccion: $scope.Direccion,
                Latitud: $scope.Latitud,
                Longitud: $scope.Longitud,
                Foto: null
            };


           /*if($scope.file != null){
               upload.uploadFile($scope.file).then(function(res)
               {
                   console.log(res);
                   //user.Foto = res;
               })

           }*/

        console.log(user);

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
          // $location.url("/estructuras");
        }else{
            alert("La estructura que desea registrar ya existe en la base de datos.");
        }
    };

    function existEstructura($nom){

        var data = {
            NomEstructura: $nom
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

empleadoControllers.controller('modificarEstructuraCtrl', ['$scope','$routeParams', '$http', 'auth', '$location', function ($scope, $routeParams, $http, auth, $location) {

    var id = angular.toJson($routeParams);
    console.log(id);
    modEstruc(id);
    var estruct;

    function modEstruc($idEstructura){


        $http.post('http://localhost/apiPhp/V1/estructuras/obtenerEstructurasId', $idEstructura).then(function (r) {
            console.log(r.data);
            estruct = r.data.datos;
            $scope.Nombre = r.data.datos.Nombre;
            $scope.Latitud = r.data.datos.Latitud;
            $scope.Direccion = r.data.datos.Direccion;
            $scope.Longitud = r.data.datos.Longitud;
            $scope.idEstructura = r.data.datos.idEstructura;
        })
    }

    $scope.modificar = function (id) {


        var v = 'http://localhost/apiPhp/V1/estructuras/' + id;
        console.log(v);


        if(confirm('Esta seguro de modificar esta estructura?')){
            var usuario = {
                Nombre: $scope.Nombre,
                Direccion: $scope.Direccion,
                Latitud: $scope.Latitud,
                Longitud: $scope.Longitud
            };

            console.log(usuario);


            $http.put(v, usuario).then(function (r) {
                if (r.data.estado == 1) {
                    alert(r.data.mensaje);
                    $location.url("/estructuras/" + id);
                } else {
                    alert(r.data.estado + ": " + r.data.mensaje);
                }
            })
        }
    };

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

                        var per;
                        if(r.data.usuario.Permiso == 1){
                            per = "Administrador";
                        }else{
                            per = "Trabajador";
                        }
                        console.log(per);
                        $rootScope.globals = {
                            usuario: {
                                id: r.data.usuario.idUsuario,
                                permiso: r.data.usuario.Permiso,
                                perm: per,
                                Nombre: r.data.usuario.Nombre,
                                Apellido: r.data.usuario.Apellido
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
                        $scope.error = r.data.mensaje;
                        $scope.dataLoading = false;
                        alert(r.data.mensaje);
                    }
                });
            };
    }]);

empleadoControllers.controller('useriniCtrl', ['$scope', '$http', 'auth', '$rootScope', function ($scope,  $http, auth, $rootScope) {

    listadoEstructurasUser($rootScope.globals.usuario.id);

    function listadoEstructurasUser($id) {

        var user = {
            idUsuario: $id
        };

        $http.post('http://localhost/apiPhp/V1/estructuras/obtenerEstructurasUser', user).then(function (r) {
            $scope.model = r.data;
            console.log($scope.model);
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

        console.log(v);

        if(confirm('Esta seguro añadir esta estructura?')){
            $http.post('http://localhost/apiPhp/V1/estructuras/anadirEstrUser', v ).then(function (r) {
                console.log("antes if");
                if(r.data.estado == 1){
                    listadoEstructurassinusuario();
                }
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
                listadoEstructurasUsuario(id);
            });
        }
    };



    $scope.logout = function()
    {
        auth.logout();
    }

}]);

empleadoControllers.controller('userverEstructurasCtrl', ['$scope','$routeParams', '$http', 'auth', function ($scope, $routeParams, $http, auth){

    //var id = angular.toJson($routeParams);
    var id = $routeParams;
    console.log(id);
    $scope.diainicio = new Date();
    $scope.diafin = new Date();
    $scope.diainicio.setDate($scope.diainicio.getDate());
    var num =$scope.diainicio.getMonth()+1;
    var inicio = $scope.diainicio.getFullYear()+"-"+num+"-0"+$scope.diainicio.getDate();
    num = $scope.diafin.getMonth()+1;
    var fin = $scope.diafin.getFullYear()+"-"+num+"-0"+$scope.diafin.getDate();
    console.log($scope.diainicio);
    console.log($scope.diafin);
    console.log(inicio);
    console.log(fin);
    datosEstructura(id);



    function datosEstructura($idEstructura){

        var prueba;
        var user = {
            idEstructura: $idEstructura.idEstructura,
            inicio: fin,
            fin: inicio
        };

        console.log(user);


        $http.post('http://localhost/apiPhp/V1/estructuras/obtenerEstruc', user).then(function (r) {
            console.log(r.data);
            prueba = r.data;
            $scope.model = r.data;


            $scope.map = {center: {latitude: prueba.datos.Latitud, longitude: prueba.datos.Longitud }, zoom: 15 };
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



            var ejey = [];

            var labels = [];

            for(var i = 0; i < n.length; i++){
                ejey[i] = n[i].Y;
                labels[i] = "" + n[i].Fecha + " " + n[i].Hora;

            }


            var datasY = {
                labels: labels,
                datasets: [
                    {
                        label: "EjeX",
                        fill: false,
                        lineTension: 0.1,
                        backgroundColor: "rgba(75,192,192,0.4)",
                        borderColor: "#bec022",
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
                        pointRadius: 0,
                        pointHitRadius: 10,
                        data: ejey,
                        spanGaps: false
                    }
                ]
            };
            var ctx = "EjeY";
            var myLineChartY = new Chart(ctx, {
                type: 'line',
                data: datasY
            });



        });

    }

    $scope.logout = function()
    {
        auth.logout();
    }

}]);

empleadoControllers.controller('cambiarFotoCtrl', ['$scope', '$routeParams', '$http', 'auth','$location', 'upload', function ($scope, $routeParams, $http, auth, $location, upload)
{

    var dni = angular.toJson($routeParams);
    //console.log(dni);
    //obtenerFoto(dni);

    function obtenerFoto($dni){
        $http.post('http://localhost/apiPhp/V1/usuarios/buscarFoto', $dni).then(function (r) {
            console.log(r.data);
            $scope.foto = r.data.datos.Foto;
        });
    }



    $scope.addFoto = function() {
        console.log($scope.file);

        if($scope.file != null){
             upload.uploadFile($scope.file, dni).then(function(res)
             {
                 console.log(res);
                 //user.Foto = res;
             })
        }else{
            $location.url('/user');
        }


    };



    $scope.logout = function()
    {
        auth.logout();
    }


}]);

empleadoControllers.controller('defectosCtrl', ['$scope', '$http', 'auth', '$rootScope', function ($scope,  $http, auth, $rootScope) {

    listadoDefectosUser($rootScope.globals.usuario.id);

    function listadoDefectosUser($id) {

        var user = {
            idUsuario: $id
        };

        $http.post('http://localhost/apiPhp/V1/defectos/obtenerDefectoUser', user).then(function (r) {
            $scope.model = r.data;
            console.log($scope.model);
        });
    }

    $scope.retirar = function (id) {
        var v = 'http://localhost/apiPhp/V1/defectos/' + id;

        if(confirm('Esta seguro de eliminar este defecto?')){
            $http.delete(v).then(function (r) {
                listadoDefectosUser($rootScope.globals.usuario.id);
            });
        }
    };


    $scope.logout = function()
    {
        auth.logout();
    }

}]);

empleadoControllers.controller('addDefectosCtrl', ['$scope', '$http', 'auth', '$rootScope', "$location", function ($scope,  $http, auth, $rootScope, $location) {

    $scope.rangs = true;

    var iduser = $rootScope.globals.usuario.id;
    listadoEstructurasUser(iduser);

    $scope.LimitInf = null;
    $scope.LimitSup = null;
    $scope.rangi = true;



    function listadoEstructurasUser($id) {

        var user = {
            idUsuario: $id
        };

        $http.post('http://localhost/apiPhp/V1/estructuras/obtenerEstructurasUser', user).then(function (r) {
            $scope.model = r.data;
            console.log($scope.model);
        });
    }

    $scope.registrarDef = function() {


        if(!existDefecto($scope.nombre)){

            var user = {
                Nombre: $scope.nombre,
                TipoDefecto: $scope.type,
                LimitInf: $scope.LimitInf,
                LimitSup: $scope.LimitSup,
                Descripcion: $scope.Descripcion,
                Usuario: iduser,
                Estructura: $scope.estructura
            };
            console.log(user);

            $http.post('http://localhost/apiPhp/V1/defectos/registro', user).then(function (r) {
                $location.url("/defectos/"+ $rootScope.globals.usuario.id);

                if(r.data.estado == 1){
                    alert(r.data.mensaje);

                }else{
                    console.log("Error");
                    alert(r.data.mensaje);
                }
                //$location.url("/defectos/"+ $rootScope.globals.usuario.id);
            });
            //console.log("/defectos/" + iduser);

        }else{
            alert("El defecto que desea registrar ya existe en la base de datos.");
        }
    };

    function existDefecto($nom){

        var data = {
            NomEstructura: $nom
        };

        $http.post('http://localhost/apiPhp/V1/defectos/obtenerDefectoNom', data).then(function (r) {
            if(r.data.estado == 0){
                return true;
            }else{
                return false;
            }
        })
    }



    $scope.cambio = function () {
        if(angular.equals($scope.type, "Entre")){

            $scope.rangs = true;
            $scope.rangi = true;

        }else if(angular.equals($scope.type, "Por Debajo")){
            $scope.rangs = false;
            $scope.rangi = true;

        }else if(angular.equals($scope.type, "Por Encima")){
            $scope.rangs = true;
            $scope.rangi = false;
        }
    };


    $scope.logout = function()
    {
        auth.logout();
    }

}]);

empleadoControllers.controller('modificarDefectoCtrl', ['$scope','$routeParams', '$http', 'auth', '$location', function ($scope, $routeParams, $http, auth, $location) {

    var id = angular.toJson($routeParams);
    $scope.rangs = true;
    $scope.rangi = true;
    console.log(id);
    modDefec(id);
    var defect;

    function modDefec($idDefecto){


        $http.post('http://localhost/apiPhp/V1/defectos/obtenerDefectoId', $idDefecto).then(function (r) {
            console.log(r.data);
            defect = r.data.datos;
            $scope.Nombre = r.data.datos.Nombre;
            $scope.type = r.data.datos.TipoDefecto;
            $scope.LimitInf = parseInt(r.data.datos.LimitInf);
            $scope.LimitSup = parseInt(r.data.datos.LimitSup);
            $scope.Descripcion= r.data.datos.Descripcion;
            $scope.idDefecto = r.data.datos.idDefecto;
            $scope.cambio();
        })
    }

    $scope.modificar = function (id) {


        var v = 'http://localhost/apiPhp/V1/defectos/' + id;
        console.log(v);


        if(confirm('Esta seguro de modificar este defecto?')){
            var defecto = {
                Nombre: $scope.Nombre,
                TipoDefecto: $scope.type,
                LimitInf: $scope.LimitInf,
                LimitSup: $scope.LimitSup,
                Descripcion: $scope.Descripcion
            };

            console.log(defecto);


            $http.put(v, defecto).then(function (r) {
                if (r.data.estado == 1) {
                    alert(r.data.mensaje);
                    $location.url("/defectos/" + id);
                } else {
                    alert(r.data.estado + ": " + r.data.mensaje);
                }
            })
        }
    };

    $scope.cambio = function () {
        if(angular.equals($scope.type, "Entre")){

            $scope.rangs = true;
            $scope.rangi = true;

        }else if(angular.equals($scope.type, "Por Encima")){
            $scope.rangs = true;
            $scope.rangi = false;

        }else if(angular.equals($scope.type, "Por Debajo")){
            $scope.rangs = false;
            $scope.rangi = true;
        }
    };

    $scope.logout = function()
    {
        auth.logout();
    }

}]);

empleadoControllers.controller('userelecdefectoCtrl', ['$scope', '$routeParams', '$http', 'auth', '$rootScope', function ($scope, $routeParams,  $http, auth, $rootScope) {


    $scope.idEstruc = $routeParams;
    console.log($scope.idEstruc.idEstructura);
    listadoDefectosUser($rootScope.globals.usuario.id, $scope.idEstruc.idEstructura);
    $rootScope.globals.diainicio = new Date();
    $rootScope.globals.diafin = new Date();
    $rootScope.globals.diainicio.setDate($rootScope.globals.diainicio.getDate()-1);
    console.log($rootScope.globals.diainicio);
    console.log($rootScope.globals.diafin);

    $scope.dateOptions = {
        dateDisabled: disabled,
        formatYear: 'yy',
        maxDate: new Date(2020, 5, 22),
        minDate: new Date(2000,1,1),
        startingDay: 1

    };

    function disabled(data) {
        var date = data.date,
            mode = data.mode;
        return mode === 'day' && (date.getDay() === 0 || date.getDay() === 6);
    }

    $scope.open2 = function() {
        $scope.popup2.opened = true;
    };

    $scope.popup2 = {
        opened: false
    };

    $scope.open1 = function() {
        $scope.popup1.opened = true;
    };

    $scope.popup1 = {
        opened: false
    };

    function listadoDefectosUser($idUser, $idEstructur ) {

        var user = {
            idUsuario: $idUser,
            idEstructura: $idEstructur
        };


        $http.post('http://localhost/apiPhp/V1/defectos/obtenerDefectoEstruc', user).then(function (r) {
            $scope.model = r.data;
            console.log($scope.model);
        });
    }




    $scope.logout = function()
    {
        auth.logout();
    }

}]);

empleadoControllers.controller('analisisCtrl', ['$scope','$routeParams', '$http', 'auth', '$location', '$rootScope', function ($scope, $routeParams, $http, auth, $location, $rootScope) {

    var id = $routeParams.idEstructura;
    var idDef = $routeParams.idDefecto;
    var date = new Date();

    $scope.numdefectos = 0;
    console.log(id);
    console.log(idDef);
    datosEstructura(id);

    $scope.export = function() {

        html2canvas(document.getElementById('graficaY'), {
            onrendered: function (canvas) {
                var data = canvas.toDataURL();
                var docDefinition = {
                    footer: {
                        columns: [
                            'Creado por ' + $rootScope.globals.usuario.Nombre + ' ' + $rootScope.globals.usuario.Apellido,
                            { text: date.getDate() +'/' +  (date.getMonth()+1) + '/' + date.getFullYear() , alignment: 'right', paddingRight:10 }
                        ]
                    },
                    content: [
                        {
                           text: $scope.estructura.datos.Nombre + '\n\n',
                           style: 'header',
                           decoration: 'underline',
                           alignment: 'center'
                        },
                        {
                            columns: [
                                {
                                    bold: true,
                                    fontsize: 15,
                                    width: 90,
                                    text:'Dirección: \n Defecto: \n Encontrados: \n\n'
                                },
                                {
                                    text: $scope.estructura.datos.Direccion + '\n' + $scope.defect.datos.Nombre + '\n' + $scope.numdefectos + '\n\n\n'
                                }
                            ]
                        },
                        {
                            text: "Grafica\n",
                            style:'header',
                            alignment: 'center'
                        },
                        {
                        image: data,
                        width: 500
                        }
                    ],
                    styles: {
                        header: {
                            fontSize: 30,
                            bold: true

                        }
                    }
                };
                pdfMake.createPdf(docDefinition).download($scope.estructura.datos.Nombre + ".pdf");
            }
        });
    };

    /*console.log(estructura);
    console.log(defect);
    marca = analisis(defect, estructura);
    grafica(marca, estructura);*/

    function grafica($marca, $estructura){

        var n = $estructura.Vibraciones;

        var ejey = [];

        var labels = [];

        for(var i = 0; i < n.length; i++){
            ejey[i] = n[i].Y;
            labels[i] = "" + n[i].Fecha + " " + n[i].Hora;

        }

        console.log($marca.length);
        console.log(ejey.length);

        var datasY = {
            labels: labels,
            datasets: [
                {
                    label: "EjeX",
                    fill: false,
                    lineTension: 0.1,
                    backgroundColor: "#4bc000",
                    borderColor: "#bec022",
                    borderCapStyle: 'butt',
                    borderDash: [],
                    borderDashOffset: 0.0,
                    borderJoinStyle: 'miter',
                    pointBorderColor: "#c00a14",
                    pointBackgroundColor: "#fff",
                    pointBorderWidth: 1,
                    pointHoverRadius: 0,
                    pointHoverBackgroundColor: "#224bc0",
                    pointHoverBorderColor: "#dcdc01",
                    pointHoverBorderWidth: 2,
                    pointRadius: $marca,
                    pointHitRadius: 10,
                    data: ejey,
                    spanGaps: false
                }
            ]
        };
        var ctx = "EjeY";
        var myLineChartY = new Chart(ctx, {
            type: 'line',
            data: datasY
        });



    }

    function defectodatos($idDefecto) {
        var user = {
            idDefecto: $idDefecto
        };

        $http.post('http://localhost/apiPhp/V1/defectos/obtenerDefectoId', user).then(function (r) {
            $scope.defect = r.data;
            console.log($scope.defect);
            var marca = analisis($scope.defect.datos, $scope.estructura.datos);
            grafica(marca, $scope.estructura.datos);
        });
    }

    function datosEstructura($idEstructura){

        $mesIni = $rootScope.globals.diainicio.getMonth()+1;
        $mesfin = $rootScope.globals.diafin.getMonth()+1

        var user = {
            idEstructura: $idEstructura,
            inicio: $rootScope.globals.diafin.getFullYear()+"-"+$mesfin+"-0"+$rootScope.globals.diafin.getDate(),
            fin: $rootScope.globals.diainicio.getFullYear()+"-"+ $mesIni+"-0"+ $rootScope.globals.diainicio.getDate()
            //fin: $rootScope.globals.diafin.getFullYear()+"-"+$mesfin+"-0"+$rootScope.globals.diafin.getDate()
        };

        console.log(user);

        $http.post('http://localhost/apiPhp/V1/estructuras/obtenerEstruc', user).then(function (p) {
            console.log(p.data);
            $scope.estructura = p.data;
            defectodatos(idDef);
        });
    }

    function analisis($defect, $estructura) {

        var tipo = $defect.TipoDefecto;
        console.log($estructura);
        var vib = $estructura.Vibraciones;
        var marca;
        console.log(tipo);
        console.log(vib);

        if(angular.equals(tipo, "Entre")){
            marca = analisisentre($defect.LimitInf, $defect.LimitSup, vib);
        }else if(angular.equals(tipo, "Por Encima")){
            marca =analisisencima($defect.LimitSup, vib);
        }else if(angular.equals(tipo, "Por Debajo")){
            marca = analisisdebajo($defect.LimitInf, vib);
        }
        console.log(marca);
        return marca;

    }
    
    function analisisentre($limInf, $limSup, $vib) {

        var marcas = [];
        console.log($vib[1]);
        console.log($vib.length);

        for(var i= 0 ; i<$vib.length; i++){

            if(parseInt($vib[i].Y)<$limSup && parseInt($vib[i].Y)>$limInf){
                $scope.numdefectos= $scope.numdefectos+1;
                marcas[i] = 2;
            }else{
                marcas[i] = 0;
            }
        }
        console.log(marcas);
        return marcas;
        
    }
    
    function analisisencima($limSup, $vib){
        var marcas = [];


        for(var i= 0 ; i<$vib.length; i++){

            if(parseInt($vib[i].Y)>parseInt($limSup)){
                $scope.numdefectos= $scope.numdefectos+1;
                marcas[i] = 2;
            }else{
                marcas[i] = 0;
            }
        }
        return marcas;
    }
    
    function analisisdebajo($limInf, $vib) {

        var marcas = [];

        for(var i= 0 ; i<$vib.length; i++){

            if( parseInt($vib[i].Y)<$limInf){
                $scope.numdefectos= $scope.numdefectos+1;
                marcas[i] =2;
            }else{
                marcas[i] = 0;
            }
        }
        return marcas;
    }




    $scope.logout = function()
    {
        auth.logout();
    }

}]);
