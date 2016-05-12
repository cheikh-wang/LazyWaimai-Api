<?php

namespace app\components\activity\types;

use app\modules\v1\models\Order;
use app\components\settle\models\SettleBody;
use app\components\activity\IDiscount;
use app\components\activity\models\DiscountInfo;
use app\components\activity\models\DiscountResult;

/**
 * 满减优惠的逻辑处理类
 */
class FullCutDiscount implements IDiscount {

    /**
     * 处理活动优惠，返回优惠结果
     * @param DiscountInfo $discountInfo
     * @param SettleBody $settleBody
     * @return DiscountResult $discountResult
     */
    public function handleDiscount($discountInfo, $settleBody) {
        $discountResult = new DiscountResult();

        if ($settleBody->payMethod == Order::PAYMENT_ONLINE) {
            $attributes = json_decode($discountInfo->attribute);
            foreach ($attributes as $attribute) {
                if ($settleBody->originPrice > $attribute->condition
                    && $discountResult->price < $attribute->cut_price) {
                    $discountResult->price = $attribute->cut_price;
                    $discountResult->is_valid = true;
                    $discountResult->is_share = $discountInfo->is_share;
                    $discountResult->description = '在线支付满' . $attribute->condition . '减' . $attribute->cut_price;
                }
            }
        }

        return $discountResult;
    }
}