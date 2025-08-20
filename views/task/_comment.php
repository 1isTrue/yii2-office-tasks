<?php
use yii\helpers\Html;

/* @var $model app\models\Comment */
?>

<div class="comment">
    <strong><?= Html::encode($model->content) ?></strong><br>
    <small>Создано: <?= Html::encode($model->created_at) ?></small>
</div>