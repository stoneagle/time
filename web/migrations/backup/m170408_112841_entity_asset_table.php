<?php

use yii\db\Schema;
use yii\db\Migration;

class m170408_112841_entity_asset_table extends Migration
{
    public function up()
    {
        $this->createTable('{{%entity_asset}}', [
            'id'              => Schema::TYPE_PK,
            'name'            => Schema::TYPE_STRING.' NOT NULL DEFAULT "" COMMENT "名称" ',
            'desc'            => Schema::TYPE_STRING.' NOT NULL DEFAULT "" COMMENT "描述" ',
            'year'            => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0 COMMENT "年份" ',
            'area_id'         => Schema::TYPE_INTEGER.' NOT NULL DEFAULT 0 COMMENT "隶属领域" ',
            'status'          => Schema::TYPE_SMALLINT.' NOT NULL DEFAULT 0 COMMENT "公共,商业,私人" ',
            /* 'organization_id' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "所属组织" ', */
            /* 'owner_id'        => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0 COMMENT "负责人" ', */
            'ctime'           => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "创建时间"',
            'utime'           => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "更新时间"',
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%entity_asset}}');
    }
}
