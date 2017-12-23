<?php

namespace app\controllers\frontend;

use Yii;
use app\models\Action;
use app\models\Error;
use app\models\Task;
use app\models\Daily;
use app\models\Project;
use app\models\Plan;
use app\models\PlanProject;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

class PlanController extends BaseController
{
    public function actionValid()
    {
        try {
            $model = new Plan();
            return $this->validModel($model, Plan::class);
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionIndex()
    {
        $model = new Plan();
        $model->load(Yii::$app->request->queryParams);

        $data_provider = new ActiveDataProvider([
            'query' => $model->getQuery(),
        ]);
        return $this->render('index', [
            'searchModel'  => $model,
            'dataProvider' => $data_provider,
        ]);
    }

    public function actionCreate()
    {
        $model = new Plan();
        try {
            if (Yii::$app->request->post()) {
                $model->load(Yii::$app->request->post());
                $model->user_id = $this->user_obj->id;
                $model->modelValidSave();
                $code = Error::ERR_OK;
                // 更新plan_id为空的plan_projects
                $conditions = [
                    "and",
                    ['plan_id' => 0],
                    ['user_id' => $this->user_obj->id],
                ];
                $plan_project_model = new PlanProject();
                $plan_project_model->updateAll(["plan_id" => $model->attributes['id']], $conditions);
                return $this->packageJson(['id' => $model->attributes['id']], $code, Error::msg($code));
            } else {
                // 删除plan_id为空的plan_projects
                PlanProject::deleteAll(
                    "plan_id = :plan_id and user_id = :user_id",
                    [":plan_id" => 0, ":user_id" => $this->user_obj->id]
                );
                
                $daily_dict = Daily::getDict();
                $default_daily_key = key($daily_dict);
                $model->from_date = date("Y-m-01", time());
                $model->to_date = date("Y-m-d", strtotime(date('Y-m-01', time()) . ' +1 month'));
                return $this->render('save', [
                    'model'   => $model,
                    'dailyArr'   => $daily_dict,
                    'defaultDailyKey'   => $default_daily_key,
                ]);
            }
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionUpdate()
    {
        try {
            $id = Yii::$app->request->get('id', null);
            $model  = $this->findModel($id, Plan::class);

            if (Yii::$app->request->post()) {
                $model->load(Yii::$app->request->post());
                $model->modelValidSave();
                $code = Error::ERR_OK;
                return $this->packageJson(['id' => $model->attributes['id']], $code, Error::msg($code));
            } else {
                // 删除plan_id为空的plan_projects
                PlanProject::deleteAll(
                    "plan_id = :plan_id and user_id = :user_id",
                    [":plan_id" => 0, ":user_id" => $this->user_obj->id]
                );

                $daily_dict = Daily::getDict();
                return $this->render('save', [
                    'dailyArr' => $daily_dict,
                    'defaultDailyKey'   => $model->daily_id,
                    'model'    => $model,
                    'id'       => $id,
                ]);
            }
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionCheck()
    {
        try {
            $id = Yii::$app->request->get('id', null);
            $model  = $this->findModel($id, Plan::class);
            $daily_dict = Daily::getDict();
            return $this->render('check', [
                'dailyArr' => $daily_dict,
                'defaultDailyKey'   => $model->daily_id,
                'model'    => $model,
                'id'       => $id,
            ]);
        } catch (\Exception $e) {
            return $this->returnException($e);
        }
    }

    public function actionDelete()
    {
        try {
            $ids = Yii::$app->request->post('ids', null);
            if (empty($ids)) {
                throw new \Exception (Error::msg(Error::ERR_PARAMS), Error::ERR_PARAMS);
            }
            // 删除相关的plan_projects
            if (is_array($ids)) {
                foreach ($ids as $tmp_id) {
                    PlanProject::deleteAll(
                        "plan_id = :plan_id",
                        [":plan_id" => $tmp_id]
                    );
                } 
            } else {
                PlanProject::deleteAll(
                    "plan_id = :plan_id",
                    [":plan_id" => $ids]
                );
            }
            $query = Plan::find()->andWhere(['and',['in', 'id', $ids]]);
            foreach ($query->all() as $model) {
                $result = $model->delete();
                if (!$result) {
                    throw new \Exception (Error::msg(Error::ERR_DEL), Error::ERR_DEL);
                }
            }
            $code = Error::ERR_OK;
            return $this->packageJson($ids, $code, Error::msg($code));
        } catch (\Exception $e) {
            return $this->returnexception($e);
        }
    }
    
    // 处理plan-project列表的操作
    public function actionProjectOper($project_id)
    {
        try {
            $params_conf = [
                "hours"   => [null, true],
                "plan_id" => [null, true],
                "select"  => [null, true],
            ];
            $params = $this->getParamsByConf($params_conf, 'post');
            $model = new PlanProject;
            if ($params["select"] == 0) {
                // 撤除某项计划(不能标记delete，前端会删除该栏目)
                // $action_type = "deleted";
                $action_type = "updated";
                $model = $model->find()
                    ->andWhere(["project_id" => $project_id])
                    ->one();
                if (is_null($model)) {
                    throw new \Exception(Error::msg(Error::ERR_MODEL), Error::ERR_MODEL);
                }
                $result      = $model->delete();
                if (!$result) {
                    throw new \Exception(Error::msg(Error::ERR_DEL), Error::ERR_DEL);
                }
            } else {
                // 新增某项计划
                $check = PlanProject::find()
                    ->andWhere(["plan_id" => $params["plan_id"]])
                    ->andWhere(["user_id" => $this->user_obj->id])
                    ->andWhere(["project_id" => $project_id])
                    ->one();
                if (is_null($check)) {
                    $action_type       = "inserted";
                    $model->hours      = $params["hours"];
                    $model->plan_id    = $params["plan_id"];
                    $model->project_id = $project_id;
                    $model->user_id    = $this->user_obj->id;
                    $model->modelValidSave();
                } else {
                    $action_type = "updated";
                    $check->hours = $params["hours"];
                    $check->modelValidSave();
                }
            }

            $ret = $this->prepareResponse($action_type, $project_id);
            return $this->directJson($ret);
        } catch (\exception $e) {
            $action_type = "error";
            $ret         = $this->prepareResponse($action_type, $project_id, $e->getMessage());
            return $this->directJson($ret);
        }
    }

    // 获取Project挑选的列表
    public function actionProjectList()
    {
        $plan_id = Yii::$app->request->get('plan_id', 0);
        if (empty($plan_id)) {
            $plan_id = 0;
        }
        $model = new PlanProject;
        $model->user_id = $this->user_obj->id;
        $model->plan_id = $plan_id;
        $query = $model->getProjectListQuery();
        $result = $query->asArray()->all();
        $ret = [];
        foreach ($result as $one) {
            $ret["rows"][] = [
                "id" => $one['id'],
                "data" => [
                    is_null($one["hours"]) ? 0 : 1,
                    empty($plan_id) ? 0 : $plan_id,
                    $one['text'],
                    is_null($one["hours"]) ? 0 : $one["hours"],
                ]
            ];
        }
        return $this->directJson(json_encode($ret));
    }

    // 获取总结查看的Project列表
    public function actionProjectCheck()
    {
        $plan_id = Yii::$app->request->get('plan_id', 0);
        if (empty($plan_id)) {
            throw new \Exception (Error::msg(Error::ERR_PARAMS), Error::ERR_PARAMS);
        }
        $model  = $this->findModel($plan_id, Plan::class);
        $model->user_id = $this->user_obj->id;
        $query = $model->getCheckListQuery();
        $result = $query->asArray()->all();
        foreach ($result as $one) {
            $check_map[$one["id"]] = $one["sum_time"];
        }

        $model = new PlanProject;
        $model->user_id = $this->user_obj->id;
        $model->plan_id = $plan_id;
        $query = $model->getProjectListQuery(false, true);
        $project_t = Project::tableName();
        $query = $query->andWhere(['and',['in', "$project_t.id", array_keys($check_map)]]);
        $result = $query->asArray()->all();
        $ret = [];

        foreach ($result as $one) {
            $ret["rows"][] = [
                "id" => $one['id'],
                "data" => [
                    $one['text'],
                    is_null($one["hours"]) ? 0 : $one["hours"],
                    $check_map[$one["id"]] / (60 * 60),
                    ($one["progress"] == 1) ? "已完成" : "进行中"
                ]
            ];
        }
        return $this->directJson(json_encode($ret));
    }
}
