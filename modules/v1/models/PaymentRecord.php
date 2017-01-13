<?php

namespace app\modules\v1\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use app\components\filters\IdentityBehavior;

/**
 * This is the model class for table "{{%payment_record}}".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $user_id
 * @property integer $platform_id
 * @property double $amount
 * @property integer $updated_at
 * @property integer $created_at
 */
class PaymentRecord extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%payment_record}}';
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
            [['order_id', 'platform_id', 'amount'], 'required'],
            [['order_id', 'user_id', 'platform_id', 'updated_at', 'created_at'], 'integer'],
            [['amount'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'order_id' => '订单ID',
            'user_id' => '用户ID',
            'platform_id' => '支付平台ID',
            'amount' => '支付金额',
            'updated_at' => '修改时间',
            'created_at' => '创建时间',
        ];
    }
}
