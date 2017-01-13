<?php

namespace app\modules\v1\controllers;

use yii;
use yii\rest\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;
use app\modules\v1\models\Order;
use app\modules\v1\models\PaymentRecord;
use app\components\oauth2\TokenAuth;

class PaymentController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => TokenAuth::className()
        ];

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    protected function verbs() {
        return [
            'create' => ['POST']
        ];
    }

    /**
     * 订单提交
     * @return Order $order
     * @throws BadRequestHttpException
     * @throws ServerErrorHttpException
     */
    public function actionCreate() {
        $model = new PaymentRecord();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->save()) {
            /** @var Order $order */
            $order = Order::findOne($model->order_id);
            $order->status = Order::STATUS_WAIT_ACCEPT;
            if ($order->save()) {
                $response = Yii::$app->getResponse();
                $response->setStatusCode(201);
            } else {
                throw new ServerErrorHttpException('系统出现异常，请稍后重试');
            }
        } else {
            throw new ServerErrorHttpException('系统出现异常，请稍后重试');
        }

        return $model;
    }


    /**
     * 检查登录的用户是否有权限
     * @param string $action
     * @param null $model
     * @param array $params
     * @throws ForbiddenHttpException
     */
    public function checkAccess($action, $model = null, $params = []) {
        if ($model !== null && $model['user_id'] != Yii::$app->user->getId()) {
            throw new ForbiddenHttpException('You do not have permission to operate this resource.');
        }
    }
}