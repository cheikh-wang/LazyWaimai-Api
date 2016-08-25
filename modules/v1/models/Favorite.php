<?php

namespace app\modules\v1\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use app\components\filters\IdentityBehavior;

/**
 * This is the model class for table "{{%favorite}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $business_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class Favorite extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%favorite}}';
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
            [['business_id'], 'required'],
            [['user_id', 'business_id', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => '主键ID',
            'user_id' => '用户ID',
            'business_id' => '商铺ID',
            'created_at' => '添加时间',
            'updated_at' => '更新时间',
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
