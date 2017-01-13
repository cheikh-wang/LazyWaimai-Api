<?php

namespace app\modules\v1\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "sms_record".
 *
 * @property integer $id
 * @property string $mobile
 * @property string $code
 * @property string $action_sign
 * @property integer $valid_second
 * @property integer $created_at
 */
class Code extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%code}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['mobile', 'code', 'valid_second', 'action_sign', 'created_at'], 'required'],
            [['valid_second', 'created_at'], 'integer'],
            [['mobile', 'action_sign'], 'string', 'max' => 20],
            [['code'], 'string', 'max' => 10]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'mobile' => 'Mobile',
            'code' => 'Code',
            'action_sign' => 'Action Sign',
            'valid_second' => 'Valid Second',
            'created_at' => 'Created At',
        ];
    }
}
