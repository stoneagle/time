<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Error;
use app\models\Constants;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;

class BaseController extends Controller
{
    CONST JSON_PACKAGE = "package";
    CONST JSON_DIRECT  = "direct";

    /**
     * @var string 当前访问的controller
     */
    public $controller;
    public $req;
    public $user_obj;

    public function beforeAction($action)
    {
        $this->req = \Yii::$app->request;
        $this->controller = $action->controller->id;
        //$session = \Yii::$app->session;
        $this->user_obj = Yii::$app->user->identity;

        if (Yii::$app->user->isGuest) {
            $this->redirect("http://".$_SERVER['HTTP_HOST']."/user/login"); 
            return false;
        } 

        //登录模块
        return true;
    }

    public function packageJson($data, $error = 0, $msg = '')
    {
        header("HTTP/1.1 200 OK");
        header('Content-type: text/json; charset=utf-8');
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
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

    public function directJson($content, $type = "application/json")
    {
        header("HTTP/1.1 200 OK");
        header("Content-Type: {$type}");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        return \Yii::createObject([
            'class'  => 'yii\web\Response',
            'format' => Response::FORMAT_RAW,
            'data'   => $content
        ]);
    }

    public function directXml($content)
    {
        return \Yii::createObject([ 
            'class' => 'yii\web\Response', 
            'format' => \yii\web\Response::FORMAT_XML, 
            'formatters' => [ 
                \yii\web\Response::FORMAT_XML => [ 
                    'class' => 'yii\web\XmlResponseFormatter', 
                    'rootTag' => 'data', //根节点 
                    'itemTag' => 'event', //单元 
                ], 
            ], 
            'data' => $content 
        ]);
    }

    /*
     * 根据配置数组读取相关参数
     * @param $conf_arr参数配置数组，index是参数名，数组中0项是默认值，1项为空值校验
     *      eg [
     *          'id' => [1,true],
     *          'name' => [null,false],
     *      ]
     * @param $method 判断是get还是post的flag
     * @return array
     */
    public function getParamsByConf($conf_arr, $method_flag = "get")
    {
        $ret = [];
        foreach ($conf_arr as $name => $one) {
            switch ($method_flag) {
                case "get" :
                    $param = Yii::$app->request->get($name, $one[0]);
                    break;
                case "post" :
                    $param = Yii::$app->request->post($name, $one[0]);
                    break;
            }
            if ($one[1] && is_null($param)) {
                throw new  \Exception("{$name}不符合格式", Error::ERR_PARAMS);
            }
            $ret[$name] = $param;
        }
        return $ret;
    }

    // 传入异常对象，返回错误提醒
    public function returnException($e, $type = self::JSON_PACKAGE)
    {
        $params = [
            'msg'  => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ];
        //Yii::info($this->logFormat("exception", $params), \LOG_CATEGORY::BACKEND_EXCEPTION);
        switch ($type) {
            case self::JSON_PACKAGE :
                return $this->packageJson(
                    [],
                    $e->getCode(), 
                    $e->getMessage());
                break;
            case self::JSON_DIRECT :
                return $this->directJson($e->getMessage);
                break;
            default :
                break;
        }
    }

    public function validModel($model, $class_name = null, $function = null)
    {
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {        
            if (!is_null($model->id)) {
                $model = $this->findModel($model->id, $class_name);
                $model->load(Yii::$app->request->post());
            }
            $result = ActiveForm::validate($model);
            return $this->directJson(json_encode($result), "text/json");
        }
    }

    // model查找
    protected function findModel($id, $class_name)
    {
        if (($model = $class_name::findOne($id)) !== null) {
            return $model;
        } else {
            throw new \Exception("无法找到对象", Error::ERR_MODEL);
        }
    }


    /**
     * 输出大体积的csv类别文件
     * @param $column_name
     * @param $query
     * @param $function
     * @param $file_name
     * @param bool $array_flag
     * @param null $list_function
     */
    public function echoBigCsv($column_name, $query, $function, $file_name, $array_flag = true, $list_function = null)
    {
        $total_export_count = $query->count();
        
        set_time_limit(0);
        header ( "Content-type:application/vnd.ms-excel" );
        header ( "Content-Disposition:filename=" . iconv ( "UTF-8", "GB18030", $file_name ) . ".csv" );
        
        // 打开PHP文件句柄，php://output 表示直接输出到浏览器
        $fp = fopen('php://output', 'a'); 
        
        // 将中文标题转换编码，否则乱码
        foreach ($column_name as $i => $v) {  
            $column_name[$i] = iconv('utf-8', 'GB18030', $v);  
        }
        // 将标题名称通过fputcsv写到文件句柄  
        fputcsv($fp, $column_name);

        $batch_size = 2000;
        for ($i=0; $i < intval($total_export_count/$batch_size)+1; $i++){
            $query->offset($i * $batch_size)->limit($batch_size);
            if ($array_flag) {
                $query->asArray();
            }
            $list = $query->all();

            // 对列表内容的额外操作
            if (!is_null($list_function)) {
                /** @var \Closure $list_function */
                $list = $list_function($list);
            }
            foreach ($list as $item) {
                $rows = $function($item);
                fputcsv($fp, $rows);
            }
            unset($list);
        }
        exit ();
    }
}
