<?php

namespace app\components\activity;

use app\components\settle\models\SettleBody;
use app\components\activity\models\DiscountInfo;
use app\components\activity\models\DiscountResult;
use app\components\activity\types\FirstOrderDiscount;
use app\components\activity\types\FullCutDiscount;

/**
 * 优惠的代理类
 * Class DiscountContext
 * @package app\components\discount
 */
class DiscountContext {

    const FULL_CUT = "FULL_CUT";
    const FIRST_ORDER = 'FIRST_ORDER';

    private $discountManager;

    public function __construct($code) {
        switch ($code) {
            case self::FULL_CUT:
                $this->discountManager = new FullCutDiscount();
                break;
            case self::FIRST_ORDER:
                $this->discountManager = new FirstOrderDiscount();
                break;
        }
    }

    /**
     * 处理活动优惠的代理方法
     * @param DiscountInfo $discountInfo
     * @param SettleBody $settleBody
     * @return DiscountResult $discountResult
     */
    public function handleDiscount($discountInfo, $settleBody) {
        return $this->discountManager->handleDiscount($discountInfo, $settleBody);
    }
}