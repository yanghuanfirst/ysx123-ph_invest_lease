<?php
use yii\db\Migration;

class m251225_198421_create_recipe_report_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'ENGINE=InnoDB CHARSET=utf8mb4 COMMENT="举报表"';
        }

        $this->createTable('{{%recipe_report}}', [
            'id' => $this->primaryKey()->unsigned()->comment('主键ID'),
            'recipe_id' => $this->integer(11)->notNull()->defaultValue(0)->comment('食谱（文章）表ID'),
            'report_type_id' => $this->integer(11)->notNull()->comment('举报类型ID'),
            'report_content'=> $this->string(1000)->defaultValue('')->comment('举报内容'),
            'user_id' => $this->integer(11)->notNull()->defaultValue(0)->comment('用户ID'),
            'username' => $this->string(100)->notNull()->defaultValue('')->comment('举报人用户名'),
            'created_at' => $this->timestamp()->defaultValue(null)->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
        ], $tableOptions);

        //$this->execute("");
    }

    public function down()
    {
        $this->dropTable('{{%recipe_report}}');
    }
}