<?php

namespace app\modules\v1\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%payment_platform}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $icon
 * @property integer $updated_at
 * @property integer $created_at
 */
class PaymentPlatform extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%payment_platform}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name', 'icon'], 'required'],
            [['updated_at', 'created_at'], 'integer'],
            [['name'], 'string', 'max' => 20],
            [['icon'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'name' => '平台名称',
            'icon' => '平台图标',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields() {
        return [
            'id',
            'name',
            'icon',
        ];
    }
}
