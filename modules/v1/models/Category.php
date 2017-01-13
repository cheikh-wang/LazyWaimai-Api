<?php

namespace app\modules\v1\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "category".
 *
 * @property integer $id
 * @property integer $business_id
 * @property string $name
 * @property string $description
 * @property string $icon_url
 * @property integer $created_at
 */
class Category extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%category}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['business_id', 'name', 'created_at'], 'required'],
            [['business_id', 'created_at'], 'integer'],
            [['name'], 'string', 'max' => 30],
            [['description'], 'string', 'max' => 50],
            [['icon_url'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'business_id' => 'Business ID',
            'name' => 'Name',
            'description' => 'Description',
            'icon_url' => 'Icon Url',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields() {
        return [
            'id',
            'business_id',
            'name',
            'description',
            'icon_url',
            'products' => function($model) {
                return Product::findAll(['category_id' => $model->id]);
            }
        ];
    }
}