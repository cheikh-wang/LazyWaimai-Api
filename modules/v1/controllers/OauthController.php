<?php

namespace app\modules\v1\controllers;

use yii\rest\Controller;
use app\components\oauth2\TokenAction;

class OauthController extends Controller {

    /**
     * @inheritdoc
     */
    protected function verbs() {
        return [
            'access_token' => ['POST'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions() {
        return [
            'access_token' => [
                'class' => TokenAction::classname(),
            ],
        ];
    }
}