<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Config;
use app\models\AssetsEntity;
use app\models\Error;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class AssetsEntityController extends BaseFieldController
{
    public function actionValid()
    {
        try {
            $model = new AssetsEntity();
            return $this->validModel($model);
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionIndex()
    {
        return $this->baseEntityIndex(AssetsEntity::class);
    }

    public function actionCreate()
    {
        try {
            return $this->baseEntityCreate(AssetsEntity::class);
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionUpdate()
    {
        try {
            return $this->baseEntityUpdate(AssetsEntity::class);
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionDelete()
    {
        try {
            return $this->baseEntityDelete(AssetsEntity::class);
        } catch (\Exception $e) {
            return $this->returnexception($e);
        }
    }
}
