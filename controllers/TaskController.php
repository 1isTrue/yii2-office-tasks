<?php

namespace app\controllers;

use Yii;
use app\models\Task;
use yii\web\Controller;
use yii\data\ActiveDataProvider;

class TaskController extends Controller
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Task::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new Task();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);

        $dataProvider = new ActiveDataProvider([
            'query' => $model->getComments(),
            'pagination' => [
                'pageSize' => 3, // Количество комментариев на странице
            ],
        ]);

        return $this->render('view', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Task::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        if (!Yii::$app->request->validateCsrfToken(Yii::$app->request->get('token'))) {
            throw new \yii\web\BadRequestHttpException('Invalid CSRF token.');
        }

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionUpdateStatus()
    {
        $id = Yii::$app->request->post('id');
        $status = Yii::$app->request->post('status');

        $model = Task::findOne($id);
        if ($model) {
            $model->status = $status;
            if ($model->save()) {
                return $this->asJson(['success' => true]);
            }
        }

        return $this->asJson(['success' => false]);
    }
}
