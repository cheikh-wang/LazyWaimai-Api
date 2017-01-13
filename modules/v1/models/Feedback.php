<?php

namespace app\modules\v1\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%feedback}}".
 *
 * @property integer $id
 * @property string $content
 * @property string $contact_way
 * @property integer $created_at
 * @property integer $updated_at
 */
class Feedback extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%feedback}}';
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
    public function rules()
    {
        return [
            [['content'], 'required'],
            [['content'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
            [['contact_way'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'content' => '反馈内容',
            'contact_way' => '联系方式',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
