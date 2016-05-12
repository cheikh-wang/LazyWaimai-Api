<?php

namespace app\components\settle\decorators;

use yii;
use Exception;
use app\components\settle\IDecorator;
use app\components\settle\models\SettleBody;
use app\components\settle\models\SettleResult;
use app\modules\v1\models\Product;
use app\modules\v1\models\CartProduct;

class ProductSettleDecorator implements IDecorator {

    /**
     * @var CartProduct[]
     */
    private $cartProducts;

    /**
     * @param SettleBody $settleBody
     * @param SettleResult $settleResult
     */
    public function settleBefore($settleBody, $settleResult) {
        $shoppingProductList = $settleBody->shoppingProductList;
        foreach ($shoppingProductList as $shoppingProduct) {
            /** @var $product Product */
            $product = Product::findOne($shoppingProduct['id']);

            $cartProduct = new CartProduct();
            $cartProduct->product_id = $product->id;
            $cartProduct->name = $product->name;
            $cartProduct->quantity = $shoppingProduct['quantity'];
            $cartProduct->unit_price = $product->price;
            $cartProduct->total_price = $product->price * $shoppingProduct['quantity'];
            $this->cartProducts[] = $cartProduct;

            // 累加商品总价
            $settleBody->originPrice += $cartProduct->total_price;
        }
    }

    /**
     * @param SettleBody $settleBody
     * @param SettleResult $settleResult
     */
    public function settleAfter($settleBody, $settleResult) {
        // 使用事务来进行保存
        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($this->cartProducts as $boughtProduct) {
                /** @var $boughtProduct CartProduct */
                $boughtProduct->cart_id = $settleBody->cartId;
                if (!$boughtProduct->save()) {
                    throw new Exception('存储选购的商品出错.');
                }
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
        }
    }
}