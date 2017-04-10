<?php

use yii\db\Schema;
use yii\db\Migration;

class m170408_112841_assets_sub_table extends Migration
{
    public function up()
    {
        $this->createTable('{{%assets_sub}}', [
            'id'        => Schema::TYPE_PK,
            'name'      => Schema::TYPE_STRING.' NOT NULL DEFAULT "" COMMENT "名称" ',
            'desc'      => Schema::TYPE_STRING.' NOT NULL DEFAULT "" COMMENT "描述" ',
            'obj_id'    => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0 COMMENT "隶属领域对象ID" ',
            'entity_id' => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0 COMMENT "隶属实体ID" ',
            'ctime'     => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "创建时间"',
            'utime'     => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "更新时间"',
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%assets_sub}}');
    }
}
