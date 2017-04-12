<?php

use yii\db\Schema;
use yii\db\Migration;

class m170408_065347_country_table extends Migration
{
    public function up()
    {
        $this->createTable('{{%country}}', [
            'id'      => Schema::TYPE_PK,
            'name'    => Schema::TYPE_STRING . ' NOT NULL DEFAULT "" COMMENT "名称" ',
            'en_name' => Schema::TYPE_STRING . ' NOT NULL DEFAULT "" COMMENT "英文名称" ',
            'ctime'   => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "创建时间"',
            'utime'   => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "更新时间"',
        ]);

    }

    public function down()
    {
        $this->dropTable('{{%country}}');
    }
}
