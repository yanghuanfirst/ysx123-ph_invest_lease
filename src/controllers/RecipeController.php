<?php
namespace ysx\recipe\controllers;

use common\enums\codes\ResponseCode;
use common\helpers\OssHelper;
use common\helpers\ReturnHelper;
use common\helpers\Util;
use ysx\recipe\models\Comment;
use ysx\recipe\models\Recipe;
use ysx\recipe\models\RecipeAddress;
use ysx\recipe\models\RecipeCollect;
use common\models\youmi\FaceProcessLog;
use common\services\youmi\PictureService;
use Yii;
use yii\web\UploadedFile;
use frontend\controllers\BaseController;
use ysx\recipe\models\RecipeOrder;
use function foo\func;

class RecipeController extends BaseController
{
    public $requireLoginActions = [
        'upload-image',
        'add-recipe',
        'del-recipe',
        'collect-list',
        'collect',
        'my-recipe',
        'add-comment',
        'delete-comment',
        'my-address',
        "add-address",
        "edit-address",
        "del-address",
        "add-order",
        'order-list',
        'order-detail'
    ];
    protected $recipeType = [
        [
            "id"=>0,
            "value"=>"All",
        ],
        [
            "id"=>1,
            "value"=>"Stocks",
        ],
        [
            "id"=>2,
            "value"=>"Bonds",
        ],
        [
            "id"=>3,
            "value"=>"Funds",
        ],
        [
            "id"=>4,
            "value"=>"Real Estate",
        ],
        [
            "id"=>5,
            "value"=>"Crypto",
        ],
        [
            "id"=>6,
            "value"=>"Retirement",
        ],
        [
            "id"=>7,
            "value"=>"Wealth Management"
        ]
    ];

    /**
     * @desc actionRecipeType 菜谱类型
     * @create_at 2025/2/26 11:06
     * @return array
     */
    function actionRecipeType():array
    {
        $result = [];
        $h5Path = Yii::$app->params['H5_URL'];
        foreach ($this->recipeType as $k=>$v){
            $result[$k] = [
                "id"=>$v['id'],
                "value"=>$v['value'],
                "recipe_type_img_selected"=>$h5Path."/recipe/y".($k+1).".png",
                "recipe_type_img_no"=>$h5Path."/recipe/n".($k+1).".png",
            ];
        }
        return $this->formatJson(0, 'success', ["type_list"=>$result]);
    }
    /**
     * @desc actionIndex 首页菜谱列表
     * @create_at 2025/2/26 17:26
     * @return array|string
     */
    function actionIndex():array
    {
        $request = Yii::$app->request;
        $title = $request->get('title',"");//标题
        $type = $request->get('type',0);//类型
        $page = $request->get("page",1);
        $pageSize = $request->get("size",10);
        $recipeModel = new Recipe();
        $recipeModel->scenario = 'recipe_list';
        $recipeModel->load(Yii::$app->request->get(),"");
        if (!$recipeModel->validate()) {
            return $this->formatJson(ResponseCode::PARAM_CHECK_FAIL, current($recipeModel->getFirstErrors()));
        }
        $map = ["and"];
        if($title){
            $map[] = ['like','title',$title];
        }
        if($type){
            $map[] = ['type'=>$type];
        }
        $offset = ($page - 1) * $pageSize;
        $total = Recipe::find()->where($map)->count();
        $list = Recipe::find()->select(["id","title","cover_img","type","created_at","collect_num","like_num",'collect_num'])->where($map)->orderBy([
            'id' => SORT_DESC,
        ])->offset($offset)->limit($pageSize)->asArray()->all();
        //查询推荐的3条的数据
        //$recommend = Recipe::find()->select(["id","title","cover_img","type"])->where(["recommend"=>2])->limit(3)->asArray()->all();
        $recommend = Recipe::find()->select(["id","title","cover_img","type","recipe_price"])->orderBy(["collect_num"=>SORT_DESC])->limit(3)->asArray()->all();
        return $this->formatJson(0, 'success', compact('total','list','recommend'));
    }

