<?php

namespace app\modules\v1\controllers;

use yii;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use app\modules\v1\models\Product;
use app\modules\v1\models\Category;

class ProductsController extends Controller {

    public function actionIndex($parent_ctl, $parent_id) {
        if ($parent_ctl !== 'businesses') {
            throw new NotFoundHttpException();
        }

        $categorys = Category::find()->where(['business_id' => $parent_id])->asArray()->all();
        if ($categorys === null) {
            $categorys = [];
        } else {
            foreach ($categorys as &$category) {
                $products = Product::findAll(['category_id' => $category['id']]);
                if ($products === null) {
                    $products = array();
                }
                $category['products'] = $products;
            }
        }

        return $categorys;
    }
}