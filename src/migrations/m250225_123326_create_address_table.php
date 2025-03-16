<?php
use yii\db\Migration;

class m230225_123456_create_address_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'ENGINE=InnoDB CHARSET=utf8mb4 COMMENT="地址表"';
        }

        $this->createTable('{{%recipe_address}}', [
            'id' => $this->primaryKey()->unsigned()->comment('主键ID'),
            'user_id' => $this->integer(11)->notNull()->defaultValue(0)->comment('用户ID'),
            'address' => $this->string(255)->notNull()->defaultValue('')->comment('收货地址'),
            'consignee' => $this->string(50)->notNull()->defaultValue("")->comment('收货人姓名'),
            'consignee_tel' => $this->string(20)->notNull()->defaultValue("")->comment('收货人手机号'),
            'is_default' => $this->tinyInteger(1)->notNull()->defaultValue(1)->comment('是否是默认地址，2：默认地址，1：非默认地址'),
            'created_at' => $this->timestamp()->defaultValue(null)->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
        ], $tableOptions);
        //$this->execute();
    }

    public function down()
    {
        $this->dropTable('{{%recipe_address}}');
    }
}