    /**
     * @desc actionUploadImage  上传图片
     * @create_at 2025/2/26 22:01
     * @return array|string
     */
    function actionUploadImage(){
        $model = new Recipe();
        $model->scenario = 'upload_image';
        //为了通用，后续的字段名都用took
        $model->image_file = UploadedFile::getInstanceByName('took');
        Yii::info("食谱上传图片 " . json_encode($model->image_file, JSON_UNESCAPED_UNICODE), "appInfo");
        if (!$model->validate()) {
            //return json_encode(['success' => true, 'url' => Yii::getAlias('@web/uploads/') . basename($filePath)]);
            return $this->formatJson(ResponseCode::PARAM_CHECK_FAIL, current($model->getFirstErrors()));
        }
        $extension = substr($model->image_file->name, strrpos($model->image_file->name, '.') + 1);
        $object = 'recipe/'.Util::getNewName($extension);
        $configKey = self::getOssConfigKey();
        //$bucketName = self::getBucketName($configKey);
        $localFile = $model->image_file->tempName;
        $backImage = "";
        try {
            $res = OssHelper::uploadFile($object, $localFile, $configKey);
            if (!$res) {
                return ReturnHelper::error('Image upload failed, please try again', (object)[], ReturnHelper::ERR_AAR_FRONT);//OSS上传图片失败
            }
            $configKey = PictureService::getOssConfigKey();
            //身份证
            $backImage = OssHelper::getFileUrl($object, $configKey);
        }catch (\Exception $e){
            return ReturnHelper::error('Image upload failed, please try again.', (object)[], ReturnHelper::ERR_AAR_FRONT);//OSS上传图片失败
        }

        return $this->formatJson(0, 'success',["url"=>$backImage]);
    }

    /**
     * @desc actionAddRecipe
     * @create_at 2025/2/26 10:29
     * @return array|string
     */
    function actionAddRecipe():array
    {
        $userId = $this->getLoginUserId();
        $data = Yii::$app->request->post();
        $recipeModel = new Recipe();
        $recipeModel->scenario = 'add_recipe';
        $recipeModel->load($data,'');
        Yii::info("userId: {$userId} 发布菜谱 " . json_encode($data, JSON_UNESCAPED_UNICODE), "appInfo");
        Yii::info("userId: {$userId} 发布菜谱2 " . json_encode($recipeModel->getErrors(), JSON_UNESCAPED_UNICODE), "appInfo");
        if (!$recipeModel->validate()) {
            return $this->formatJson(ResponseCode::PARAM_CHECK_FAIL, current($recipeModel->getFirstErrors()));
        }
        $recipeModel->user_id = $userId;
        $res = $recipeModel->save();
        if (!$res){
            return $this->formatJson(-1, "add recipe fail please try again");
        }
        return $this->formatJson(0, 'success'); //新增成功
    }

    /**
     * @desc actionDelRecipe 删除食谱
     * @create_at 2025/2/26 11:20
     * @return array
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    function actionDelRecipe():array
    {
        $userId = $this->getLoginUserId();
        $data = Yii::$app->request->post();
        $recipeModel = new Recipe();
        $recipeModel->scenario = 'del_recipe';
        $recipeModel->load($data,'');
        if (!$recipeModel->validate()) {
            return $this->formatJson(ResponseCode::PARAM_CHECK_FAIL, current($recipeModel->getFirstErrors()));
        }
        $info = Recipe::find()->where(["user_id"=>$userId,"id"=>$data["id"]])->one();
        if (!$info){
            return $this->formatJson(-1, "recipe not exist");
        }
        $res = $info->delete();
        if (!$res){
            return $this->formatJson(-1, "delete recipe fail please try again");
        }
        return $this->formatJson(0, 'success'); //删除成功
    }

    /**
     * @desc actionCollectList 收藏列表
     * @create_at 2025/2/26 15:20
     * @return array
     */
    function actionCollectList():array
    {
        $userId = $this->getLoginUserId();
        $request = Yii::$app->request;
        $title = $request->post('title',"");//标题
        $page = $request->post("page",1);
        $pageSize = $request->post("size",10);
        $recipeModel = new Recipe();
        $recipeModel->scenario = 'collect_list';
        $recipeModel->load(Yii::$app->request->post(),"");
        if (!$recipeModel->validate()) {
            return $this->formatJson(ResponseCode::PARAM_CHECK_FAIL, current($recipeModel->getFirstErrors()));
        }
        $collectInfo = RecipeCollect::find()->select(["user_id","recipe_id"])->where(["user_id"=>$userId])->asArray()->all();
        $recipeIds = array_column($collectInfo,"recipe_id");
        $list = [];
        $total = 0;
        if (empty($recipeIds)){
            return $this->formatJson(0, 'success',compact("list","total"));
        }
        $offset = ($page - 1) * $pageSize;
        $map = ["and"];
        if($title){
            $map[] = ['like','title',$title];
        }
        $map[] = ['id'=>$recipeIds];

        $list = Recipe::find()->where($map)->select(["id","title","cover_img","type","recipe_price"])->offset($offset)->limit($pageSize)->orderBy(["id"=>SORT_DESC])->asArray()->all();
        $total = Recipe::find()->where($map)->count();
        return $this->formatJson(0, 'success',compact("list","total"));
    }

