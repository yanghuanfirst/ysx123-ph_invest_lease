<?php
use yii\db\Migration;

class m230225_123456_create_address_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'ENGINE=InnoDB CHARSET=utf8mb4 COMMENT="A面-租赁订单表"';
        }

        $this->createTable('{{%recipe_order}}', [
            'id' => $this->primaryKey()->unsigned()->comment('主键ID'),
            'user_id' => $this->integer(11)->notNull()->defaultValue(0)->comment('用户ID'),
            'address_id' => $this->integer(11)->notNull()->defaultValue('')->comment('收货地址表的id--recipe_address'),
            'order_no' => $this->string(50)->notNull()->defaultValue("")->comment('订单号'),
            'order_status' => $this->string(20)->notNull()->defaultValue("")->comment('收货人手机号'),
            'remark' => $this->string(255)->notNull()->defaultValue(1)->comment('备注'),
            'created_at' => $this->timestamp()->defaultValue(null)->comment('创建时间'),
            'updated_at' => $this->timestamp()->defaultValue(null)->comment('更新时间'),
        ], $tableOptions);
        //$this->execute();
    }

    public function down()
    {
        $this->dropTable('{{%recipe_order}}');
    }
}
