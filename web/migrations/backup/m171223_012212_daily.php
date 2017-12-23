<?php
use yii\db\Schema;
use yii\db\Migration;

class m171223_012212_daily extends Migration
{
    public function up()
    {
        $this->createTable('{{%daily}}', [
            'id'      => Schema::TYPE_PK,
            'name'    => Schema::TYPE_STRING . ' NOT NULL DEFAULT "" COMMENT "名称" ',
            'user_id'    => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "所属用户" ',
            'ctime'   => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "创建时间"',
            'utime'   => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "更新时间"',
        ]);

    }

    public function down()
    {
        $this->dropTable('{{%daily}}');
    }
}
