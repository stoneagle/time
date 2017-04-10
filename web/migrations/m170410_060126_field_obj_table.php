<?php

use yii\db\Schema;
use yii\db\Migration;

class m170410_060126_field_obj_table extends Migration
{
    public function up()
    {
        $this->createTable('{{%field_obj}}', [
            'id'         => Schema::TYPE_PK,
            'user_id'    => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "用户ID"',
            'field_id'   => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0 COMMENT "领域ID"',
            'ctime'      => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "创建时间"',
            'utime'      => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "更新时间"',
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%field_obj}}');
    }
}
