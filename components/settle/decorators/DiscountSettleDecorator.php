<?php

namespace app\components\settle\decorators;

use yii;
use Exception;
use app\components\settle\IDecorator;
use app\components\settle\models\SettleBody;
use app\components\settle\models\SettleResult;
use app\components\activity\DiscountContext;
use app\modules\v1\models\CartDiscount;
use app\modules\v1\models\BusinessActivity;

class DiscountSettleDecorator implements IDecorator {

    /**
     * @var CartDiscount[]
     */
    private $CartDiscounts;

    /**
     * @param SettleBody $settleBody
     * @param SettleResult $settleResult
     */
    public function settleBefore($settleBody, $settleResult) {
        $discountInfoList = BusinessActivity::discountInfoList($settleBody->businessId);
        foreach ($discountInfoList as $discountInfo) {
            $discountContext = new DiscountContext($discountInfo->code);
            if ($discountContext != null) {
                $discountResult = $discountContext->handleDiscount($discountInfo, $settleBody);

                if ($discountResult->is_valid) {
                    $cartDiscount = new CartDiscount();
                    $cartDiscount->activity_id = $discountInfo->id;
                    $cartDiscount->name = $discountInfo->name;
                    $cartDiscount->price = $discountResult->price;
                    $cartDiscount->description = $discountResult->description;
                    $cartDiscount->icon_name = $discountInfo->icon_name;
                    $cartDiscount->icon_color = $discountInfo->icon_color;

                    if (!$discountResult->is_share) {
                        $this->CartDiscounts = [];
                        $settleBody->discountPrice = $cartDiscount->price;
                        $this->CartDiscounts[] = $cartDiscount;
                        break;
                    } else {
                        $settleBody->discountPrice += $cartDiscount->price;
                        $this->CartDiscounts[] = $cartDiscount;
                    }
                }
            }
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
            foreach ($this->CartDiscounts as $discountInfo) {
                /** @var $discountInfo CartDiscount */
                $discountInfo->cart_id = $settleBody->cartId;
                if (!$discountInfo->save()) {
                    throw new Exception('存储优惠信息出错.');
                }
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
        }
    }
}