<?php

namespace ysx\recipe\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class RecipeReport extends ActiveRecord
{

    public function rules() :array
    {
        return [
            //举报帖子
            [['id'], 'required',
                'message' => 'param cannot be empty.',
                'on' => 'report_recipe',
            ],
            [['id'], 'integer',
                'min' => 1,
                'message' => 'Param is illegal',
                'tooSmall' => 'Param is illegal',
                'on' => 'report_recipe',
            ],
            [['report_type_id'], 'required',
                'message' => 'param cannot be empty.',
                'on' => 'report_recipe',
            ],
            [['report_type_id'], 'integer',
                'min' => 1,
                'message' => 'Please select a report type',
                'tooSmall' => 'The type of report is illegal',
                'on' => 'report_recipe',
            ],
            [
                ["report_content"],"required","message"=>"Please enter the report content",'on' => 'report_recipe',
            ],
            [
                ['report_content'],
                'string',
                'max' => 1000,
                "min" => 1,
                'on' => 'report_recipe',
                'tooLong' => 'The maximum length of the report content is 1000 characters',
                "tooShort" => 'The minimum length of the report content is 1 characters'

            ]
        ];
    }
    public function scenarios() :array
    {

        $scenarios['report_recipe'] = ['report_content',"report_type_id","id"];
        $scenarios['default'] = [];
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