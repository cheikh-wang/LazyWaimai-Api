<?php

namespace app\modules\v1\models;

use Yii;
use yii\base\Model;
use yii\web\ServerErrorHttpException;
use app\models\User;
use app\components\Ucpaas;


class RegisterSendSmsForm extends Model {

    public $mobile;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            ['mobile', 'trim'],
            ['mobile', 'required'],
            ['mobile', 'match', 'pattern' => '/^1[3|5|7|8|][0-9]{9}$/'],
            // 验证手机号是否存在
            ['mobile', function ($attribute, $params) {
                $user = User::findOne(['mobile' => $this->mobile]);
                if ($user) {
                    $this->addError($attribute, '该手机号已被注册');
                }
            }],
            // 验证获取验证码的频率
            ['mobile', function ($attribute, $params) {
                /** @var Code $code */
                $code = Code::find()->where(['mobile' => $this->mobile, 'action_sign' => 'register'])
                    ->orderBy('created_at DESC')->limit(1)->one();
                if ($code && time() < $code->created_at + 30) {
                    $this->addError($attribute, '发送验证码过于频繁');
                }
            }]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'mobile' => '手机号',
        ];
    }

    /**
     * 发送短信验证码
     * @param bool $runValidation
     * @return bool
     * @throws ServerErrorHttpException
     */
    public function sendSms($runValidation = true) {
        if ($runValidation && !$this->validate()) {
            return false;
        }

        $verifyCode = (string) mt_rand(100000, 999999);
        $validMinutes = 30;

        $model = new Code();
        $model->mobile = $this->mobile;
        $model->code = $verifyCode;
        $model->action_sign = 'register';
        $model->valid_second = $validMinutes * 60;
        $model->created_at = time();
        if ($model->save() === false) {
            $this->addError('mobile', '验证码发送失败,请重试');
            return false;
        }

        // 调用云之讯组件发送模板短信
        /** @var $ucpass Ucpaas */
        $ucpass = Yii::$app->ucpass;
        $ucpass->templateSMS($this->mobile, $verifyCode.','.$validMinutes);
        if ($ucpass->state == Ucpaas::STATUS_SUCCESS) {
            return true;
        } else {
            $this->addError('mobile', $ucpass->message);
            return false;
        }
    }
}