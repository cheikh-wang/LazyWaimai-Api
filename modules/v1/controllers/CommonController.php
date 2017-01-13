<?php

namespace app\modules\v1\controllers;

use app\modules\v1\models\PaymentPlatform;
use yii;
use yii\rest\Controller;

class CommonController extends Controller {

    /**
     * @inheritdoc
     */
    protected function verbs() {
        return [
            'setting' => ['GET', 'HEAD']
        ];
    }

    /**
     * 应用配置
     * @return array
     */
    public function actionSetting() {
        return [
            'common_remarks' => [
                '不放辣',
                '少放辣',
                '多放辣',
                '不吃香菜',
                '不吃蒜',
                '不吃葱'
            ],
            'payment_platforms' => PaymentPlatform::find()->all(),
        ];
    }
}