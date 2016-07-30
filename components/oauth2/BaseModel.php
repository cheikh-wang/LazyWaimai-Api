<?php

namespace app\components\oauth2;

use yii\base\Model;
use yii\helpers\ArrayHelper;
use app\components\oauth2\models\Client;
use yii\web\BadRequestHttpException;

abstract class BaseModel extends Model {

    protected $_client;
    
    /**
     * @link https://tools.ietf.org/html/rfc6749#section-7.1
     * @var string
     */
    public $tokenType = 'bearer';
    
    /**
     * Authorization Code lifetime
     * 30 seconds by default
     * @var integer
     */
    public $authCodeLifetime = 30;
    
    /**
     * Access Token lifetime
     * 1 weeks by default
     * @var integer
     */
    public $accessTokenLifetime = 604800;
    
    /**
     * Refresh Token lifetime
     * 2 weeks by default
     * @var integer
     */
    public $refreshTokenLifetime = 1209600;
    
    
    public function init() {
        $headers = [
            'client_id' => 'PHP_AUTH_USER',
            'client_secret' => 'PHP_AUTH_PW',
        ];
        
        foreach ($this->safeAttributes() as $attribute) {
            $this->$attribute = self::getRequestValue($attribute, ArrayHelper::getValue($headers, $attribute));
        }
    }
    
    public function addError($attribute, $error = "") {
        throw new Exception($error, Exception::INVALID_REQUEST);
    }
    
    public function errorServer($error, $type = Exception::INVALID_REQUEST) {
        throw new Exception($error, $type);
    }
    
    public function errorRedirect($error, $type = Exception::INVALID_REQUEST) {
        $redirectUri = isset($this->redirect_uri) ? $this->redirect_uri : $this->getClient()->redirect_uri;
        if ($redirectUri) {
            throw new RedirectException($redirectUri, $error, $type, isset($this->state)?$this->state:null);
        } else {
            throw new Exception($error, $type);
        }
    }
    
    abstract function getResponseData();
    
    public static function getRequestValue($param, $header = null) {
        static $request;
        if (is_null($request)) {
            $request = \Yii::$app->request;
        }
        if ($header && ($result = $request->headers->get($header))) {
            return $result;
        } else {
            return $request->post($param, $request->get($param));
        }
    }

    /**
     * @return Client
     * @throws BadRequestHttpException
     */
    public function getClient() {
        if (is_null($this->_client)) {
            if (empty($this->client_id)) {
                throw new BadRequestHttpException('缺少参数:client_id');
            }
            if (!$this->_client = Client::findOne(['client_id' => $this->client_id])) {
                throw new BadRequestHttpException('无效的client id');
            }
        }
        return $this->_client;
    }
    
    public function validateClient_id() {
        $this->getClient();
    }
    
    public function validateClient_secret($attribute) {
        if (!\Yii::$app->security->compareString($this->getClient()->client_secret, $this->$attribute)) {
            throw new BadRequestHttpException('无效的client secret');
        }
    }
    
    public function validateRedirect_uri($attribute) {
        if (!empty($attribute)){
            $clientRedirectUri = $this->getClient()->redirect_uri;
            if (strncasecmp($attribute, $clientRedirectUri, strlen($clientRedirectUri))!==0) {
                $this->errorServer('The redirect URI provided is missing or does not match', Exception::REDIRECT_URI_MISMATCH);
            }
        }
    }
    
    public function validateScope($attribute) {
        if (!$this->checkSets($attribute, $this->getClient()->scope)) {
            $this->errorRedirect('The requested scope is invalid, unknown, or malformed', Exception::INVALID_SCOPE);
        }
    }
    
    /**
     * Checks if everything in required set is contained in available set.
     *
     * @param string|array $requiredSet
     * @param string|array $availableSet
     * @return boolean
     */
    protected function checkSets($requiredSet, $availableSet) {
        if (!is_array($requiredSet)) {
            $requiredSet = explode(' ', trim($requiredSet));
        }
        if (!is_array($availableSet)) {
            $availableSet = explode(' ', trim($availableSet));
        }
        return (count(array_diff($requiredSet, $availableSet)) == 0);
    }
}