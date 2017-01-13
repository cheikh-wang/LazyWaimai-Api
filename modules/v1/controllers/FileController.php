<?php

namespace app\modules\v1\controllers;

use yii;
use yii\rest\Controller;
use yii\web\UploadedFile;
use yii\base\NotSupportedException;
use yii\web\ForbiddenHttpException;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;
use app\modules\v1\models\File;
use app\components\oauth2\TokenAuth;
use app\components\QiNiu;

class FileController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => TokenAuth::className()
        ];

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    protected function verbs() {
        return [
            'create' => ['POST']
        ];
    }

    /**
     * 上传文件
     * 如果是单文件上传, 上传成功后返回url字符串;
     * 如果是多文件上传, 上传成功后返回url数组
     *
     * @return string | array
     * @throws BadRequestHttpException
     * @throws ServerErrorHttpException
     * @throws yii\web\HttpException
     */
    public function actionCreate() {
        if (empty($_FILES)) {
            throw new BadRequestHttpException('没有找到需要上传的文件');
        }

        if (count($_FILES) > 1) { // 多文件上传
            $urls = [];
            foreach ($_FILES as $name => $value) {
                $file = UploadedFile::getInstanceByName($name);
                if ($this->validateFile($name, $file)) {
                    $urls[] = $this->uploadIntoQiNiuCloud($file);
                }
            }

            return $urls;
        } else {
            $url = null;
            list($name) = each($_FILES);
            $file = UploadedFile::getInstanceByName($name);
            if ($this->validateFile($name, $file)) {
                $url = $this->uploadIntoQiNiuCloud($file);
            }

            return $url;
        }
    }

    /**
     * 检查上传的文件是否满足约束条件
     * @param $name
     * @param $file
     * @return bool
     * @throws BadRequestHttpException
     * @throws NotSupportedException
     */
    private function validateFile($name, $file) {
        $model = new File();
        if ($model->hasProperty($name)) {
            $model->$name = $file;
            $result = $model->validate();
            if (!$result) {
                throw new BadRequestHttpException($model->getFirstError($name));
            }

            return $result;
        } else {
            throw new NotSupportedException('不支持的上传名称:' . $name);
        }
    }

    /**
     * 上传文件到七牛云
     * @param UploadedFile $file 需要上传的文件
     * @return string 上传后的url
     */
    private function uploadIntoQiNiuCloud(UploadedFile $file) {
        /** @var $qiniu QiNiu */
        $qiniu = Yii::$app->qiniu;
        $result = $qiniu->uploadFile($file->tempName, $file->name);
        $url = $qiniu->getLink($result['key']);

        return $url;
    }

    /**
     * @inheritdoc
     */
    public function checkAccess($action, $model = null, $params = []) {
        if ($model !== null && $model['id'] !== Yii::$app->user->id) {
            throw new ForbiddenHttpException('You do not have permission to operate this resource.');
        }
    }
}