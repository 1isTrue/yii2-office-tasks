<?php

namespace app\controllers;

use Yii;
use app\models\Comment;
use yii\web\Controller;
use yii\web\Response;

class CommentController extends Controller
{
    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Comment();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return [
                    'success' => true,
                    'data' => $model,
                    'html' => $this->renderPartial('/task/_comment', ['model' => $model]),
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Ошибка при добавлении комментария.',
                    'errors' => $model->getErrors(), // Возвращаем ошибки валидации
                ];
            }
        }

        return [
            'success' => false,
            'message' => 'Ошибка при добавлении комментария.',
        ];
    }
}