    /**
     * @desc actionCollect 收藏或者取消收藏 或者点赞 或者取消点赞
     * @create_at 2025/2/26 17:23
     * @return array
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    function actionCollect():array
    {
        $userId = $this->getLoginUserId();
        $request = Yii::$app->request;
        $recipeModel = new Recipe();
        $recipeModel->scenario = 'collect';
        $recipeModel->load(Yii::$app->request->post(),"");
        if (!$recipeModel->validate()) {
            return $this->formatJson(ResponseCode::PARAM_CHECK_FAIL, current($recipeModel->getFirstErrors()));
        }
        $recipeId = $request->post("id",0);
        $actionType = $request->post("action_type",1);//1收藏 2点赞
        $collectInfo = RecipeCollect::find()->where(["user_id"=>$userId,"recipe_id"=>$recipeId,"action_type"=>$actionType])->one();
        //取消收藏
        $transaction = Yii::$app->db->beginTransaction();
        try{
            if($collectInfo){
                $res = $collectInfo->delete();
                //减少数量
                if($actionType == 1 ){
                    $map = ['collect_num' => -1];
                }else{
                    $map = ['like_num' => -1];
                }
                Recipe::updateAllCounters($map, ['id' => $recipeId]);
            }else{
                //添加收藏
                $collectModel = new RecipeCollect();
                $collectModel->user_id = $userId;
                $collectModel->recipe_id = $recipeId;
                $collectModel->action_type = $actionType;
                $collectModel->save();
                //增加数量
                if($actionType == 1 ){
                    $map = ['collect_num' => 1];
                }else{
                    $map = ['like_num' => 1];
                }
                Recipe::updateAllCounters($map, ['id' => $recipeId]);
            }
            $transaction->commit();
        }catch (\Exception $e){
            $transaction->rollBack();
            return $this->formatJson(-1, "action fail please try again");
        }
        return $this->formatJson(0, 'success');
    }

    /**
     * @desc actionDetail 查看详情
     * @create_at 2025/2/26 17:53
     * @return array
     */
    function actionDetail():array
    {
        $userId = $this->getLoginUserId();
        $request = Yii::$app->request;
        $recipeModel = new Recipe();
        $recipeModel->scenario = 'detail';
        $recipeModel->load(Yii::$app->request->get(),"");
        Yii::info("userId: {$userId} 食谱详情 " . json_encode(Yii::$app->request->get(), JSON_UNESCAPED_UNICODE), "appInfo");
        if (!$recipeModel->validate()) {
            return $this->formatJson(ResponseCode::PARAM_CHECK_FAIL, current($recipeModel->getFirstErrors()));
        }
        $recipeId = $request->get("id",0);
        $info = Recipe::find()->select(["id","title","cover_img","type","detail","created_at","user_id","collect_num","like_num","recipe_price"])->where(["id"=>$recipeId])->asArray()->one();
        if(!$info)
            return $this->formatJson(-1, "recipe not exist");
        $info["is_collect"] = 0;//0：不显示收藏按钮  1：显示收藏按钮
        $info["is_like"] = 0;//0：不显示点赞按钮  1：显示点赞按钮
        $info["is_delete"] = 0;//0：不显示删除按钮  1：显示删除按钮
        $info["is_collected"] = 0;//0：未收藏  1：已收藏
        $info["is_liked"] = 0;//0：未点赞  1：已点赞
        if($userId){
            if($userId != $info["user_id"]){
                $info["is_collect"] = 1;//0：不显示收藏按钮  1：显示收藏按钮
                $info["is_like"] = 1;//0：不显示点赞按钮  1：显示点赞按钮
                $info["is_delete"] = 0;//0：不显示删除按钮  1：显示删除按钮
            }
            //判断是否收藏
            if($info["is_collect"] == 1 || $info["is_like"] == 1){
                $collectedOrLiked = RecipeCollect::find()->select(["id","action_type"])->where(["user_id"=>$userId,"recipe_id"=>$recipeId])->asArray()->all();
                if ($collectedOrLiked){
                        foreach ($collectedOrLiked as $v){
                            if($v["action_type"] == 1){
                                $info["is_collected"] = 1;
                            }elseif ($v["action_type"] == 2){
                                $info["is_liked"] = 1;
                            }
                        }
                }

            }
        }
        return $this->formatJson(0, 'success',compact("info"));
    }

