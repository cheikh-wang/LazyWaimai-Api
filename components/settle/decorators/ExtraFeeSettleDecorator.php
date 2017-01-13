<?php

namespace app\components\settle\decorators;

use yii;
use yii\web\BadRequestHttpException;
use Exception;
use app\components\settle\IDecorator;
use app\components\settle\models\SettleBody;
use app\components\settle\models\SettleResult;
use app\modules\v1\models\CartExtra;
use app\modules\v1\models\Business;


class ExtraFeeSettleDecorator implements IDecorator {

    /**
     * @var CartExtra[]
     */
    private $cartExtras;

    /**
     * @param SettleBody $settleBody
     * @param SettleResult $settleResult
     * @throws BadRequestHttpException
     */
    public function settleBefore($settleBody, $settleResult) {
        /** @var $businessInfo Business */
        $businessInfo = Business::findOne($settleBody->businessId);
        if ($businessInfo == null) {
            throw new BadRequestHttpException('不存在该商铺');
        }

        if ($businessInfo->shipping_fee > 0) {
            $shippingFee = new CartExtra();
            $shippingFee->name = '配送费';
            $shippingFee->description = '本订单由【'.$businessInfo->name.'】进行配送';
            $shippingFee->price = $businessInfo->shipping_fee;
            $this->cartExtras[] = $shippingFee;

            // 加价
            $settleBody->originPrice += $businessInfo->shipping_fee;
        }
        if ($businessInfo->package_fee > 0) {
            $packageFee = new CartExtra();
            $packageFee->name = '包装费';
            $packageFee->price = $businessInfo->package_fee;
            $this->cartExtras[] = $packageFee;

            // 加价
            $settleBody->originPrice += $businessInfo->package_fee;
        }
    }

    /**
     * @param SettleBody $settleBody
     * @param SettleResult $settleResult
     */
    public function settleAfter($settleBody, $settleResult) {
        // 使用事务来进行保存
        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($this->cartExtras as $extraFee) {
                /** @var $extraFee CartExtra */
                $extraFee->cart_id = $settleBody->cartId;
                if (!$extraFee->save()) {
                    throw new Exception('存储额外费用出错.');
                }
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
        }
    }
}