<?php

namespace app\components\settle\models;

class SettleBody {

    /**
     * 商铺ID
     * @var int
     */
    public $businessId;

    /**
     * 支付方式
     * @var int
     */
    public $payMethod;

    /**
     *  最近使用的地址ID
     * @var int
     */
    public $lastAddressId = 0;

    /**
     * 选购的商品列表
     * @var array
     */
    public $shoppingProductList;

    /**
     * 购物车的ID
     * @var int
     */
    public $cartId;

    /**
     * 商品的总价
     * @var double
     */
    public $originPrice = 0;

    /**
     * 商品的优惠价格
     * @var double
     */
    public $discountPrice = 0;

    /**
     * 商品的最终价格
     * @var double
     */
    public $totalPrice = 0;
}