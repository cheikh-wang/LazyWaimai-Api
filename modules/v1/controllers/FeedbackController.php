<?php

namespace app\modules\v1\controllers;

use yii;
use yii\rest\Controller;


class FeedbackController extends Controller {

    public $modelClass = 'app\modules\v1\models\Feedback';

    /**
     * @inheritdoc
     */
    protected function verbs() {
        return [
            'create' => ['POST']
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'create' => [
                'class' => 'yii\rest\CreateAction',
                'modelClass' => $this->modelClass,
            ],
        ];
    }
}