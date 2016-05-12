<?php

namespace app\components\activity\models;

/**
 * 优惠信息类
 * @property integer id
 * @property string name
 * @property string description
 * @property double price
 * @property string code
 * @property string attribute
 * @property string icon_name
 * @property string icon_color
 * @property boolean is_share
 * @property integer priority
 */
class DiscountInfo {
    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var double
     */
    public $price;

    /**
     * @var string
     */
    public $code;

    /**
     * @var string
     */
    public $attribute;

    /**
     * @var string
     */
    public $icon_name;

    /**
     * @var string
     */
    public $icon_color;

    /**
     * @var boolean
     */
    public $is_share;

    /**
     * @var integer
     */
    public $priority;
}