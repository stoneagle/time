<?php

use yii\db\Schema;
use yii\db\Migration;

class m170410_061348_field_obj_entity_link_table extends Migration
{
    public function up()
    {
        $this->createTable('{{%field_obj_entity_link}}', [
            'id'        => Schema::TYPE_PK,
            'obj_id'    => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "领域对象ID"',
            'entity_id' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "相关实体ID"',
            'ctime'     => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "创建时间"',
            'utime'     => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "更新时间"',
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%field_obj_entity_link}}');
    }
}
