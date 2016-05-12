<?php

namespace app\components\activity\models;

/**
 * 优惠结果类
 * @property boolean is_valid
 * @property boolean is_share
 * @property string description
 * @property double price
 */
class DiscountResult {
    /**
     * @var boolean
     */
    public $is_valid;

    /**
     * @var boolean
     */
    public $is_share;

    /**
     * @var string
     */
    public $description;

    /**
     * @var double
     */
    public $price;
}