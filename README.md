# 投资分享模块安装
### 1：通过composer安装
```bash
composer require ysx123/ph_invest:dev-master --ignore-platform-reqs
```
### 2：在项目中增加模块的路由。修改配置文件：D:\www\ysx_www\ph02\shiny-pera-ios-dc\frontend\config\main.php
```php
 'components'=>[
 'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            //'rules' =>  $url_rules,
            'rules' =>  array_merge($url_rules,['recipe/<action:\w+>' => 'recipe/recipe/<action>']),
  ],
],
//增加模块
'modules' => [
        'recipe' => [
            'class' => 'ysx\recipe\Module',
        ],
    ],
```
### 3：app-api-doc项目增加url路由，用于混淆。修改url.php文件
```php
    //因为这是一个独立出去的模块。所以前面加个recipe模块名
 '/recipe/recipe/recipe-type' => "credit/syncabl1",
    '/recipe/recipe/index' => "credit/syncabl2",
    '/recipe/recipe/collect-list' => "credit/syncabl3",
    '/recipe/recipe/detail' => "credit/syncabl4",
    '/recipe/recipe/collect' => "credit/syncabl5",
    '/recipe/recipe/upload-image' => "credit/syncabl6",
    '/recipe/recipe/add-recipe' => "credit/syncabl7",
    '/recipe/recipe/del-recipe' => "credit/syncabl8",
    '/recipe/recipe/my-recipe' => "credit/syncabl9",
    '/recipe/recipe/add-comment' => "credit/syncabl10",
    '/recipe/recipe/comment-list' => "credit/syncabl11",
    '/recipe/recipe/delete-comment' => "credit/syncabl12",
    '/recipe/recipe/edit-recipe' => "credit/syncabl13",
```
### 4:修改app-api-doc项目，增加文档，直接复制到相应项目的文档目录里。示例文档在：D:\www\ysx_www\app-api-doc\docs\ph_wealth_cash_ios\recipe.md

### 5：执行生成混淆路由和混淆字段
```bash
#混淆字段
php generater.php 目录名
#混淆路由
php generate_url.php 目录名 前缀
```
### 6：上传图片接口的key,固定位took,需要手动改一下文档

### 7：线上项目执行数据迁移，创建表
```bash
    php yii migrate --migrationPath=vendor/ysx123/ph_invest/src/migrations/
```
#### 如果该迁移已经被执行过一次，那上面的命令无法再次执行，需要先删除掉执行记录。删除后再次执行上面迁移命令。
```sql
    DELETE FROM migration WHERE version = 'm230225_123456_create_recipe_table';
```
### 8:类型图标问题。如果设计图中有类型图标，需要手动上传到H5项目的中 recipe 目录，然后图片地址固定位y1.png到y10-png（这是选中的图片），未选中的是n1.png到n10.png。