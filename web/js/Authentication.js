siteApp.controller('Authentication',
    function Authentication($scope, $http,$rootScope){
//Выводим при загрузке страницы состояние пользователя
    $http.get('/web/index.php?r=site/viewauth')
        .success(function(response) {
            $scope.AuthenticationBlock =response;
        }, 
        function(response) {// failed
            console.log(response);
        });
//Кнопка для показа формы авторизации
    $scope.vhod = function () {
        $scope.vhodViwe=!$scope.vhodViwe;
        $scope.regViwe=false;
        $scope.loginErorr="";
    };
//Кнопка для показа формы регистрации
    $scope.reg = function () {
        $scope.regViwe=!$scope.regViwe;
        $scope.vhodViwe=false;
        $scope.regErorr="";
    };
//Кнопка проверки логин пароля и входа(обновление состояния пользователя)
    $scope.loginPush=function(login,pass){
        $http.post('/web/index.php?r=site/login',
        {_csrf: yii.getCsrfToken(),LoginForm:{username:login,password:pass,rememberMe:1}})
        .success(function(response) {
            if(response.error==0)
                {
                    $scope.AuthenticationBlock =response.text;            
                    blogR();
                }
            else{
                $scope.loginErorr=response.errorMess;
            }
        }, 
        function(response) {// failed
            console.log(response);
        });
    };
//Выход авторизованного пользователя (обновление состояния пользователя)    
    $scope.exit=function(){
        $http.post('/web/index.php?r=site/logout',
        {_csrf: yii.getCsrfToken()})
        .success(function(response) {
            $scope.AuthenticationBlock =response;
            $scope.vhodViwe=false;
            $scope.regViwe=false;
            $scope.loginErorr="";
            $scope.login="";
            $scope.pass="";
            blogR();
        }, 
        function(response) {// failed
            console.log(response);
        });
    };
    function blogR(){
        $http.get('/web/index.php?r=site/blog')
            .success(function(response) {
                $rootScope.BlogBlock =response.text;
            }, 
            function(response) {// failed
                console.log(response);
            });}
//Кнопка Регистрации 
    $scope.registrPush=function(login,pass){
        var http=1;
        var errorMess="";
        if($scope.loginR==""||$scope.loginR==undefined){errorMess+=" Введите логин";http=0;}
        if($scope.passR==""||$scope.passR==undefined){errorMess+=" Введите пароль";http=0;}
        if($scope.passR!=$scope.pass2R){errorMess+="Пароли не совпадают";http=0;}
        if(http){
            $http.post('/web/index.php?r=site/user',
            {_csrf: yii.getCsrfToken(),username:login,password:pass})
            .success(function(response) {console.log(response);
                if(response.error==0)
                    {           
                        $scope.vhodViwe=true;
                        $scope.regViwe=false;
                        $scope.login=login;
                        $scope.pass=pass;
                        $scope.loginR="";
                        $scope.passR="";
                        $scope.pass2R="";
                        $scope.loginErorr="Вы зарегистрировались";
                    }
                else{
                    $scope.regErorr=response.errorMess;
                }
            }, 
            function(response) {// failed
                console.log(response);
            });
        }
        else{$scope.regErorr=errorMess;}
    };
});