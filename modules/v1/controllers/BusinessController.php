<?php

namespace app\modules\v1\controllers;

use yii;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use yii\web\ServerErrorHttpException;
use app\components\oauth2\TokenAuth;
use app\modules\v1\models\Business;
use app\modules\v1\models\Category;
use app\modules\v1\models\Favorite;

class BusinessController extends ActiveController {

    public $modelClass = 'app\modules\v1\models\Business';

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items'
    ];

    /**
     * @inheritdoc
     */
    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => TokenAuth::className(),
            'optional' => ['index', 'view', 'products'],
        ];

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return [
            'index' => ['GET', 'HEAD'],
            'view' => ['GET', 'HEAD'],
            'products' => ['GET', 'HEAD'],
            'favorite' => ['POST'],
        ];
    }

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
            'pagination' => [
                'pageParam' => 'page',
                'pageSizeParam' => 'size'
            ]
        ]);
    }

    /**
     * 获取某个商家下的所有商品
     * @param $businessId
     * @return array|yii\db\ActiveRecord[]
     */
    public function actionProducts($businessId) {
        return Category::findAll(['business_id' => $businessId]);
    }

    /**
     * 收藏或取消收藏商家
     * @param $businessId
     * @return Favorite|null
     * @throws ServerErrorHttpException
     * @throws \Exception
     */
    public function actionFavorite($businessId) {
        $favorite = Favorite::find()->where(['business_id' => $businessId, 'user_id' => Yii::$app->user->id])->one();
        if ($favorite != null) {
            if ($favorite->delete()) {
                Yii::$app->getResponse()->setStatusCode(204);
            } else if (!$favorite->hasErrors()) {
                throw new ServerErrorHttpException('取消收藏失败!请稍候重试');
            }

            return null;
        } else {
            $model = new Favorite();
            $model->business_id = $businessId;
            if ($model->save()) {
                $response = Yii::$app->getResponse();
                $response->setStatusCode(201);
            } else if (!$model->hasErrors()) {
                throw new ServerErrorHttpException('收藏失败!请稍候重试');
            }

            return $model;
        }
    }
}