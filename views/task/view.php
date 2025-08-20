<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ListView;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $model app\models\Task */

$this->title = 'Задача #'.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Задачи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id;
?>
<h1><?= Html::encode($this->title) ?></h1>

<div class="task-details">
    <?php $form = ActiveForm::begin(); ?>
        <strong>Название:</strong> <?= Html::encode($model->title) ?><br>
        <?= $form->field($model, 'status')->dropDownList([
            'new' => 'Новая',
            'in_progress' => 'В процессе',
            'done' => 'Завершена',
        ], [
            'class' => 'status-dropdown',
            'data-id' => $model->id, // Сохраняем ID задачи в атрибуте data-id
        ]) ?>
        <strong>Дата создания:</strong> <?= Html::encode($model->created_at) ?><br>
        <strong>Дата обновления:</strong> <?= Html::encode($model->updated_at) ?><br>
        <strong>Описание:</strong> <?= (strlen($model->description)) ? Html::encode($model->description) : 'Нет описания' ?><br>
    <?php ActiveForm::end(); ?>
</div>



<h2 style="margin-top:30px;">Комментарии</h2>

<textarea id="COMMENT_TEXTAREA" class="form-control"></textarea>
<button class="btn btn-primary" id="ADD_COMMENT">Добавить комментарий</button>

<div id="comments-list" style="margin-top:30px;">
    <?php foreach ($dataProvider->models as $model): ?>
        <?= $this->render('_comment', ['model' => $model]) ?>
    <?php endforeach; ?>
</div>
<?php
$script = <<< JS
$.ajaxSetup({
    headers: {
        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
    }
});
JS;
$this->registerJs($script);


$script = <<< JS
$('.status-dropdown').change(function() {
    var status = $(this).val();
    var id = $(this).data('id');
    
    $.ajax({
        url: 'update-status', // URL для обновления статуса
        type: 'POST',
        data: {id: id, status: status},
        success: function(response) {
            // Обработка успешного ответа, если нужно
            console.log('Статус обновлен');
        },
        error: function() {
            alert('Ошибка при обновлении статуса');
        }
    });
});
JS;
$this->registerJs($script);

$script = <<< JS
$('#ADD_COMMENT').click(function() {
    var content = $('#COMMENT_TEXTAREA').val();
    var taskId = {$model->id};

    $.ajax({
        url: '/comment/create',
        type: 'POST',
        data: {
            Comment: {
                task_id: taskId,
                content: content
            }},
        success: function(response) {
            if (response.success) {
                $('#comments-list').append(response.html);
                $('#COMMENT_TEXTAREA').val('')
            } else {
                alert(response.message);
            }
        },
        error: function() {
            alert('Ошибка при добавлении комментария');
        }
    });
});
JS;
$this->registerJs($script);
?>