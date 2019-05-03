<?php

namespace app\modules\v1\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\AttributeTypecastBehavior;
use app\components\filters\IdentityBehavior;

/**
 * This is the model class for table "address".
 *
 * @property integer $id
 * @property string $summary
 * @property string $detail
 * @property string $phone
 * @property string $name
 * @property integer $gender
 * @property integer $user_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class Address extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%address}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            AttributeTypecastBehavior::className(),
            TimestampBehavior::className(),
            IdentityBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['summary', 'detail', 'phone', 'name', 'gender'], 'required'],
            [['gender', 'user_id', 'created_at', 'updated_at'], 'integer'],
            [['summary'], 'string', 'max' => 50],
            [['detail'], 'string', 'max' => 200],
            [['phone', 'name'], 'string', 'max' => 20]
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'summary' => 'Summary',
            'detail' => 'Detail',
            'phone' => 'Phone',
            'name' => 'Name',
            'gender' => 'Gender',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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
