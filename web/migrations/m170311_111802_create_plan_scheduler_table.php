<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Handles the creation of table `plan_scheduler`.
 */
class m170311_111802_create_plan_scheduler_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%plan_scheduler}}', [
            'id'         => Schema::TYPE_PK,
            'week'       => Schema::TYPE_STRING.' NOT NULL DEFAULT "" COMMENT "周计划日期" ',
            'start_date' => Schema::TYPE_DATETIME.' NOT NULL COMMENT "开始时间" ',
            'end_date'   => Schema::TYPE_DATETIME.' NOT NULL COMMENT "结束时间" ',
            'user_id'    => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "所属用户" ',
            'ctime'      => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "创建时间"',
            'utime'      => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "更新时间"',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%plan_scheduler}}');
    }
}