    /**
     * @desc actionEditRecipe 编辑食谱
     * @create_at 2025/2/26 18:00
     * @return array
     */
    function actionEditRecipe():array
    {
        $userId = $this->getLoginUserId();
        $data = Yii::$app->request->post();
        $recipeModel = new Recipe();
        $recipeModel->scenario = 'edit_recipe';
        $recipeModel->load($data,'');
        if (!$recipeModel->validate()) {
            return $this->formatJson(ResponseCode::PARAM_CHECK_FAIL, current($recipeModel->getFirstErrors()));
        }
        $recipeModel = Recipe::find()->where(["user_id"=>$userId,"id"=>$data["id"]])->one();
        if(!$recipeModel){
            return $this->formatJson(-1, "recipe not exist");
        }
        $recipeModel->setAttributes($data);
        $res = $recipeModel->save();
        if (!$res){
            return $this->formatJson(-1, "edit recipe fail please try again");
        }
        return $this->formatJson(0, 'action success');
    }

    /**
     * @desc actionMyRecipe 我的发布
     * @create_at 2025/2/26 15:26
     * @return array
     */
    function actionMyRecipe():array
    {
        $userId = $this->getLoginUserId();
        $request = Yii::$app->request;
        $page = $request->get("page",1);
        $pageSize = $request->get("size",10);
        $recipeModel = new Recipe();
        $recipeModel->scenario = 'my_recipe';
        $recipeModel->load(Yii::$app->request->get(),"");
        if (!$recipeModel->validate()) {
            return $this->formatJson(ResponseCode::PARAM_CHECK_FAIL, current($recipeModel->getFirstErrors()));
        }
        $offset = ($page - 1) * $pageSize;
        $total = Recipe::find()->where(["user_id"=>$userId])->count();
        $list = Recipe::find()->where(["user_id"=>$userId])->select(["id","title","cover_img","type","created_at","recipe_price"])->orderBy([
            'id' => SORT_DESC,
        ])->offset($offset)->limit($pageSize)->asArray()->all();
        //收藏数量
        $collectCount = RecipeCollect::find()->where(["user_id"=>$userId])->count();
        return $this->formatJson(0, 'success', compact('total','list',"collectCount"));
    }

    /**
     * @desc actionAddComment 发布评论
     * @create_at 2025/3/8 14:07
     * @return array
     */
    function actionAddComment():array
    {
        $user = $this->getLoginUser();
        $request = Yii::$app->request;
        $recipeModel = new Recipe();
        $recipeModel->scenario = 'add_comment';
        $recipeModel->load(Yii::$app->request->post(),"");
        if (!$recipeModel->validate()) {
            return $this->formatJson(ResponseCode::PARAM_CHECK_FAIL, current($recipeModel->getFirstErrors()));
        }
        $comment = new Comment();
        $comment->user_id = $user->id;
        $comment->username = $user->user_name;
        $comment->recipe_id = $request->post("recipe_id");
        $comment->comment_content = $request->post("comment_content");
        $comment->save();
        return $this->formatJson(0, 'Review success');
    }

    /**
     * @desc actionCommentList 评论列表
     * @create_at 2025/3/8 14:23
     * @return array
     */
    function actionCommentList():array
    {
        $userId = $this->getLoginUserId();
        $request = Yii::$app->request;
        $page = $request->get("page",1);
        $pageSize = $request->get("size",10);
        $recipeModel = new Recipe();
        $recipeModel->scenario = 'recipe_comment_list';
        $recipeModel->load(Yii::$app->request->get(),"");
        if (!$recipeModel->validate()) {
            return $this->formatJson(ResponseCode::PARAM_CHECK_FAIL, current($recipeModel->getFirstErrors()));
        }
        $map = [
            "recipe_id"=>$request->get("recipe_id"),
        ];
        $offset = ($page - 1) * $pageSize;
        $total = Comment::find()->where($map)->count();
        $list = Comment::find()->select(["id","comment_content","username","user_id","created_at"])->where($map)->orderBy([
            'id' => SORT_DESC,
        ])->offset($offset)->limit($pageSize)->asArray()->all();
        foreach ($list as $k=>$v){
            $v["is_delete"] = 0;//1 显示删除按钮，0 不显示
            if($userId){
                if($userId == $v["user_id"]){
                    $v["is_delete"] = 1;
                }
            }
            //对手机号进行脱敏，显示手机号前3位，后4位，中间用*号代替
            $v["username"] =  substr_replace($v["username"],'****',3,4);
            $list[$k] = $v;
        }
        return $this->formatJson(0, 'success',compact("total","list"));
    }

