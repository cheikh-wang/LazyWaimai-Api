<?php

namespace app\modules\v1\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "product".
 *
 * @property integer $id
 * @property integer $business_id
 * @property integer $category_id
 * @property string $name
 * @property double $price
 * @property string $description
 * @property string $image_path
 * @property integer $month_sales
 * @property integer $rate
 * @property integer $left_num
 * @property integer $created_at
 */
class Product extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%product}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['business_id', 'category_id', 'name', 'rate', 'left_num', 'created_at'], 'required'],
            [['business_id', 'category_id', 'month_sales', 'rate', 'left_num', 'created_at'], 'integer'],
            [['price'], 'number'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 100],
            [['image_path'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'business_id' => 'Business ID',
            'category_id' => 'Category ID',
            'name' => 'Name',
            'price' => 'Price',
            'description' => 'Description',
            'image_path' => 'Image Path',
            'month_sales' => 'Month Sales',
            'rate' => 'Rate',
            'left_num' => 'Left Num',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields() {
        $fields = parent::fields();
        unset($fields['created_at'], $fields['updated_at']);

        return $fields;
    }
}
