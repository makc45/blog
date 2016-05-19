<div class="Blogs">
<?php if(Yii::$app->user->isGuest){?>

Чтобы добавлять записи, авторизуйтесь на сайте.
<?php } else {?>

<button ng-click="BlogAdd()">Добавить запись</button>
<button ng-click="BlogMyView()">{{BlogMyText}}</button>
<div class="addBlog"  ng-show="BlogViewAdd">
    <form name="BlogAddForm">
        <button ng-click="BlogSave(BlogMonth,BlogZag,BlogText)">Сохранить</button>
        <button ng-click="BlogSaveClosed(BlogZag,BlogText)">Отмена</button>
        <span class="errorColor">{{BlogErorr}}</span><br> 
    <fieldset>
        Выберите месяц: 
        <select class="BlogAddVvod" ng-model="BlogMonth" style="width: auto;">
            <option ng-repeat="Month in Months" value="{{Month.value}}">{{Month.name}}</option>
        </select>   <br>
        <input class="BlogAddVvod" ng-model="BlogZag" placeholder = "Заголовок"/><br>
        <textarea class="BlogAddVvod" autocomplete="off" ng-model="BlogText" placeholder = "Текст"></textarea><br>  
  
    </fieldset>
        Теги: <span class="TagBlock" ng-repeat="Tag in BlogNewTags" >{{Tag.name}} 
            <button ng-click="BlogDelTag(Tag)">Удалить</button></span><br>
        Добавьте новый тег<br>
        <input ng-model="BlogAddNewTag"/> <button ng-click="BlogSaveNewTag(BlogAddNewTag)">Добавить</button><span class="errorColor"> {{ErrorTag}}</span><br> <br>
        или выберете из уже имеющихся<br>
        
        <span class="TagBlock" ng-repeat="Tag in Tags | filter:filterNotInCurrent">{{Tag.name}} 
            <button ng-click="BlogAddTag(Tag)">Добавить</button></span>
      
    </form>
</div>






<div>
        <modal-dialog show='modalBlogSaveClosed' width='350px' height='150px'>
            <p>Все внесенные данные будут потеряны!<br> Продолжить?<p><br><br>
            <button ng-click="BlogSaveClosedYes()">Да</button>
            <button ng-click="BlogSaveClosedNo()">Нет</button>
        </modal-dialog>
</div>
<?php } 
?>
<div class="BlogsContent">
    <?php 
    
    foreach($blog as $blog_el)
        {?><table <?php if((!Yii::$app->user->isGuest)&&(Yii::$app->user->identity->id!=$blog_el['id_user']))echo 'ng-hide="myBlog"'; ?>>
            <tr><td><h4>Месяц <?=$month[$blog_el['month']]?></h4></td><td class="avtor">Автор <?=$users[$blog_el['id_user']]?></td></tr>
        <tr><td><h3><?=$blog_el['header']?></h3></td></tr>
        <tr><td><p><?=$blog_el['text']?></td></tr>
        <tr><td>Оценка (<?php
        $ratingCount=0;
         if (array_key_exists($blog_el['id'], $Rating)) 
            {
                echo  round($Rating[$blog_el['id']]['summa']/$Rating[$blog_el['id']]['count'],1);
                $ratingCount=$Rating[$blog_el['id']]['count'];
            }
         else{ echo "-";}
        ?>) Голосов <?=$ratingCount?>: 
                <button ng-click="BlogRating(1,<?=$blog_el['id']?>)">1</button>
                <button ng-click="BlogRating(2,<?=$blog_el['id']?>)">2</button>
                <button ng-click="BlogRating(3,<?=$blog_el['id']?>)">3</button>
                <button ng-click="BlogRating(4,<?=$blog_el['id']?>)">4</button>
                <button ng-click="BlogRating(5,<?=$blog_el['id']?>)">5</button>
                <span class="errorColor">{{errorRating}}</span>
            </td></tr>
        
                <?php 
                
                if (array_key_exists($blog_el['id'], $BlogTag)) {
                    ?><tr class="tags"><td> <h3>Теги:</h3><?php 
                    foreach ($BlogTag[$blog_el['id']] as $BlogTagEl)
                        {?>                            
                            <?=$tags[$BlogTagEl]?>    
                        <?php }
                        ?> </td></tr> <?php 
                }
                if(!Yii::$app->user->isGuest){?>            
                    <tr class="BlogDel"><td>
                <button ng-click="BlogDel(<?=$blog_el['id']?>)">Удалить запись</button>
            </td></tr>
            
                <?php } 
                
                
                        if (array_key_exists($blog_el['id'], $Comments)) {
                            ?><tr><td><h3>Комментарии:</h3></td></tr><?php 
                    foreach ($Comments[$blog_el['id']] as $CommentsEl)
                        {?>         
                            <tr class="zagComm"><td><h3><?php echo $CommentsEl['id_user']==-1? "Гость":$users[$CommentsEl['id_user']]; ?></h3></td></tr>
                        <tr><td><?=$CommentsEl['text_comment']?></td></tr>
                        <?php if(!Yii::$app->user->isGuest){?>  
                        <tr><td><button ng-click="CommentDel(<?=$CommentsEl['id']?>)">Удалить комментарий</button></td></tr>
                        <?php } }
                        
                }
                ?>
            
            
            
            <tr class="botTab"><td ng-controller="MessageCtrl"><button ng-click="MessegeOpenView()">Оставить отзыв</button>&nbsp;&nbsp; {{MessageSendError}}
                <div ng-show="MessegeView">
                <textarea class="BlogAddVvod" autocomplete="off" ng-model="MessageText" placeholder = "Введите отзыв"></textarea><br>  
                <button ng-click="MessegeSend(MessageText,<?=$blog_el['id']?>)">Отправить</button>
                <span class="errorColor">{{MessageError}}</span>
                </div>
            </td></tr></table>
            <?php } ?>  
    
</div>

</div>
<div class="BlogFilters"><h3>Фильтры</h3>
    <div class="filterMonth"> Выберите месяц <br>{{monthActiv}}
        <div class="filterEl">
            <button ng-repeat="monthF in FiltrActivMonth" ng-click="filterDelMonth(monthF)">{{monthF.name}} </button>
        </div>
        <button ng-repeat="Month in Months| filter:filterNotInMonth" value="{{Month.value}}" ng-click="filterAddMonth(Month)">{{Month.name}}</button> 
    </div>
    <div class="filterTeg"> Выберите тег <br>{{tegActiv}}<br>
        <div class="filterEl">
            <button ng-repeat="Tag in FilterActivTag" ng-click="filterDelTag(Tag)">{{Tag.name}}</button>
        </div>
        <button  ng-repeat="Tag in Tags | filter:filterNotInTags" ng-click="filterAddTag(Tag)">{{Tag.name}}</button>
    </div>
</div>
