<?php

namespace ysx\recipe\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class RecipeAddress extends ActiveRecord
{

    public function rules() :array
    {
        return [
            // 添加地址验证
            [
                ["address"], "required", "message" => "Please enter the address", 'on' => 'add_address',
            ],
            [['address'], 'string',
                'max' => 200,
                "min" => 1,
                'on' => 'add_address',
                'tooLong' => 'The maximum length of the address is 200 characters',
                "tooShort" => 'The minimum length of the address is 1 characters'

            ],
            [
                ["consignee"], "required", "message" => "Please enter the consignee", 'on' => 'add_address',
            ],
            [
                ['consignee'], 'string',
                'max' => 200,
                "min" => 1,
                'on' => 'add_address',
                'tooLong' => 'The maximum length of the consignee is 100 characters',
                "tooShort" => 'The minimum length of the consignee is 1 characters'
            ],
            [
                ["consignee_tel"], "required", "message" => "Please enter the consignee telphone", 'on' => 'add_address',
            ],
            [
                ['consignee_tel'], 'string',
                'max' => 20,
                "min" => 10,
                'on' => 'add_address',
                'tooLong' => 'The maximum length of the consignee telphone is 20 characters',
                "tooShort" => 'The minimum length of the consignee telphone is 10 characters'
            ],
            [
                ["is_default"], "required",
                "message" => "Please select a default shipping address",
                'on' => 'add_address',
            ],
            [
                ['is_default'],
                'integer',
                'min' => 1, 'max' => 2, // 👌 直接限制 1-2
                'on' => 'add_address',
                "message" => "The default shipping address format is incorrect",
                'tooSmall' => 'The maximum length 2',
                "tooBig" => 'The minimum length 1'
            ],

            // 编辑地址验证
            [
                ["id"],"required","message"=>"Missing parameter",'on' => 'edit_address',
            ],
            [
                ["id"],"integer",
                'min' => 1,
                'tooSmall' => 'The minimum length of the param is 1',
                "message"=>"Missing parameter",'on' => 'edit_address',
            ],
            [
                ["address"], "required", "message" => "Please enter the address", 'on' => 'edit_address',
            ],
            [['address'], 'string',
                'max' => 200,
                "min" => 1,
                'on' => 'edit_address',
                'tooLong' => 'The maximum length of the address is 200 characters',
                "tooShort" => 'The minimum length of the address is 1 characters'

            ],
            [
                ["consignee"], "required", "message" => "Please enter the consignee", 'on' => 'edit_address',
            ],
            [
                ['consignee'], 'string',
                'max' => 200,
                "min" => 1,
                'on' => 'edit_address',
                'tooLong' => 'The maximum length of the consignee is 100 characters',
                "tooShort" => 'The minimum length of the consignee is 1 characters'
            ],
            [
                ["consignee_tel"], "required", "message" => "Please enter the consignee telphone", 'on' => 'edit_address',
            ],
            [
                ['consignee_tel'], 'string',
                'max' => 20,
                "min" => 10,
                'on' => 'edit_address',
                'tooLong' => 'The maximum length of the consignee telphone is 20 characters',
                "tooShort" => 'The minimum length of the consignee telphone is 10 characters'
            ],
            [
                ["is_default"], "required", "message" => "Please select a default shipping address", 'on' => 'edit_address',
            ],
            [
                ['is_default'],
                'integer',
                'min' => 1, 'max' => 2, // 👌 直接限制 1-2
                'on' => 'edit_address',
                "message" => "The default shipping address format is incorrect",
                'tooSmall' => 'The maximum length 2',
                "tooBig" => 'The minimum length 1'
            ],
            // 删除地址验证
            [
                ["id"],"required","message"=>"Missing parameter",'on' => 'del_address',
            ],
            [
                ["id"],"integer",
                'min' => 1,
                'tooSmall' => 'The minimum length of the param is 1',
                "message"=>"Missing parameter",'on' => 'del_address',
            ],
        ];

    }
    public function scenarios() :array
    {
        $scenarios = parent::scenarios();
        $scenarios['add_address'] = ['address', 'consignee',"consignee_tel","is_default"];
        $scenarios['edit_address'] = ["id",'address', 'consignee',"consignee_tel","is_default"];
        $scenarios['del_address'] = ['id'];
        return $scenarios;
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
