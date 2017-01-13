<?php
/**
 * @link https://github.com/borodulin/yii2-oauth2-server
 * @copyright Copyright (c) 2015 Andrey Borodulin
 * @license https://github.com/borodulin/yii2-oauth2-server/blob/master/LICENSE
 */

namespace app\components\oauth2\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "oauth2_client".
 *
 * @property string $client_id
 * @property string $client_secret
 * @property string $redirect_uri
 * @property string $grant_type
 * @property string $scope
 * @property integer $user_id
 * @property string $public_key
 *
 * @property AccessToken[] $accessTokens
 * @property RefreshToken[] $refreshTokens
 */
class Client extends ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%oauth2_client}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['client_id', 'client_secret', 'redirect_uri'], 'required'],
            [['scope'], 'string'],
            [['created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['client_id', 'client_secret', 'grant_type'], 'string', 'max' => 80],
            [['redirect_uri'], 'string', 'max' => 2000]
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            TimestampBehavior::className(),
            BlameableBehavior::className(),
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'client_id' => 'Unique client identifier',
            'client_secret' => 'Client secret',
            'redirect_uri' => 'Redirect URI used for Authorization Grant',
            'grant_type' => 'Space-delimited list of grant types permitted, null = all',
            'scope' => 'Space-delimited list of approved scopes',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccessTokens() {
        return $this->hasMany(AccessToken::className(), ['client_id' => 'client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRefreshTokens() {
        return $this->hasMany(RefreshToken::className(), ['client_id' => 'client_id']);
    }
}