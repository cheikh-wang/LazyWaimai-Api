<?php
/**
 * @link https://github.com/borodulin/yii2-oauth-server
 * @copyright Copyright (c) 2015 Andrey Borodulin
 * @license https://github.com/borodulin/yii2-oauth-server/blob/master/LICENSE
 */

namespace app\components\oauth2;

use yii;
use yii\base\Action;
use yii\web\Response;
use yii\web\BadRequestHttpException;

/**
 * 
 * @author Andrey Borodulin
 *
 */
class TokenAction extends Action {
    
    public $grantTypes = [
        'password' => 'app\components\oauth2\granttypes\UserCredentials',
        'refresh_token' => 'app\components\oauth2\granttypes\RefreshToken',
    ];
    
    public function init() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->controller->enableCsrfValidation = false;
    }
    
    public function run() {
        if (!$grantType = BaseModel::getRequestValue('grant_type')) {
            throw new BadRequestHttpException('缺少参数:grant_type');
        }
        if (isset($this->grantTypes[$grantType])) {
            $grantModel = Yii::createObject($this->grantTypes[$grantType]);
        } else {
            throw new BadRequestHttpException("不支持的grant_type:" . $grantType);
        }
        
        $grantModel->validate();
        
        Yii::$app->response->data = $grantModel->getResponseData();
    }
}