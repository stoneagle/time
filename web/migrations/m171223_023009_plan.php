<?php
use yii\db\Schema;
use yii\db\Migration;

class m171223_023009_plan extends Migration
{
    public function up()
    {
        $this->createTable('{{%plan}}', [
            'id'       => Schema::TYPE_PK,
            'from_date'    => Schema::TYPE_STRING . ' NOT NULL DEFAULT "" COMMENT "开始日期" ',
            'to_date'    => Schema::TYPE_STRING . ' NOT NULL DEFAULT "" COMMENT "结束日期" ',
            'daily_id' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "作息模板" ',
            'user_id'  => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "所属用户" ',
            'ctime'    => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "创建时间"',
            'utime'    => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "更新时间"',
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%plan}}');
    }
}
