LazyWaimai-Api 
==========
此项目是懒人外卖（本人用来练手的项目,类似于百度外卖,美团外卖和饿了么的系统）的API端，为[Android客户端](https://github.com/cheikh-wang/LazyWaimai-Android)提供API服务，基于 [Yii2](https://github.com/yiisoft/yii2) 框架实现的。

环境条件
-------
+ PHP版本必须大于或等于php5.4

部署
-------

## windows环境下部署

#### 1.准备工作

###### 搭建好WampServer服务器环境

教程可参考按照[windows下通过Wamp搭建服务器环境.doc](/doc/windows下通过Wamp搭建服务器环境.doc)

###### 安装好php的包管理工具composer

教程可参考[windows下安装composer.doc](/doc/windows下安装composer.doc)

###### 安装好git

教程自行百度

#### 2.clone代码

    假设在上一步准备工作中WampServer被安装在了D:/wamp目录下

打开cmd，以此输入以下命令

```
d:
cd wamp/www
git clone https://github.com/cheikh-wang/LazyWaimai-Api.git
cd LazyWaimai-Api
```

#### 3.安装依赖

```
composer global require "fxp/composer-asset-plugin:^1.3.1"
composer install
```

#### 4.配置数据库

###### 导入sql文件到数据库

参考[导入sql文件.doc](/doc/导入sql文件.doc)

###### 配置数据库

打开config/db.php文件，修改username和password（mysql密码默认为空）

```
<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=lazy_waimai',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
];

```

#### 5.其他配置

###### 1.短信服务的配置

本项目的短信服务是使用的[云之讯](http://www.ucpaas.com)，请自行注册账户并按如下方式配置：

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
###### 2.七牛云的配置

本项目的图片上传服务是使用的[七牛](http://www.qiniu.com)，请自行注册账户并按如下方式配置：

```
'qiniu' => [
	'class' => 'app\components\QiNiu',
	'accessKey' => '修改为你的AccessKey',
	'secretKey' => '修改为你的SecretKey',
	'bucket' => '修改为你的空间名',
	'domain' => '修改为你的域名',
],
```

到此项目部署完成，请在浏览器中输入```http://localhost/LazyWaimai-Api/Web```进行查看