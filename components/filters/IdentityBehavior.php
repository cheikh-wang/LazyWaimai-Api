<?php

namespace app\components\filters;

use yii;
use yii\db\BaseActiveRecord;
use yii\behaviors\AttributeBehavior;

class IdentityBehavior extends AttributeBehavior {

    public $identityAttribute = 'user_id';

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();

        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_INSERT => $this->identityAttribute,
                BaseActiveRecord::EVENT_BEFORE_UPDATE => $this->identityAttribute,
            ];
        }
    }

    /**
     * @inheritdoc
     */
    protected function getValue($event) {
        return Yii::$app->user->getId() ? : 0;
    }
}