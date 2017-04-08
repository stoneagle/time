<?php

use yii\db\Schema;
use yii\db\Migration;

class m170408_064749_art_work_table extends Migration
{
    public function up()
    {
        $this->createTable('{{%art_work}}', [
            'id'         => Schema::TYPE_PK,
            'name'       => Schema::TYPE_STRING . ' NOT NULL DEFAULT "" COMMENT "名称" ',
            'year'       => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "年代" ',
            'country_id' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "所属国家" ',
            'entity_id'  => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "所属实体" ',
            'ctime'      => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "创建时间"',
            'utime'      => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "更新时间"',
        ]);

    }

    public function down()
    {
        $this->dropTable('{{%art_work}}');
    }
}
