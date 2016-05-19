<?php if(Yii::$app->user->isGuest){?>

<button ng-click="vhod()">Вход</button> <button ng-click="reg()">Регистрация</button>
        <div ng-show="vhodViwe">
            <div class="AuthBlock">
                <input ng-model="login" placeholder = "Логин" required/><br>
                <input autocomplete="off" type="password" ng-model="pass"  placeholder = "Пароль" required/><br>
                <button ng-click="loginPush(login,pass)">Войти</button> <span class="errorColor">{{loginErorr}}</span>
            </div>
        </div> 
        
        <div ng-show="regViwe">
            <div class="AuthBlock">
                <input ng-model="loginR" placeholder = "Логин" required/><br>
                <input autocomplete="off" type="password" ng-model="passR"  placeholder = "Пароль" required/><br>
                <input autocomplete="off" type="password" ng-model="pass2R" placeholder = "Повторите пароль" required/><br>
                <button ng-click="registrPush(loginR,passR)">Зарегистрироваться</button> <span class="errorColor">{{regErorr}}</span>
            </div>
        </div>
<?php } else {?>
Добро пожаловать <b><?php echo Yii::$app->user->identity->username; ?></b> 
<button ng-click="exit()">Выход</button>

<?php } ?>

