<?php

namespace app\components\activity;

use app\components\settle\models\SettleBody;
use app\components\activity\models\DiscountInfo;
use app\components\activity\models\DiscountResult;

interface IDiscount {

    /**
     * 处理活动优惠，返回优惠结果
     * @param DiscountInfo $discountInfo
     * @param SettleBody $settleBody
     * @return DiscountResult $discountResult
     */
    function handleDiscount($discountInfo, $settleBody);
}