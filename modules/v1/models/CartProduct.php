<?php

namespace app\modules\v1\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%cart_product}}".
 *
 * @property integer $id
 * @property integer $cart_id
 * @property integer $product_id
 * @property string $name
 * @property integer $quantity
 * @property double $unit_price
 * @property double $total_price
 * @property integer $created_at
 * @property integer $updated_at
 */
class CartProduct extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%cart_product}}';
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
            [['cart_id', 'product_id', 'name', 'quantity', 'unit_price', 'total_price'], 'required'],
            [['cart_id', 'product_id', 'quantity', 'created_at', 'updated_at'], 'integer'],
            [['unit_price', 'total_price'], 'number'],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => '主键ID',
            'cart_id' => '购物车ID',
            'product_id' => '商品ID',
            'name' => '商品名称',
            'quantity' => '商品数量',
            'unit_price' => '商品单价',
            'total_price' => '商品总价',
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
            'name',
            'quantity',
            'unit_price',
            'total_price',
            'product' => function ($model) {
                return Product::findOne($model->product_id);
            },
        ];
    }
}