    /**
     * @desc actionDeleteComment 删除评论
     * @create_at 2025/3/8 14:28
     * @return array
     */
    function actionDeleteComment():array
    {
        $userId = $this->getLoginUserId();
        $request = Yii::$app->request;
        $recipeModel = new Recipe();
        $recipeModel->scenario = 'del_comment';
        $recipeModel->load(Yii::$app->request->get(),"");
        if (!$recipeModel->validate()) {
            return $this->formatJson(ResponseCode::PARAM_CHECK_FAIL, current($recipeModel->getFirstErrors()));
        }
        $commentId = $request->get("id");
        $info = Comment::find()->where(["user_id"=>$userId,"id"=>$commentId])->one();
        if (!$info){
            return $this->formatJson(-1, "comment not exist");
        }
        $info->delete();
        return $this->formatJson(0, 'delete success');
    }

    /**
     * @desc actionMyAddress 我的地址
     * @create_at 2025/3/16 13:03
     * @return array
     */
    function actionMyAddress():array
    {
        $userId = $this->getLoginUserId();
        $list = RecipeAddress::find()->where(["user_id"=>$userId])->orderBy([
            'is_default' => SORT_DESC,
        ])->asArray()->all();
        return $this->formatJson(0, 'success',compact("list"));
    }

    /**
     * @desc actionAddAddress
     * @create_at 2025/3/16 13:36
     * @return array
     */
    function actionAddAddress():array
    {
        $userId = $this->getLoginUserId();
        $address = new RecipeAddress();
        $address->scenario = 'add_address';
        $address->load(Yii::$app->request->post(),"");
        if (!$address->validate()) {
            return $this->formatJson(ResponseCode::PARAM_CHECK_FAIL, current($address->getFirstErrors()));
        }
        $address->user_id = $userId;
        $address->save();
        return $this->formatJson(0, 'action success');
    }

    /**
     * @desc actionEditAddress 编辑地址
     * @create_at 2025/3/16 15:09
     * @return array
     */
    function actionEditAddress():array
    {
        $userId = $this->getLoginUserId();
        $address = new RecipeAddress();
        $address->scenario = 'edit_address';
        $data = Yii::$app->request->post();
        $address->load($data,"");
        if (!$address->validate()) {
            return $this->formatJson(ResponseCode::PARAM_CHECK_FAIL, current($address->getFirstErrors()));
        }
        $addressInfo = RecipeAddress::find()->where(["user_id"=>$userId,"id"=>$data["id"]])->one();
        if(!$addressInfo){
            return $this->formatJson(-1, "address not exist");
        }
        //$addressInfo->setAttributes($data);
        $addressInfo->address = $data['address'];
        $addressInfo->consignee = $data['consignee'];
        $addressInfo->consignee_tel = $data['consignee_tel'];
        $addressInfo->is_default = $data['is_default'];
        $addressInfo->save();
       return $this->formatJson(0, 'action success');
    }

