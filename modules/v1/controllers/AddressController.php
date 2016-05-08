<?php

namespace app\modules\v1\controllers;

use yii;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use app\modules\v1\models\Address;
use yii\web\ForbiddenHttpException;
use app\components\oauth2\TokenAuth;

class AddressController extends ActiveController {

    public $modelClass = 'app\modules\v1\models\Address';

    /**
     * @inheritdoc
     */
    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => TokenAuth::className(),
        ];

        return $behaviors;
    }

    /**
     * 重写Index行为方法，自定义DataProvider
     * @inheritdoc
     */
    public function actions() {
        $actions = parent::actions();
        $actions['index'] = [
            'class' => 'yii\rest\IndexAction',
            'modelClass' => $this->modelClass,
            'checkAccess' => [$this, 'checkAccess'],
            'prepareDataProvider' => [$this, 'prepareDataProvider'],
        ];

        return $actions;
    }

    /**
     * 由于只能查询出登录用户的地址，且不用分页，故需要自定义DataProvider
     * @return ActiveDataProvider
     */
    public function prepareDataProvider() {
        return new ActiveDataProvider([
            'query' => Address::find()->where(['user_id' => Yii::$app->user->id]),
            'pagination' => false
        ]);
    }

    /**
     * 检查登录的用户是否有操作此地址的权限
     * @param string $action
     * @param null $model
     * @param array $params
     * @throws ForbiddenHttpException
     */
    public function checkAccess($action, $model = null, $params = []) {
        if ($model !== null && $model['user_id'] !== Yii::$app->user->id) {
            throw new ForbiddenHttpException('You do not have permission to operate this resource.');
        }
    }
}