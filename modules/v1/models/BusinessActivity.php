<?php

namespace app\modules\v1\models;;

use Yii;
use yii\db\Query;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use app\components\activity\models\DiscountInfo;

/**
 * This is the model class for table "business_activity".
 *
 * @property integer $id
 * @property integer $business_id
 * @property integer $activity_id
 * @property string $attribute
 * @property integer $created_at
 * @property integer $updated_at
 */
class BusinessActivity extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%business_activity}}';
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
            [['business_id', 'activity_id', 'attribute'], 'required'],
            [['business_id', 'activity_id', 'created_at', 'updated_at'], 'integer'],
            [['attribute'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'business_id' => 'Business ID',
            'activity_id' => 'Activity ID',
            'attribute' => 'Attribute',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * 获取指定商家下的优惠信息
     * @param $businessId
     * @return DiscountInfo[]
     */
    public static function discountInfoList($businessId) {
        $query = new Query();
        $data = $query->select([
                'a.id',
                'b.name',
                'b.description',
                'a.attribute',
                'b.icon_name',
                'b.icon_color',
                'b.code',
                'b.is_share',
                'b.priority'
            ])
            ->from('business_activity a')
            ->innerJoin('activity b', 'a.activity_id=b.id')
            ->where(['a.business_id' => $businessId])
            ->orderBy('b.priority')
            ->all();
        $discountInfoList = [];
        foreach ($data as $value) {
            $discountInfo = new DiscountInfo();
            $discountInfo->id = $value['id'];
            $discountInfo->name = $value['name'];
            $discountInfo->code = $value['code'];
            $discountInfo->attribute = $value['attribute'];
            $discountInfo->icon_name = $value['icon_name'];
            $discountInfo->icon_color = $value['icon_color'];
            $discountInfo->is_share = $value['is_share'] === 1 ? true : false;
            $discountInfo->priority = $value['priority'];

            array_push($discountInfoList, $discountInfo);
        }

        return $discountInfoList;
    }
}
