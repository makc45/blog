siteApp.controller('MessageCtrl',
    function MessageCtrl($scope, $http, $rootScope){
    $scope.MessegeView=false;
//Открыте формы комментария
    $scope.MessegeOpenView = function()
        {
            $scope.MessegeView=!$scope.MessegeView;
        };
//Сохранение комментария    
    $scope.MessegeSend = function(MessageText,id)
        {
            var http=1;
            err="";
            if(MessageText==""||MessageText==undefined){http=0;err+="Вы ни чего не написали!";}
            $scope.MessageError=err;
            if(http){
                
                $http.post('/web/index.php?r=site/comments',
                {   
                    comments:MessageText,
                    id:id,                    
                    _csrf: yii.getCsrfToken()})
                .success(function(response) {console.log(response);
                    if(response.error==0) {
                        $scope.MessageText="";
                        $scope.MessageSendError="Ваш отзыв отправллен";
                        $scope.MessegeView=false;
                        relBlog();
                    }
                    else $scope.MessageError=response.errorMess;
                }, 
                function(response) {// failed
                    console.log(response);
                });
                
                
            }
        };
function relBlog()
    {
        $http.get('/web/index.php?r=site/blog')
            .success(function(response) {
                $rootScope.BlogBlock =response.text;
            }, 
            function(response) {// failed
                console.log(response);
            });
    }


    });