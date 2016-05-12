<?php

namespace app\components\settle;

use yii;
use yii\web\BadRequestHttpException;
use app\components\settle\models\SettleBody;
use app\components\settle\models\SettleResult;

class CartSettleHandler {

    /**
     * @var SettleBody
     */
    private $settleBody;

    /**
     * @var SettleResult
     */
    private $settleResult;

    /**
     * @var IDecorator[]
     */
    private $decorators = [];

    /**
     * SettleHandler constructor.
     * @param $settleBody
     */
    public function __construct($settleBody) {
        $this->settleBody = $settleBody;

        $this->settleResult = new SettleResult();
    }

    /**
     * 添加结算项目的装配器
     * @param IDecorator $decorator
     */
    public function addDecorator(IDecorator $decorator) {
        $this->decorators[] = $decorator;
    }

    /**
     * 结算之前
     */
    private function settleBefore() {
        foreach ($this->decorators as $decorator) {
            /** @var $decorator IDecorator */
            $decorator->settleBefore($this->settleBody, $this->settleResult);
        }
    }

    /**
     * 结算之后调用
     */
    private function settleAfter() {
        foreach ($this->decorators as $decorator) {
            /** @var $decorator IDecorator */
            $decorator->settleAfter($this->settleBody, $this->settleResult);
        }
    }

    /**
     * 处理购物车结算
     * @return SettleResult
     * @throws BadRequestHttpException
     */
    public function handleCartSettle() {
        $this->settleBefore();

        // nothing to do!

        $this->settleAfter();

        return $this->settleResult;
    }
}