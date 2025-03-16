<?php
use yii\db\Migration;

class m240225_130000_create_recipe_collect_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'ENGINE=InnoDB CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT="收藏表"';
        }

        $this->createTable('{{%recipe_collect}}', [
            'id' => $this->primaryKey()->unsigned()->comment('主键ID'),
            'user_id' => $this->integer(11)->notNull()->defaultValue(0)->comment('用户ID'),
            'recipe_id' => $this->integer(11)->notNull()->defaultValue(0)->comment('食谱（文章）表ID'),
            'action_type' => $this->smallInteger(1)->notNull()->defaultValue(1)->comment('操作类型：1收藏，2点赞'),
            'created_at' => $this->timestamp()->defaultValue(null)->comment('收藏时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
        ], $tableOptions);

        // 添加索引
        $this->createIndex('idx-recipe_collect-user_id', '{{%recipe_collect}}', 'user_id');
        $this->createIndex('idx-recipe_collect-recipe_id', '{{%recipe_collect}}', 'recipe_id');
    }

    public function down()
    {
        $this->dropTable('{{%recipe_collect}}');
    }
}
