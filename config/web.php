<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\controllers',
    'modules' => [
        'service' => [
            'class' => 'app\modules\service\ServiceModule',
        ],
        'v1' => [
            'class' => 'app\modules\v1\ApiModule'
        ],
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => '5opbkVM6PYmVxcyNvHG1wK06fkIh0vYG',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser'
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
            'loginUrl' => null,
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'site/index',
                'site/<action:\w+>' => 'site/<action>',

                '<version:\w+>/oauth/<action:\w+>' => '<version>/oauth/<action>',
                'GET <version:\w+>/settings' => '<version>/common/setting',
                'GET <version:\w+>/businesses/<businessId:\d+>/products' => '<version>/business/products',
                'POST <version:\w+>/businesses/<businessId:\d+>/favorite' => '<version>/business/favorite',
                'GET <version:\w+>/users/favorites' => '<version>/user/favorites',

                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'v1/user',
                        'v1/address',
                        'v1/business',
                        'v1/order',
                        'v1/payment',
                        'v1/favorite',
                        'v1/file',
                        'v1/feedback',
                    ],
                    'extraPatterns' => [
                        'POST check' => 'check'
                    ],
                ],
            ]
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'ucpass' => [
            'class' => 'app\components\Ucpaas',
            'accountSid' => '修改为你的云之讯Account Sid',
            'token' => '修改为你的云之讯Auth Token',
            'appId' => '修改为你的云之讯应用ID',
            'templateId' => '修改为你的云之讯短信模板ID',
        ],
        'qiniu' => [
            'class' => 'app\components\QiNiu',
            'accessKey' => '修改为你的AccessKey',
            'secretKey' => '修改为你的SecretKey',
            'bucket' => '修改为你的空间名',
            'domain' => '修改为你的域名',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'trace', 'info']
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
