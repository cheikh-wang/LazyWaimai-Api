<?php

namespace app\modules\v1\controllers;

use yii;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use app\modules\v1\models\Business;

class BusinessController extends ActiveController {

    public $modelClass = 'app\modules\v1\models\Business';

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items'
    ];

    /**
     * @inheritdoc
     */
    public function actions() {
        return [
            'index' => [
                'class' => 'yii\rest\IndexAction',
                'modelClass' => $this->modelClass,
                'prepareDataProvider' => [$this, 'prepareDataProvider'],
            ],
            'view' => [
                'class' => 'yii\rest\ViewAction',
                'modelClass' => $this->modelClass,
            ],
        ];
    }

    /**
     * 由于只需要查询出指定分类下的商家，故此不用默认提供的
     * @return ActiveDataProvider
     */
    public function prepareDataProvider() {
        $category = Yii::$app->request->getQueryParam('category', 0);
        if ($category == 0) {
            $query = Business::find();
        } else {
            $query = Business::find()->where(['category' => $category]);
        }

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}