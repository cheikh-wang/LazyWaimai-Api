<?php

namespace app\modules\v1\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class File extends Model {

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
                'mimeTypes' => 'image/*',
                'minSize' => 2048,
                'maxSize' => 2097152,
                'tooSmall' => '{attribute}最小不能小于2KB',
                'tooBig' => '{attribute}最大不能超过2MB',
                'notImage' => '{file} 不是图片文件'
            ],
        ];
    }
}