    /**
     * @desc actionDelAddress 删除地址
     * @create_at 2025/3/16 15:35
     * @return array
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    function actionDelAddress():array
    {
        $userId = $this->getLoginUserId();
        $request = Yii::$app->request;
        $address = new RecipeAddress();
        $address->scenario = 'del_address';
        $data = Yii::$app->request->post();
        $address->load($data,"");
        if (!$address->validate()) {
            return $this->formatJson(ResponseCode::PARAM_CHECK_FAIL, current($address->getFirstErrors()));
        }
        $info = RecipeAddress::find()->where(["user_id"=>$userId,"id"=>$data["id"]])->one();
        if (!$info){
            return $this->formatJson(-1, "address not exist");
        }
        $info->delete();
        return $this->formatJson(0, 'delete success');
    }

    /**
     * @desc actionAddOrder 下单
     * @create_at 2025/3/16 15:56
     * @return array
     */
    function actionAddOrder():array
    {
        $userId = $this->getLoginUserId();
        $request = Yii::$app->request;
        $order = new RecipeOrder();
        $order->scenario = 'add_order';
        $data = Yii::$app->request->post();
        $order->load($data,"");
        if (!$order->validate()) {
            return $this->formatJson(ResponseCode::PARAM_CHECK_FAIL, current($order->getFirstErrors()));
        }
        $order->remark = $data["remark"];
        $order->address_id = $data["address_id"];
        $order->order_no = $this->generateOrderNumber();
        $order->user_id = $userId;
        $order->save();
        return $this->formatJson(0, 'action success');
    }

    /**
     * @desc actionOrderList 订单列表
     * @create_at 2025/3/16 16:07
     * @return array
     */
    function actionOrderList():array
    {
        $userId = $this->getLoginUserId();
        $order = new RecipeOrder();
        $order->scenario = 'order_list';
        $data = Yii::$app->request->get();
        $order->load($data,"");
        if (!$order->validate()) {
            return $this->formatJson(ResponseCode::PARAM_CHECK_FAIL, current($order->getFirstErrors()));
        }
        $map["user_id"] = $userId;
        if($data["order_status"]){
            $map["order_status"] = $data["order_status"];
        }
        $list = RecipeOrder::find()->select(["id","recipe_id","address_id","order_no","order_status","remark","created_at"])->with(["recipe"=>function($query){
            $query->select(["id","title","cover_img","recipe_price"]);
        },"address"=>function($query){
            $query->select(["id","address","consignee","consignee_tel"]);
        }])->where($map)->orderBy([
            'id' => SORT_DESC,
        ])->asArray()->all();
        foreach ($list as $key=>$item){
            $item["order_status_zh"] = match($item["order_status"]){
                1=>"Under review",
                2=>"Successful audit",
                3=>"Audit failure",
                default=>"unknown"
            };
            $list[$key] = $item;
        }

        return $this->formatJson(0, 'success',compact("list"));
    }

    function actionOrderDetail():array
    {
        $userId = $this->getLoginUserId();
        $order = new RecipeOrder();
        $order->scenario = 'order_detail';
        $data = Yii::$app->request->get();
        $order->load($data,"");
        if (!$order->validate()) {
            return $this->formatJson(ResponseCode::PARAM_CHECK_FAIL, current($order->getFirstErrors()));
        }
        $map["user_id"] = $userId;
        if($data["id"]){
            $map["id"] = $data["id"];
        }
        $info = RecipeOrder::find()->select(["id","recipe_id","address_id","order_no","order_status","remark","created_at"])->with(["recipe"=>function($query){
            $query->select(["id","title","cover_img","recipe_price"]);
        },"address"=>function($query){
            $query->select(["id","address","consignee","consignee_tel"]);
        }])->where($map)->orderBy([
            'id' => SORT_DESC,
        ])->asArray()->one();
        if(!$info)
            return $this->formatJson(-1, "order not exist");
        $info["order_status_zh"] = match($info["order_status"]){
            1=>"Under review",
            2=>"Successful audit",
            3=>"Audit failure",
            default=>"unknown"
        };
        return $this->formatJson(0, 'success',compact("info"));
    }

    /**
     * @desc generateOrderNumber 生成订单号
     * @create_at 2025/3/16 15:55
     * @return string
     */
    function generateOrderNumber():string
    {
        return date('YmdHis') . substr(uniqid(), -5);
    }
    /**
     * 获取oss配置的key
     *
     * @return string
     */
    public static function getOssConfigKey()
    {
        $ossName = 'defaultOss';
        if (ENV == 'prod' || defined('ENV_CONFIG')) {// 正式环境或者docker4环境
            $ossName = 'defaultOss';
        }
        return $ossName;
    }

    /**
     * 获取bucket
     *
     * @param string $configKey
     * @return string
     */
    public static function getBucketName($configKey = '')
    {
        if (empty($configKey)) {
            $configKey = self::getOssConfigKey();
        }
        $params = \Yii::$app->params;
        if (isset($params[$configKey])) {
            $bucket = $params[$configKey][YII_ENV.'Bucket'];
        } else {
            $bucket = '';
        }
        return $bucket;
    }



}