<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Создать задачу';
$this->params['breadcrumbs'][] = ['label' => 'Задачи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="task-create">

        <h1><?= Html::encode($this->title) ?></h1>

        <div class="task-form">

            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

            <div class="form-group">
                <label>Загрузить фотографии (до 5 штук, JPG/PNG, до 2MB)</label>
                <input type="file" id="image-upload" name="images[]" multiple accept="image/jpeg, image/png" />
                <div id="image-preview" style="margin-top: 10px;"></div>
                <div id="loader" style="display:none;">Загрузка...</div>
            </div>

            <input type="hidden" id="uploaded-image-ids" name="uploaded_image_ids" value="">

            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>

    </div>

<?php
$script = <<< JS
$.ajaxSetup({
    headers: {
        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
    }
});
$(document).ready(function() {
    var isUploading = false;

    $('#image-upload').on('change', function() {
        var files = this.files;
        var preview = $('#image-preview');
        var loader = $('#loader');
        preview.empty();
        $('#uploaded-image-ids').val('');

        if (files.length > 5) {
            alert('Вы можете загрузить не более 5 изображений.');
            $(this).val('');
            return;
        }

        var formData = new FormData();
        for (var i = 0; i < files.length; i++) {
            if (files[i].size > 2 * 1024 * 1024) {
                alert('Файл ' + files[i].name + ' превышает 2MB.');
                continue;
            }
            if (!['image/jpeg', 'image/png'].includes(files[i].type)) {
                alert('Файл ' + files[i].name + ' имеет недопустимый формат. Допустимы только JPG и PNG.');
                continue;
            }
            formData.append('images[]', files[i]);
        }

        loader.show();
        isUploading = true;

        $.ajax({
            url: 'task/imagesUpload',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                loader.hide();
                isUploading = false;
                if (response.success) {
                    response.imageIds.forEach(function(imageId) {
                        var img = $('<img>').attr('src', response.imageUrls[imageId]).css({
                            'width': '100px',
                            'height': 'auto',
                            'margin': '5px'
                        });
                        preview.append(img);
                    });
                    $('#uploaded-image-ids').val(response.imageIds.join(','));
                } else {
                    alert('Ошибка при загрузке изображений: ' + response.message);
                }
            },
            error: function() {
                loader.hide();
                isUploading = false;
                alert('Ошибка при загрузке изображений');
            }
        });
    });

    $('form').on('submit', function(e) {
        if (isUploading) {
            e.preventDefault();
            alert('Дождитесь полной загрузки изображений');
        }
    });
});
JS;
$this->registerJs($script);
?>