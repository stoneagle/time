<?php

use yii\db\Schema;
use yii\db\Migration;

class m170412_105305_entity_life_table extends Migration
{
    public function up()
    {
        $this->createTable('{{%entity_life}}', [
            'id'      => Schema::TYPE_PK,
            'name'    => Schema::TYPE_STRING.' NOT NULL DEFAULT "" COMMENT "名称" ',
            'desc'    => Schema::TYPE_STRING.' NOT NULL DEFAULT "" COMMENT "描述" ',
            'ctime'   => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "创建时间"',
            'utime'   => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "更新时间"',
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%entity_life}}');
    }
}
