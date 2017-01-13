<?php

namespace app\modules\v1\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use app\components\filters\IdentityBehavior;

/**
 * This is the model class for table "{{%order}}".
 *
 * @property integer $id
 * @property integer $cart_id
 * @property integer $business_id
 * @property integer $user_id
 * @property string $order_num
 * @property integer $status
 * @property double $origin_price
 * @property double $discount_price
 * @property double $total_price
 * @property string $consignee
 * @property string $phone
 * @property string $address
 * @property integer $pay_method
 * @property string $remark
 * @property string $booked_at
 * @property integer $created_at
 * @property integer $updated_at
 */
class Order extends ActiveRecord {

    const STATUS_WAIT_SUBMIT = -1;  // 待提交，默认状态
    const STATUS_WAIT_PAYMENT = 0;  // 待支付
    const STATUS_WAIT_ACCEPT = 1;   // 待接单
    const STATUS_WAIT_SEND = 2;     // 待发货
    const STATUS_WAIT_ARRIVE = 3;   // 待送达
    const STATUS_WAIT_CONFIRM = 4;  // 待确认
    const STATUS_FINISHED = 5;      // 已完成

    const PAYMENT_ONLINE = 1;
    const PAYMENT_OFFLINE = 0;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%order}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            TimestampBehavior::className(),
            IdentityBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['cart_id', 'business_id', 'order_num', 'status', 'pay_method', 'origin_price', 'discount_price', 'total_price', 'consignee', 'phone', 'address'], 'required'],
            [['cart_id', 'business_id', 'user_id', 'pay_method', 'status', 'created_at', 'updated_at'], 'integer'],
            [['origin_price', 'discount_price', 'total_price'], 'number'],
            [['order_num'], 'string', 'max' => 50],
            [['consignee', 'phone', 'booked_at'], 'string', 'max' => 20],
            [['address'], 'string', 'max' => 100],
            [['remark'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => '订单ID',
            'cart_id' => '购物车ID',
            'business_id' => '商铺Id',
            'user_id' => '用户ID',
            'order_num' => '订单编号',
            'status' => '订单状态',
            'origin_price' => '商品原价',
            'discount_price' => '优惠价格',
            'total_price' => '合计价格',
            'consignee' => '联系人',
            'phone' => '联系电话',
            'address' => '收货地址',
            'pay_method' => '支付方式',
            'remark' => '备注',
            'booked_at' => '预订时间',
            'created_at' => '下单时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * @inheritdoc
     */
    public function extraFields() {
        return ['business_info', 'cart_info'];
    }

    public function getBusiness_info() {
        return $this->hasOne(Business::className(), ['id' => 'business_id']);
    }

    public function getCart_info() {
        return $this->hasOne(Cart::className(), ['id' => 'cart_id']);
    }
}
