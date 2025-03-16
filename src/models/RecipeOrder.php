<?php

namespace ysx\recipe\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class RecipeOrder extends ActiveRecord
{
    public function rules() :array
    {
        return [
            // 添加订单验证
            [
                ["remark"], "required", "message" => "Please enter the address", 'on' => 'add_order',
            ],
            [['remark'], 'string',
                'max' => 200,
                "min" => 1,
                'on' => 'add_order',
                'tooLong' => 'The maximum length of the address is 200 characters',
                "tooShort" => 'The minimum length of the address is 1 characters'

            ],
            [
                ["address_id"], "required",
                "message" => "Please select a delivery address",
                'on' => 'add_order',
            ],
            [
                ['address_id'],
                'integer',
                'on' => 'add_order',
                "message" => "Please select a delivery address",
            ],
            [
                ["recipe_id"], "required",
                "message" => "Missing parameter",
                'on' => 'add_order',
            ],
            [
                ['recipe_id'],
                'integer',
                'on' => 'add_order',
                "message" => "Missing parameter",
            ],
            //订单列表
            [['order_status'], 'required',
                'when' => function () {
                    return Yii::$app->request->get('order_type', null) !== null;
                },
                'message' => 'order type cannot be empty.',
                'on' => 'order_list',
            ],
            [['order_status'], 'integer',
                'min' => 0,
                'max'=>4,
                'message' => 'order type Incorrect type parameter',
                'tooSmall' => 'order type number minimum 1',
                'tooBig' => 'order type number minimum 3',
                'on' => 'order_list',
            ],
            //订单详情
            [['id'], 'required',
                'message' => 'order id cannot be empty.',
                'on' => 'order_detail',
            ],
            [['id'], 'integer',
                'min' => 1,
                'message' => 'order id Incorrect type parameter',
                'tooSmall' => 'order id number minimum 1',
                'on' => 'order_detail',
            ],
        ];

    }
    public function scenarios() :array
    {
        $scenarios = parent::scenarios();
        $scenarios['add_order'] = ['address', 'consignee',"consignee_tel","is_default",'recipe_id'];
        $scenarios['order_list'] = ['order_status'];
        $scenarios['order_detail'] = ['id'];
        return $scenarios;
    }

    public function getRecipe(){
        return $this->hasOne(Recipe::class, ['id' => 'recipe_id']);
    }
    public function getAddress(){
        return $this->hasOne(RecipeAddress::class, ['id' => 'address_id']);
    }
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
    }


}
