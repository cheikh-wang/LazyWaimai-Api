<?php

namespace app\components\activity\types;

use yii;
use app\modules\v1\models\Order;
use app\components\settle\models\SettleBody;
use app\components\activity\IDiscount;
use app\components\activity\models\DiscountInfo;
use app\components\activity\models\DiscountResult;

/**
 * 首单优惠的逻辑处理类
 */
class FirstOrderDiscount implements IDiscount {

    /**
     * 处理活动优惠，返回优惠结果
     * @param DiscountInfo $discountInfo
     * @param SettleBody $settleBody
     * @return DiscountResult $discountResult
     */
    public function handleDiscount($discountInfo, $settleBody) {
        // 查询该用户的有效订单数
        $count = Order::find()->where([
            'and',
            ['user_id' => Yii::$app->user->id],
            ['>', 'status', Order::STATUS_WAIT_SUBMIT]
        ])->count();

        $discountResult = new DiscountResult();
        $discountResult->is_share = $count == 0;
        $discountResult->is_share = $discountInfo->is_share;
        $discountResult->price = $discountInfo->attribute;
        $discountResult->description = '(不与其他活动共享)新用户下单立减'
            .$discountResult->price.'元';

        return $discountResult;
    }
}