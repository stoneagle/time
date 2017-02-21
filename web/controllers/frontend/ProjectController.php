<?php

namespace app\controllers\frontend;

use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;

class ProjectController extends BaseController
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

    public function actionData()
    {
        $test = [
            'data' => [
                'id' => 1,
                "text" => "project",
                "start_date" => "01-04-2013",
                "duration" => 11,
                "progress" => 0.6,
                "open" => true,
            ]
        ];

        return $this->echoJson([], 0, "", json_encode($test));
    }
}
