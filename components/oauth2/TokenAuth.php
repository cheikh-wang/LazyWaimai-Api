<?php
/**
 * @link https://github.com/borodulin/yii2-oauth2-server
 * @copyright Copyright (c) 2015 Andrey Borodulin
 * @license https://github.com/borodulin/yii2-oauth2-server/blob/master/LICENSE
 */

namespace app\components\oauth2;

use yii\filters\auth\AuthMethod;
use app\components\oauth2\models\AccessToken;
use yii\web\UnauthorizedHttpException;

/**
 * TokenAuth is an action filter that supports the authentication method based on the OAuth2 Access Token.
 *
 * You may use TokenAuth by attaching it as a behavior to a controller or module, like the following:
 *
 * ```php
 * public function behaviors()
 * {
 *     return [
 *         'tokenAuth' => [
 *             'class' => \conquer\oauth2\TokenAuth::className(),
 *         ],
 *     ];
 * }
 * ```
 *
 * @author Andrey Borodulin
 */
class TokenAuth extends AuthMethod {

    private $_accessToken;

    /**
     * @var string the HTTP authentication realm
     */
    public $realm = 'api';

    /**
     * @var string the class name of the [[identity]] object.
     */
    public $identityClass;
    
    /**
     * @inheritdoc
     */
    public function authenticate($user, $request, $response) {
        $accessToken = $this->getAccessToken();

        /* @var $user \yii\web\User */
        /* @var $identityClass \yii\web\IdentityInterface */
        $identityClass = is_null($this->identityClass) ? $user->identityClass : $this->identityClass;

        $identity = $identityClass::findIdentity($accessToken->user_id);

        if (empty($identity)) {
            throw new UnauthorizedHttpException('User is not found.');
        }
        $user->setIdentity($identity);

        return $identity;
    }

    /**
     * @return AccessToken
     * @throws UnauthorizedHttpException
     */
    protected function getAccessToken() {
        if (is_null($this->_accessToken)) {
            $request = \Yii::$app->request;
    
            $authHeader = $request->getHeaders()->get('Authorization');
    
            $postToken = $request->post('access_token');
            $getToken = $request->get('access_token');
    
            // Check that exactly one method was used
            $methodsCount = isset($authHeader) + isset($postToken) + isset($getToken);
            if ($methodsCount > 1) {
                throw new UnauthorizedHttpException('Only one method may be used to authenticate at a time (Auth header, POST or GET).');
            } elseif ($methodsCount == 0) {
                throw new UnauthorizedHttpException('The access token was not found.');
            }
            // HEADER: Get the access token from the header
            if ($authHeader) {
                if (preg_match("/^Bearer\\s+(.*?)$/", $authHeader, $matches)) {
                    $token = $matches[1];
                } else {
                    throw new UnauthorizedHttpException('Malformed auth header.');
                }
            } else {
                // POST: Get the token from POST data
                if ($postToken) {
                    if(!$request->isPost)
                        throw new UnauthorizedHttpException('When putting the token in the body, the method must be POST.');
    
                    // IETF specifies content-type. NB: Not all web servers populate this _SERVER variable
                    if($request->contentType != 'application/x-www-form-urlencoded')
                        throw new UnauthorizedHttpException('The content type for POST requests must be "application/x-www-form-urlencoded"');
                    $token = $postToken;
                } else {
                    $token = $getToken;
                }
            }
            /** @var $accessToken AccessToken */
            if (!$accessToken = AccessToken::findOne(['access_token'=>$token])) {
                throw new UnauthorizedHttpException('无效的Access Token.');
            }
            if ($accessToken->expires < time()) {
                throw new UnauthorizedHttpException('您的登录身份已过期,请重新登录.');
            }
            $this->_accessToken = $accessToken;
        }
        return $this->_accessToken;
    }

    /**
     * @inheritdoc
     */
    public function challenge($response) {
        $response->getHeaders()->set('WWW-Authenticate', "Bearer realm=\"{$this->realm}\"");
    }
}