<?php

use yii\db\Schema;
use yii\db\Migration;

class m170410_060126_target_table extends Migration
{
    public function up()
    {
        $this->createTable('{{%target}}', [
            'id'          => Schema::TYPE_PK,
            'name'        => Schema::TYPE_STRING.' NOT NULL DEFAULT "" COMMENT "名称" ',
            'desc'        => Schema::TYPE_STRING.' NOT NULL DEFAULT "" COMMENT "描述" ',
            'user_id'     => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "用户ID"',
            'field_id'    => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0 COMMENT "所属范畴"',
            'priority_id' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0 COMMENT "优先级"',
            'ctime'       => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "创建时间"',
            'utime'       => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "更新时间"',
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%target}}');
    }
}
