<?php

namespace app\modules\v1\controllers;

use yii;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use yii\web\ForbiddenHttpException;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;
use app\models\User;
use app\components\helpers\Constants;
use app\modules\v1\models\Code;
use app\components\oauth2\TokenAuth;
use app\modules\v1\models\Favorite;
use app\modules\v1\models\RegisterSendSmsForm;

class UserController extends ActiveController {

    public $modelClass = 'app\models\User';

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
            'optional' => ['create'],
        ];

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    protected function verbs() {
        return [
            'view' => ['GET', 'HEAD'],
            'create' => ['POST'],
            'update' => ['PUT', 'PATCH'],
            'favorites' => ['GET'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions() {
        return [
            'view' => [
                'class' => 'yii\rest\ViewAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'update' => [
                'class' => 'yii\rest\UpdateAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->updateScenario,
            ],
        ];
    }

    /**
     * 注册新用户
     * @return array|null|yii\db\ActiveRecord
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @throws ServerErrorHttpException
     */
    public function actionCreate() {
        // 获取操作参数
        $action = Yii::$app->request->getQueryParam('action');
        if (empty($action)) {
            throw new BadRequestHttpException('缺少必需的参数: action');
        }
        // 根据操作参数进行分发处理
        if ($action == 'send_code') {
            return $this->_sendSmsCode();
        } else if ($action == 'verify_code') {
            return $this->_verifySmsCode();
        } else if ($action == 'create_user') {
            return $this->_createUser();
        } else {
            throw new BadRequestHttpException('不支持的action:'.$action);
        }
    }

    /**
     * 注册时发送短信验证码
     * @throws BadRequestHttpException
     */
    private function _sendSmsCode() {
        $form = new RegisterSendSmsForm();
        $form->mobile = Yii::$app->request->getBodyParam('mobile');
        if ($form->sendSms()) {
            return true;
        } else {
            $message = $form->getFirstError('mobile');
            throw new BadRequestHttpException($message);
        }
    }

    /**
     * 注册时检查短信验证码正确性
     * @return bool
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    private function _verifySmsCode() {
        // 获取参数
        $mobile = Yii::$app->request->getBodyParam('mobile');
        $code = Yii::$app->request->getBodyParam('code');
        if (empty($mobile)) {
            throw new BadRequestHttpException('缺少必需的参数: mobile');
        } else if (empty($code)) {
            throw new BadRequestHttpException('缺少必需的参数: code');
        }
        // 获取验证码的信息
        $model = Code::findOne([
            'mobile' => $mobile,
            'action_sign' => 'register',
            'code' => $code
        ]);
        if ($model === null) {
            throw new ForbiddenHttpException('验证码输入错误,请重新输入');
        }
        // 判断是否失效
        if (time() - $model['created_at'] > $model['valid_second']) {
            throw new ForbiddenHttpException('您的验证码已过期,请重新获取');
        }

        return true;
    }

    /**
     * 注册时创建用户账户
     * @return User
     * @throws BadRequestHttpException
     * @throws ServerErrorHttpException
     */
    private function _createUser() {
        // 获取参数
        $mobile = Yii::$app->request->getBodyParam('mobile');
        $password = Yii::$app->request->getBodyParam('password');
        if (empty($mobile)) {
            throw new BadRequestHttpException('缺少必需的参数: mobile');
        } else if (empty($password)) {
            throw new BadRequestHttpException('缺少必需的参数: password');
        }
        // 创建新用户并存到数据库
        $model = new User();
        $model->mobile = $mobile;
        $model->setPassword($password);
        $model->avatar_url = Yii::$app->params[Constants::USER_DEFAULT_AVATAR];
        $model->nickname = 'lazy'.Yii::$app->security->generateRandomString(8); // 随机生成昵称
        if ($model->save() === false) {
            throw new ServerErrorHttpException('系统异常,请重试');
        }

        return true;
    }

    /**
     * 获取所有收藏
     * @return ActiveDataProvider
     */
    public function actionFavorites() {
        return new ActiveDataProvider([
            'query' => Favorite::find()->where(['user_id' => Yii::$app->user->id]),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function checkAccess($action, $model = null, $params = []) {
        if ($model !== null && $model['id'] !== Yii::$app->user->id) {
            throw new ForbiddenHttpException('You do not have permission to operate this resource.');
        }
    }
}