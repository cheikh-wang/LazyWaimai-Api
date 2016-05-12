<?php

namespace app\modules\v1\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%cart}}".
 *
 * @property integer $id
 * @property integer $business_id
 * @property integer $last_address_id
 * @property string $pay_method
 * @property double $origin_price
 * @property double $discount_price
 * @property double $total_price
 * @property integer $created_at
 * @property integer $updated_at
 */
class Cart extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%cart}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['business_id', 'last_address_id', 'pay_method', 'origin_price', 'discount_price', 'total_price'], 'required'],
            [['business_id', 'last_address_id', 'created_at', 'updated_at'], 'integer'],
            [['pay_method'], 'string'],
            [['origin_price', 'discount_price', 'total_price'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => '购物车ID',
            'business_id' => '商铺ID',
            'last_address_id' => '地址ID',
            'pay_method' => '支付方式',
            'origin_price' => '原价',
            'discount_price' => '优惠价',
            'total_price' => '总价',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields() {
        return [
            'id',
            'origin_price',
            'discount_price',
            'total_price',
            'business_info' => function ($model) {
                return Business::findOne($model->business_id);
            },
            'product_list' => function($model) {
                return CartProduct::find()->where(['cart_id' => $model->id])->all();
            },
            'extra_fee_list' => function($model) {
                return CartExtra::find()->where(['cart_id' => $model->id])->all();
            },
            'discount_list' => function($model) {
                return CartDiscount::find()->where(['cart_id' => $model->id])->all();
            },
        ];
    }
}