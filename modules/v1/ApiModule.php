<?php

namespace app\modules\v1;

use Yii;
use yii\base\Module;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use app\components\helpers\Constants;
use app\components\oauth2\models\Client;


class ApiModule extends Module {

    const HASH_ALGO = 'sha256';

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\v1\controllers';

    /**
     * @inheritdoc
     */
    public function beforeAction($action) {
        if (parent::beforeAction($action)) {
            $httpTimeStamp = Yii::$app->request->headers->get(Constants::HTTP_TIMESTAMP);
            $versionName = Yii::$app->request->headers->get(Constants::HTTP_APP_VERSION);
            $deviceId = Yii::$app->request->headers->get(Constants::HTTP_DEVICE_ID);
            $requestType = Yii::$app->request->headers->get(Constants::HTTP_DEVICE_TYPE);
            $signature = Yii::$app->request->headers->get(Constants::HTTP_SIGNATURE);
            $appKey = Yii::$app->request->headers->get(Constants::HTTP_APP_KEY);

            if ($httpTimeStamp === null || $versionName === null
                || $deviceId === null || $requestType === null
                || $signature === null || $appKey === null) {
                throw new NotFoundHttpException('非法访问.');
            }

            if (!$this->verifySignature($signature, $appKey)) {
                throw new BadRequestHttpException("无效的请求参数");
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * 验证请求签名
     * @param $signature
     * @param $appKey
     * @return bool
     */
    private function verifySignature($signature, $appKey) {
        return $signature === $this->getSignature($appKey);
    }

    /**
     * 获取请求签名
     * @param $appKey
     * @return string
     */
    private function getSignature($appKey) {
        $client = Client::find()->where(['client_id' => $appKey])->one();
        if ($client == null) {
            return '';
        }

        // 收集参数
        $params = [];
        $this->collectQueryParameters($params);
        $this->collectBodyParameters($params);

        // 生成源串
        $path = $this->getRequestPath();
        $serialParameters = $this->getSerialParameters($params, false);
        $source = $path . '&' . $serialParameters . '&' . $client['client_secret'];

        // 使用HMAC-SHA1算法将源串进行加密
        $encrypted = hash_hmac(self::HASH_ALGO, $source, $client['client_secret']);

        // 将加密后的字符串进行Base64编码
        $signature = base64_encode($encrypted);

        return $signature;
    }

    /**
     * 手机get请求参数
     * @param $param
     */
    private function collectQueryParameters(&$param) {
        $url = Yii::$app->request->getUrl();
        $q = strpos($url, '?');
        if ($q > 0) {
            $param += $this->decodeForm(substr($url, $q + 1));
        }
    }

    /**
     * 收集post请求参数
     * @param $params
     */
    private function collectBodyParameters(&$params) {
        $contentType = Yii::$app->request->getContentType();
        if ($contentType == 'application/x-www-form-urlencoded') {
            $params += Yii::$app->request->getBodyParams();
        }
    }

    /**
     * 获取请求路径
     * @return string
     */
    private function getRequestPath() {
        $path = Yii::$app->request->getPathInfo();
        // 只保留版本号及以后的内容为路径
        if (preg_match_all('/v\d+/', $path, $matches)) {
            $str = $matches[0][0];
            $path = substr($path, strpos($path, $str));
        }

        return $path;
    }

    /**
     * 获取键值对形式的参数字符串
     * @param $params
     * @param $onlySerialValue
     * @return string
     */
    private function getSerialParameters($params, $onlySerialValue) {
        if (empty($params)) {
            return '';
        }
        // 将所有参数按照key进行字典序升序排列
        ksort($params);
        $i = 0;
        $result = '';
        foreach ($params as $key => $value) {
            if ($i > 0) {
                $result .= '&';
            }
            if ($onlySerialValue) {
                $result .= $key."=".rawurlencode($value); // rawurlencode()将空格转义成%20，urlencode()将空格转化为+
            } else {
                $result .= $key."=".$value;
            }
            $i++;
        }
        if (!$onlySerialValue) {
            return rawurlencode($result);
        }
        return $result;
    }

    /**
     * 从字符串中解析出参数对
     * @param $form
     * @return array
     */
    private function decodeForm($form) {
        $params = array();
        if (empty($form)) {
            return $params;
        }
        foreach (explode('&', $form) as $nvp)  {
            $equals = strpos($nvp, '=');
            if ($equals < 0) {
                $name = $nvp;
                $value = null;
            } else {
                $name = substr($nvp, 0, $equals);
                $value = substr($nvp, $equals + 1);
            }
            $params[$name] = $value;
        }
        return $params;
    }
}