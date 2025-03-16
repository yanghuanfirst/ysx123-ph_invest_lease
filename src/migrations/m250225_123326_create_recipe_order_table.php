<?php
use yii\db\Migration;

class m250225_123326_create_recipe_order_table extends Migration
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
            'recipe_id' => $this->integer(11)->notNull()->defaultValue(0)->comment('商品ID'),
            'address_id' => $this->integer(11)->notNull()->defaultValue(0)->comment('收货地址表的id--recipe_address'),
            'order_no' => $this->string(50)->notNull()->defaultValue("")->comment('订单号'),
            'order_status' => $this->tinyInteger(1)->notNull()->defaultValue(1)->comment('订单状态  1：待审核  2：审核通过  3：审核未通过'),
            'remark' => $this->string(255)->notNull()->defaultValue("")->comment('备注'),
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
