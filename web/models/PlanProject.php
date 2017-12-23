<?php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class PlanProject extends BaseActiveRecord
{
    const TABLE_NAME    = "plan_project";

    public static function tableName()
    {
        return self::TABLE_NAME;
    }

    public function rules()
    {
        return [
            [['id', 'project_id', 'plan_id', 'user_id'], 'integer'],
            [['ctime', 'utime'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'project_id' => '目标项目',
            'plan_id'    => '所属计划',
            'user_id'    => '所属用户',
            'hours'      => '投资时间',
            'ctime'      => '创建时间',
            'utime'      => '更新时间',
        ];
    }

    public function getQuery()
    {
        $p_t_t = self::tableName();
        $query = self::find()
            ->orderBy("$p_t_t.ctime");
        $query->andFilterWhere(["$p_t_t.user_id" => $this->user_id]);
        $query->andFilterWhere(["$p_t_t.project_id" => $this->project_id]);
        $query->andFilterWhere(["$p_t_t.plan_id" => $this->plan_id]);
        return $query;
    }

    public function getProjectListQuery($progress=True, $field=True)
    {
        $plan_project_t = PlanProject::tableName();
        $target_t  = Target::tableName();
        $project_t = Project::tableName();
        $query = Project::find() 
            ->select("$project_t.*, $plan_project_t.hours")
            ->leftJoin($plan_project_t, "$project_t.id = $plan_project_t.project_id and $plan_project_t.plan_id = $this->plan_id")
            ->leftJoin($target_t, "$target_t.id = $project_t.target_id")
            ->andFilterWhere(["$project_t.del"      => Constants::SOFT_DEL_NO])
            ->andFilterWhere(["$project_t.user_id"  => $this->user_id])
            ->orderBy("$plan_project_t.hours desc");
        if ($field) {
            $query = $query->andFilterWhere(["NOT", ["$target_t.field_id" => Area::FIELD_GENERAL]]);
        }
        if ($progress) {
            $query = $query->andFilterWhere(["NOT", ["$project_t.progress" => 1]]);
        }
        return $query;
    }
}
