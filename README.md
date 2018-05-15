LazyWaimai-Api 
==========
此项目是懒人外卖（本人用来练手的项目,类似于百度外卖,美团外卖和饿了么的系统）的API端，为[Android客户端](https://github.com/cheikh-wang/LazyWaimai-Android)提供API服务，基于 [Yii2](https://github.com/yiisoft/yii2) 框架实现的。

环境条件
-------
+ PHP版本必须大于或等于php5.4

安装
-------
#### 1.clone到本地
```
git clone https://github.com/cheikh-wang/LazyWaimai-Api.git
```
#### 2.配置数据库
1. 将sql文件导入到数据库中

2. 配置数据库：

```
cd LazyWaimai-Api
vi config/db.php
```
#### 3.安装依赖

本项目使用composer管理依赖,所以需要先安装composer（已安装请跳过）

```
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```

还需要安装composer-asset-plugin（已安装请跳过）

```
composer global require "fxp/composer-asset-plugin:^1.3.1"
```

安装项目所需依赖（开始之前请确保composer和composer-asset-plugin已成功安装)

```
composer install
```

#### 4.给予权限

```
chmod 777 runtime
chmod 777 web/assets
chmod 755 yii
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
