<?php

namespace app\components\settle;

use app\components\settle\models\SettleBody;
use app\components\settle\models\SettleResult;

interface IDecorator {

    /**
     * @param SettleBody $settleBody
     * @param SettleResult $settleResult
     */
    function settleBefore($settleBody, $settleResult);

    /**
     * @param SettleBody $settleBody
     * @param SettleResult $settleResult
     */
    function settleAfter($settleBody, $settleResult);
}