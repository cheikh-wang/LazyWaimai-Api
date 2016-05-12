<?php

namespace app\components\settle\models;

use yii;
use app\modules\v1\models\Cart;
use app\modules\v1\models\Address;

/**
 * 结算的结果
 */
class SettleResult {
    /**
     * 最近使用的地址
     * @var Address
     */
    public $last_address;

    /**
     * 支付方式
     * @var boolean
     */
    public $online_payment;

    /**
     * 可预订的时间
     * @var string[]
     */
    public $booking_time_list;

    /**
     * 购物车信息
     * @var  Cart
     */
    public $cart_info;

    /**
     * 能否提交
     * @var boolean
     */
    public $can_submit;
}