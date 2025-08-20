<?php
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Задачи';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="task-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('+ Новая задача', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'id',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model->id, ['view', 'id' => $model->id], ['class' => 'link']);
                },
            ],
            [
                'attribute' => 'title',
                'header' => 'Название',
            ],
            [
                'attribute' => 'status',
                'header' => 'Статус',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::dropDownList('status', $model->status, [
                        'new' => 'Новая',
                        'in_progress' => 'В процессе',
                        'done' => 'Завершена',
                    ], [
                        'class' => 'form-control status-dropdown',
                        'data-id' => $model->id, // Сохраняем ID задачи в атрибуте data-id
                    ]);
                },
            ],
            [
                'attribute' => 'created_at',
                'header' => 'Дата создания', // Заголовок колонки
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Действия', // Заголовок колонки
                'template' => '{delete}', // Указываем, что хотим только кнопку удаления
                'buttons' => [
                    'delete' => function ($url, $model) {
                        return Html::a('Удалить', $url.'&token='.Yii::$app->request->csrfToken, [
                            'class' => 'btn btn-danger',
                            'data-method' => 'post',
                            'data-confirm' => 'Вы уверены, что хотите удалить эту задачу?',
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>

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
