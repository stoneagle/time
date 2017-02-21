<?php

namespace app\controllers\frontend;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;

class BaseController extends Controller
{
    public function beforeAction($action)
    {
        //登录模块
        return true;
    }

    public function echoJson($data, $error = 0, $msg = '', $content = '')
    {
        header("HTTP/1.1 200 OK");
        header('Content-type: text/json; charset=utf-8');
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        if($content) {
            return \Yii::createObject([
                'class' => 'yii\web\Response',
                'format' => Response::FORMAT_RAW,
                'data' => $content
            ]);
        }
        return \Yii::createObject([
            'class' => 'yii\web\Response',
            'format' => Response::FORMAT_JSON,
            'data' => [
                'error' => $error,
                'message' => $msg,
                'data' => $data,
            ],
        ]);
    }
}
