<?php

namespace app\modules\v1\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%cart_discount}}".
 *
 * @property integer $id
 * @property integer $cart_id
 * @property integer $activity_id
 * @property string $name
 * @property double $price
 * @property string $description
 * @property string $icon_name
 * @property string $icon_color
 * @property integer $created_at
 * @property integer $updated_at
 */
class CartDiscount extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%cart_discount}}';
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
            [['cart_id', 'activity_id', 'name', 'price', 'description', 'icon_name', 'icon_color'], 'required'],
            [['cart_id', 'activity_id', 'created_at', 'updated_at'], 'integer'],
            [['price'], 'number'],
            [['name'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 100],
            [['icon_name', 'icon_color'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => '主键ID',
            'cart_id' => '购物车ID',
            'activity_id' => '活动ID',
            'name' => '活动的名称',
            'price' => '优惠的价格',
            'description' => '活动的描述',
            'icon_name' => '活动图标的文字',
            'icon_color' => '活动图标的颜色',
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
            $fields['id'], $fields['cart_id'], $fields['activity_id']);

        return $fields;
    }
}
