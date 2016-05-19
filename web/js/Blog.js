siteApp.controller('Blog',
    function Blog($scope, $http, $rootScope){     
        $scope.Tags=[];
        $scope.BlogNewTags=[];
        function BlogReload(){        
            $http.get('/web/index.php?r=site/blog')
                .success(function(response) {
                    $scope.Tags=response.tags;
                    $rootScope.BlogBlock =response.text;
                }, 
                function(response) {// failed
                    console.log(response);
                });
            }                  
    BlogReload();
//Месяц по умолчанию
    $scope.BlogMonth="1";
//Массив месяцов
    $scope.Months=[ {value: '1', name:  'Январь'},
                    {value: '2', name:  'Февраль'},
                    {value: '3', name:  'Март'},
                    {value: '4', name:  'Апрель'},
                    {value: '5', name:  'Май'},
                    {value: '6', name:  'Июнь'},
                    {value: '7', name:  'Июль'},
                    {value: '8', name:  'Август'},
                    {value: '9', name:  'Сентябрь'},
                    {value: '10',name:  'Октябрь'},
                    {value: '11',name:  'Ноябрь'},
                    {value: '12',name:  'Декабрь'}];
//Фильтр для показа тегов для добавления
    $scope.filterNotInCurrent = function(item) {
        var rez=1;
        $scope.BlogNewTags.forEach(function(el, i, arr) {
            if(item.id==el.id) rez=0;
          });
        return rez;
        };
//Кнопка для формы добавления записи
    $scope.BlogAdd = function () {
        $scope.BlogViewAdd=true;
        $scope.BlogErorr="";
        };
        
    $scope.modalBlogSaveClosed = false;
//кнопка отмены добавления записи
    $scope.BlogSaveClosed = function(BlogZag,BlogText) {
        if((BlogZag==""||BlogZag==undefined)&&(BlogText==""||BlogText==undefined)){
                $scope.BlogViewAdd=false;
            }
            else{$scope.modalBlogSaveClosed = !$scope.modalBlogSaveClosed;}
        };
//Отмена добавления записи    
    $scope.BlogSaveClosedNo = function() {$scope.modalBlogSaveClosed = !$scope.modalBlogSaveClosed;}
//Закрытие формы добавления записи
    $scope.BlogSaveClosedYes = function() {clearClosedSave();};
function clearClosedSave()  
    {
        $scope.BlogNewTags=[];
        $scope.BlogViewAdd=false;
        $scope.BlogMonth="1";
        $scope.BlogErorr=$scope.BlogZag=$scope.BlogText="";
        $scope.modalBlogSaveClosed = false;
    }
//Добавление тега        
    $scope.BlogAddTag = function(el)
        {            
            $scope.BlogNewTags.push(el);           
        };
//Удаление тега        
    $scope.BlogDelTag=function(item){ 
            var index=$scope.BlogNewTags.indexOf(item)
            $scope.BlogNewTags.splice(index,1);     
        };
//Добавление нового тега
    $scope.BlogSaveNewTag =function(name)
        {      
            var rez=1;
            $scope.BlogNewTags.forEach(function(el, i, arr) {
                if(name==el.name) rez=0;
            });
            $scope.Tags.forEach(function(el, i, arr) {
                if(name==el.name) rez=0;
            });
            if(rez){
            $scope.BlogNewTags.push({id:"-1",name:name}); $scope.ErrorTag=""; }
            else {$scope.ErrorTag="Такой тег уже есть!";}
        };
//Сохранение новой записи
    $scope.BlogSave = function (BlogMonth,BlogZag,BlogText){
        var http=1,err="";    
        if(BlogZag==""||BlogZag==undefined){http=0;err+="Введите заголовок";}
        if(BlogText==""||BlogText==undefined){http=0;err+=" Введите текст записи";}                
            if(http){
                //console.log(BlogMonth,BlogZag,BlogText,$scope.BlogNewTags);
                $http.post('/web/index.php?r=site/blog',
                {   month:BlogMonth,
                    header:BlogZag,
                    text:BlogText,
                    tags:$scope.BlogNewTags,
                    _csrf: yii.getCsrfToken()})
                .success(function(response) {
                    if(response.error==0) {
                        BlogReload();
                        clearClosedSave();
                    }
                    else $scope.BlogErorr=response.errorMess;
                }, 
                function(response) {// failed
                    console.log(response);
                });
            }
            $scope.BlogErorr=err;
    };

    $scope.FiltrActivMonth=[];
    $scope.FilterActivTag=[];
//Добавление в фильтер месяцев
    $scope.filterAddMonth = function(el)
        {
            $scope.FiltrActivMonth.push(el); 
            getFilter();
        };
//Удаление из фильтра месяца
    $scope.filterDelMonth=function(item){ 
            var index=$scope.FiltrActivMonth.indexOf(item);
            $scope.FiltrActivMonth.splice(index,1);   
            getFilter();
        };
//Добавление в фильтер Тегов
    $scope.filterAddTag = function(el)
        {
            $scope.FilterActivTag.push(el); 
            getFilter();
        };
//Удаление из фильтра тегов
    $scope.filterDelTag=function(item){ 
            var index=$scope.FilterActivTag.indexOf(item);
            $scope.FilterActivTag.splice(index,1);   
            getFilter();
        };
//фильт доступных месяцев для выбора в фильтр
    $scope.filterNotInMonth = function(item) {
        var rez=1;
        $scope.FiltrActivMonth.forEach(function(el, i, arr) {            
            if(item.value==el.value) rez=0;
          });
        return rez;
        };        
//Фильтр для доступных тегов для выбора в фильтр
    $scope.filterNotInTags = function(item) {
        var rez=1;
        $scope.FilterActivTag.forEach(function(el, i, arr) {
            if(item.id==el.id) rez=0;
          });
        return rez;//($scope.current().indexOf(item) == -1);
        };        
//Показ всех/своих записей
        $scope.BlogMyText="Показывать только мои записи";
        $scope.BlogMyView = function()
            {
                if($scope.myBlog){$scope.BlogMyText="Показывать только мои записи";}
                    else{$scope.BlogMyText="Показывать все записи";}
                $scope.myBlog=!$scope.myBlog;
            };
//Обновление записе в зависимости от фильтра
    function getFilter()
        {
            var url='/web/index.php?r=site/blog';            
            if($scope.FiltrActivMonth.length>0)
                {
                    url+="&month=";
                    $scope.FiltrActivMonth.forEach(function(el, i, arr) {                        
                        if(i>0)url+=",";
                        i++;
                        url+=el.value;
                   });
                }                
            if($scope.FilterActivTag.length>0)
                {
                    url+="&tags=";
                    $scope.FilterActivTag.forEach(function(el, i, arr) {
                        
                        if(i>0)url+=",";
                        i++;
                        url+=el.id;
                   });
                }            
            $http.get(url)
                .success(function(response) {
                    $rootScope.BlogBlock =response.text;
                }, 
                function(response) {// failed
                    console.log(response);
                });
        };
//Удаление записи
    $scope.BlogDel = function(id)
            {
                $http.delete('/web/index.php?r=site/blog',{data:{id:id,_csrf: yii.getCsrfToken()}})
                .success(function(response) {BlogReload();
                        console.log(response);
                }, 
                function(response) {// failed
                        console.log(response);
                });
            };
//Удаление комментария
    $scope.CommentDel = function(id)
        {   
            $http.delete('/web/index.php?r=site/comments',{data:{id:id,_csrf: yii.getCsrfToken()}})
                .success(function() {
                    BlogReload();
                }, 
                function(response) {// failed
                        console.log(response);
                });
        };
//Сохранение новой записи
    $scope.BlogRating = function (rating,id){
        $scope.errorRating="";
                $http.post('/web/index.php?r=site/rating',
                {   rating:rating,
                    id:id,
                    _csrf: yii.getCsrfToken()})
                .success(function(response) {console.log(response);
                    if(response.error==0) {
                        BlogReload();     
                    }
                    else $scope.errorRating=response.errorRating;
                }, 
                function(response) {// failed
                    console.log(response);
                });
    };
});
