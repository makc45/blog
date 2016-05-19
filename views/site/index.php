<?php

/* @var $this yii\web\View */

$this->title = 'Главная';
?>

<div class="site-index">
    <div  ng-controller="Authentication">
        <span dynamic="AuthenticationBlock"></span>
    </div>
    <div  ng-controller="Blog" class="BlogBlock">
        <span dynamic="BlogBlock"></span>
    </div>
</div>