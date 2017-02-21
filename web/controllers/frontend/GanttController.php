<?php

namespace app\controllers\frontend;

use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;

class GanttController extends BaseController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index', [
        ]);
    }
}
