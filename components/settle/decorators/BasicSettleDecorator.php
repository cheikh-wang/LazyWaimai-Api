<?php

namespace app\components\settle\decorators;

use yii;
use yii\web\BadRequestHttpException;
use app\components\settle\IDecorator;
use app\components\settle\models\SettleBody;
use app\components\settle\models\SettleResult;
use app\models\User;
use app\modules\v1\models\Address;

class BasicSettleDecorator implements IDecorator {

    /**
     * @param SettleBody $settleBody
     * @param SettleResult $settleResult
     * @throws BadRequestHttpException
     */
    public function settleBefore($settleBody, $settleResult) {
        // 最近一次使用的地址
        /** @var $user User */
        $user = Yii::$app->user->identity;
        if ($user->last_address_id != 0) {
            $settleBody->lastAddressId = $user->last_address_id;
            $settleResult->last_address = Address::findOne($user->last_address_id);
        }

        // 能够提交
        $settleResult->can_submit = ($settleResult->last_address != null);

        // 支付方式
        $settleResult->online_payment = $settleBody->payMethod == 1 ? true : false;
    }

    /**
     * @param SettleBody $settleBody
     * @param SettleResult $settleResult
     */
    public function settleAfter($settleBody, $settleResult) {}
}