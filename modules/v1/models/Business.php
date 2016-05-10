<?php

namespace app\modules\v1\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "business".
 *
 * @property integer $id
 * @property string $name
 * @property string $phone
 * @property string $address
 * @property string $pic_url
 * @property integer $status
 * @property string $opening_time
 * @property double $shipping_fee
 * @property double $package_fee
 * @property double $min_price
 * @property string $shipping_time
 * @property integer $month_sales
 * @property string $bulletin
 * @property integer $category
 * @property string $booking_times
 * @property integer $updated_at
 * @property integer $created_at
 */
class Business extends ActiveRecord {

    const STATUS_OPEN = 1;
    const STATUS_CLOSE = 0;

    public $image;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%business}}';
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
            [['name', 'phone'], 'required'],
            [['status', 'month_sales', 'category', 'updated_at', 'created_at'], 'integer'],
            [['opening_time', 'booking_times'], 'string'],
            [['shipping_fee', 'package_fee', 'min_price'], 'number'],
            [['name'], 'string', 'max' => 50],
            [['phone', 'shipping_time'], 'string', 'max' => 20],
            [['address'], 'string', 'max' => 100],
            [['pic_url'], 'string', 'max' => 200],
            [['bulletin'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'name' => '店名',
            'phone' => '联系电话',
            'address' => '地址',
            'status' => '营业状态',
            'opening_time' => '营业时间',
            'pic_url' => 'Logo地址',
            'image' => '店铺LOGO',
            'shipping_fee' => '配送费',
            'package_fee' => '打包费',
            'min_price' => '起送价',
            'shipping_time' => '平均配送时间',
            'month_sales' => '月销量',
            'bulletin' => '公告',
            'category' => '分类',
            'booking_times' => '可预订的时间段',
            'updated_at' => '最近修改时间',
            'created_at' => '创建时间',
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields() {
        $fields = parent::fields();
        unset($fields['booking_times'], $fields['created_at'], $fields['updated_at']);

        return $fields;
    }
}