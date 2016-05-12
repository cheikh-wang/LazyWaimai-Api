<?php

namespace app\modules\v1\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%cart_extra}}".
 *
 * @property integer $id
 * @property integer $cart_id
 * @property string $name
 * @property string $description
 * @property double $price
 * @property integer $created_at
 * @property integer $updated_at
 */
class CartExtra extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%cart_extra}}';
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
            [['cart_id', 'name', 'price'], 'required'],
            [['cart_id', 'created_at', 'updated_at'], 'integer'],
            [['price'], 'number'],
            [['name'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => '主键ID',
            'cart_id' => '购物车ID',
            'name' => '名称',
            'description' => '描述',
            'price' => '价格',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields() {
        $fields = parent::fields();
        unset($fields['created_at'], $fields['updated_at'],
            $fields['id'], $fields['cart_id']);

        return $fields;
    }
}