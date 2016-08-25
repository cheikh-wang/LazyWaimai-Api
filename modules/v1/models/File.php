<?php

namespace app\modules\v1\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

class File extends ActiveRecord {

    /**
     * @var UploadedFile
     */
    public $avatar;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                'avatar',
                'image',
                'extensions' => 'jpg, png, jpeg, gif',
                'mimeTypes' => 'image/jpeg, image/png, image/gif',
                'minSize' => 100,
                'maxSize' => 204800,
                'tooBig' => '{attribute}最大不能超过200KB',
                'tooSmall' => '{attribute}最小不能小于0.1KB',
                'notImage' => '{file} 不是图片文件'
            ],
        ];
    }
}