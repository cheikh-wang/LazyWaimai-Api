LazyWaimai-Api 
==========
** 此项目是本人学习时开发的系统懒人外卖的API端，为[Android客户端](https://github.com/cheikh-wang/LazyWaimai-Android)提供API服务，基于 [Yii2](https://github.com/yiisoft/yii2) 框架实现的。 **

环境条件
-------
+ PHP版本必须大于或等于php5.4

安装
-------
#### 1.clone到本地
```
git clone git@github.com:cheikh-wang/LazyWaimai-Api.git
```
#### 2.配置数据库
```
cd LazyWaimai-Api
vi config/web.php
```
#### 3.安装依赖
```
composer install
```
#### 4.初始化项目
```
./yii init
```
#### 5.配置服务器
```
配置nginx/apache的webroot指向LazyWaimai-Api/web
```
其他配置
-------
#### 1.短信服务的配置
###### 本项目的短信服务是使用的[云之讯](http://www.ucpaas.com)，请自行注册账户并按如下方式配置：

编辑config/web.php

```
'ucpass' => [
	'class' => 'app\components\Ucpaas',
    'accountSid' => '修改为你的云之讯Account Sid',
    'token' => '修改为你的云之讯Auth Token',
    'appId' => '修改为你的云之讯应用ID',
    'templateId' => '修改为你的云之讯短信模板ID',
],
```
#### 2.七牛云的配置
###### 本项目的图片上传服务是使用的[七牛](http://www.qiniu.com)，请自行注册账户并按如下方式配置：
```
'qiniu' => [
	'class' => 'app\components\QiNiu',
	'accessKey' => '修改为你的AccessKey',
	'secretKey' => '修改为你的SecretKey',
	'bucket' => '修改为你的空间名',
	'domain' => '修改为你的域名',
],
```