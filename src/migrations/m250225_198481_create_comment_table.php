<?php
use yii\db\Migration;

class m250225_198481_create_comment_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'ENGINE=InnoDB CHARSET=utf8mb4 COMMENT="评论表"';
        }

        $this->createTable('{{%comment}}', [
            'id' => $this->primaryKey()->unsigned()->comment('主键ID'),
            'recipe_id' => $this->integer(11)->notNull()->defaultValue(0)->comment('食谱（文章）表ID'),
            'comment_content' => $this->string(1000)->notNull()->comment('评论内容'),
            'user_id' => $this->integer(11)->notNull()->defaultValue(0)->comment('用户ID'),
            'username' => $this->string(100)->notNull()->defaultValue('')->comment('用户名'),
            'created_at' => $this->timestamp()->defaultValue(null)->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
        ], $tableOptions);

        //$this->execute("");
    }

    public function down()
    {
        $this->dropTable('{{%comment}}');
    }
}