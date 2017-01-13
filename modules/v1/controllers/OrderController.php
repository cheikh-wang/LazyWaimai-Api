<?php

namespace app\modules\v1\controllers;

use yii;
use app\modules\v1\models\Cart;
use app\modules\v1\models\Address;
use yii\web\ForbiddenHttpException;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use app\components\oauth2\TokenAuth;
use app\components\helpers\StringUtil;
use app\modules\v1\models\Order;
use app\components\settle\models\SettleBody;
use app\components\settle\models\SettleResult;
use app\components\settle\CartSettleHandler;
use app\components\settle\decorators\BasicSettleDecorator;
use app\components\settle\decorators\CartInfoSettleDecorator;
use app\components\settle\decorators\DiscountSettleDecorator;
use app\components\settle\decorators\ExtraFeeSettleDecorator;
use app\components\settle\decorators\ProductSettleDecorator;
use app\components\settle\decorators\BookingTimeSettleDecorator;


class OrderController extends ActiveController {

    public $modelClass = 'app\modules\v1\models\Order';

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items'
    ];

    /**
     * 重写行为方法，自定义身份认证类
     * @inheritdoc
     */
    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => TokenAuth ::className(),
        ];

        return $behaviors;
    }

    /**
     * 重写此方法，更改prepareDataProvider
     * @inheritdoc
     */
    public function actions() {
        $actions['index'] = [
            'class' => 'yii\rest\IndexAction',
            'modelClass' => $this->modelClass,
            'checkAccess' => [$this, 'checkAccess'],
            'prepareDataProvider' => [$this, 'prepareDataProvider'],
        ];
        $actions['view'] = [
            'class' => 'yii\rest\ViewAction',
            'modelClass' => $this->modelClass,
            'checkAccess' => [$this, 'checkAccess'],
        ];
        $actions['update'] = [
            'class' => 'yii\rest\UpdateAction',
            'modelClass' => $this->modelClass,
            'checkAccess' => [$this, 'checkAccess'],
            'scenario' => $this->updateScenario,
        ];

        return $actions;
    }

    /**
     * @inheritdoc
     */
    protected function verbs() {
        return [
            'index' => ['GET'],
            'view' => ['GET'],
            'create' => ['POST'],
            'update' => ['PUT', 'PATCH'],
            'check' => ['POST']
        ];
    }

    /**
     * 定制DataProvider
     * @return ActiveDataProvider
     */
    public function prepareDataProvider() {
        return new ActiveDataProvider([
            'query' => Order::find()->where([
                'and',
                ['user_id' => Yii::$app->user->id],
                ['>', 'status', Order::STATUS_WAIT_SUBMIT]])
                ->orderBy('created_at desc'),
            'pagination' => [
                'pageParam' => 'page',
                'pageSizeParam' => 'size'
            ]
        ]);
    }

    /**
     * 订单结算
     * @return SettleResult $settleResult
     * @throws BadRequestHttpException
     * @throws ServerErrorHttpException
     */
    public function actionCheck() {
        $businessId = Yii::$app->request->getBodyParam('business_id');
        $payMethod = Yii::$app->request->getBodyParam('pay_method');
        $shoppingProductJson = Yii::$app->request->getBodyParam('product_list');
        if ($businessId === null) {
            throw new BadRequestHttpException("缺少必要的参数:business_id");
        } else if ($payMethod === null) {
            throw new BadRequestHttpException('缺少必要的参数:pay_method');
        } else if ($shoppingProductJson === null) {
            throw new BadRequestHttpException('缺少必要的参数:product_list');
        }
        $settleBody = new SettleBody();
        $settleBody->businessId = $businessId;
        $settleBody->payMethod = $payMethod;
        $settleBody->shoppingProductList = json_decode($shoppingProductJson, true);

        // 调用购物车结算装配器来进行结算
        $settleHandler = new CartSettleHandler($settleBody);
        $settleHandler->addDecorator(new BasicSettleDecorator());
        $settleHandler->addDecorator(new ProductSettleDecorator());
        $settleHandler->addDecorator(new ExtraFeeSettleDecorator());
        $settleHandler->addDecorator(new DiscountSettleDecorator());
        $settleHandler->addDecorator(new BookingTimeSettleDecorator());
        $settleHandler->addDecorator(new CartInfoSettleDecorator());
        $settleResult = $settleHandler->handleCartSettle();

        return $settleResult;
    }

    /**
     * 订单提交
     * @return Order $order
     * @throws BadRequestHttpException
     * @throws ServerErrorHttpException
     */
    public function actionCreate() {
        $cartId = Yii::$app->request->getBodyParam('cart_id');
        $bookedAt = Yii::$app->request->getBodyParam('booked_at');
        $remark = Yii::$app->request->getBodyParam('remark');
        if ($cartId === null) {
            throw new BadRequestHttpException('缺少必需的参数: cart_id');
        }

        /** @var $cart Cart */
        $cart = Cart::findOne($cartId);
        if ($cart === null) {
            throw new BadRequestHttpException('无效的参数: cart_id');
        }

        /** @var $address Address */
        $address = Address::findOne($cart->last_address_id);
        if ($address == null) {
            throw new ServerErrorHttpException('收获地址不能为空');
        }

        $model = new Order();
        $model->cart_id = $cartId;
        $model->business_id = $cart->business_id;
        $model->pay_method = $cart->pay_method;
        $model->origin_price = $cart->origin_price;
        $model->discount_price = $cart->discount_price;
        $model->total_price = $cart->total_price;
        $model->consignee = $address->name;
        $model->phone = $address->phone;
        $model->address = $address->summary.$address->detail;
        $model->order_num = date('YmdHis').StringUtil::generateRandomNum(6);
        $model->remark = $remark;
        $model->booked_at = $bookedAt;
        if ($model->pay_method == Order::PAYMENT_ONLINE) {
            $model->status = Order::STATUS_WAIT_PAYMENT;
        } else {
            $model->status = Order::STATUS_WAIT_ACCEPT;
        }

        if ($model->save() === false) {
            throw new ServerErrorHttpException('系统出现异常，请稍后重试');
        }

        return $model;
    }

    /**
     * 检查登录的用户是否有操作此订单的权限
     * @param string $action
     * @param null $model
     * @param array $params
     * @throws ForbiddenHttpException
     */
    public function checkAccess($action, $model = null, $params = []) {
        if ($model !== null && $model['user_id'] != Yii::$app->user->getId()) {
            throw new ForbiddenHttpException('You do not have permission to operate this resource.');
        }
    }
}