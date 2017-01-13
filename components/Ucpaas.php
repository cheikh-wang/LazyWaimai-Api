<?php

namespace app\components;

use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;

class Ucpaas extends Component
{
    /**
     * 请求成功的状态码
     */
    const STATUS_SUCCESS = '000000';
    /**
     *  云之讯REST API版本号。当前版本号为：2014-06-30
     */
    const SoftVersion = "2014-06-30";
    /**
     * API请求地址
     */
    const BaseUrl = "https://api.ucpaas.com/";
    /**
     * @var string
     * 时间戳
     */
    private $timestamp;
    /**
     * @var string
     * 响应的状态码
     */
    public $state;
    /**
     * @var string
     * 响应的消息
     */
    public $message;

    //////////////////////////////////////////////////////
    ///                  必需配置的参数                  ///
    //////////////////////////////////////////////////////
    /**
     * @var string
     * 开发者账号ID。由32个英文字母和阿拉伯数字组成的开发者账号唯一标识符。
     */
    public $accountSid;
    /**
     * @var string
     * 开发者账号TOKEN
     */
    public $token;
    /**
     * @var string
     * 应用id
     */
    public $appId;
    /**
     * @var integer
     * 短信模板id
     */
    public $templateId;


    /**
     * 验证配置参数
     */
    public function init()
    {
        if (!isset($this->accountSid)) {
            throw new InvalidConfigException('You must setup the accountSid property.');
        }
        if (!isset($this->token)) {
            throw new InvalidConfigException('You must setup the token property.');
        }
        if (!isset($this->appId)) {
            throw new InvalidConfigException('You must setup the appId property.');
        }
        if (!isset($this->templateId)) {
            throw new InvalidConfigException('You must setup the templateId property.');
        }

        $this->timestamp = date("YmdHis") + 7200;
    }

    /**
     * @param string $type 默认json,也可指定xml,否则抛出异常
     * @return mixed|string 返回指定$type格式的数据
     * @throws NotSupportedException
     */
    public function getDevinfo($type = 'json')
    {
        if ($type == 'json') {
            $type = 'json';
        } elseif ($type == 'xml') {
            $type = 'xml';
        } else {
            throw new NotSupportedException("只能json或xml，默认为json");
        }
        $url = self::BaseUrl . self::SoftVersion . '/Accounts/' . $this->accountSid . '?sig=' . $this->getSigParameter();
        $data = $this->getResult($url,null,$type,'get');
        return $data;
    }


    /**
     * @param $appId 应用ID
     * @param $clientType 计费方式。0  开发者计费；1 云平台计费。默认为0.
     * @param $charge 充值的金额
     * @param $friendlyName 昵称
     * @param $mobile 手机号码
     * @param string $type
     * @return json /xml
     * @throws NotSupportedException
     */
    public function applyClient($appId, $clientType, $charge, $friendlyName, $mobile, $type = 'json')
    {
        $url = self::BaseUrl . self::SoftVersion . '/Accounts/' . $this->accountSid . '/Clients?sig=' . $this->getSigParameter();
        if ($type == 'json') {
            $body_json = array();
            $body_json['client'] = array();
            $body_json['client']['appId'] = $appId;
            $body_json['client']['clientType'] = $clientType;
            $body_json['client']['charge'] = $charge;
            $body_json['client']['friendlyName'] = $friendlyName;
            $body_json['client']['mobile'] = $mobile;
            $body = json_encode($body_json);
        } elseif ($type == 'xml') {
            $body_xml = '<?xml version="1.0" encoding="utf-8"?>
                        <client><appId>'.$appId.'</appId>
                        <clientType>'.$clientType.'</clientType>
                        <charge>'.$charge.'</charge>
                        <friendlyName>'.$friendlyName.'</friendlyName>
                        <mobile>'.$mobile.'</mobile>
                        </client>';
            $body = trim($body_xml);
        } else {
            throw new NotSupportedException("只能json或xml，默认为json");
        }
        $data = $this->getResult($url, $body, $type,'post');
        return $data;
    }

    /**
     * @param $clientNumber
     * @param $appId
     * @param string $type
     * @return mixed|string
     * @throws NotSupportedException
     */
    public function releaseClient($clientNumber,$appId,$type = 'json'){
        $url = self::BaseUrl . self::SoftVersion . '/Accounts/' . $this->accountSid . '/dropClient?sig=' . $this->getSigParameter();
        if($type == 'json'){
            $body_json = array();
            $body_json['client'] = array();
            $body_json['client']['clientNumber'] = $clientNumber;
            $body_json['client']['appId'] = $appId;
            $body = json_encode($body_json);
        }elseif($type == 'xml'){
            $body_xml = '<?xml version="1.0" encoding="utf-8"?>
                        <client>
                        <clientNumber>'.$clientNumber.'</clientNumber>
                        <appId>'.$appId.'</appId >
                        </client>';
            $body = trim($body_xml);
        }else {
            throw new NotSupportedException("只能json或xml，默认为json");
        }
        $data = $this->getResult($url, $body, $type,'post');
        return $data;
    }

    /**
     * @param $appId
     * @param $start
     * @param $limit
     * @param string $type
     * @return mixed|string
     * @throws NotSupportedException
     */
    public function getClientList($appId,$start,$limit,$type = 'json'){
        $url = self::BaseUrl . self::SoftVersion . '/Accounts/' . $this->accountSid . '/clientList?sig=' . $this->getSigParameter();
        if($type == 'json'){
            $body_json = array('client'=>array(
                'appId'=>$appId,
                'start'=>$start,
                'limit'=>$limit
            ));
            $body = json_encode($body_json);
        }elseif($type == 'xml'){
            $body_xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                        <client>
                            <appId>'.$appId.'</appId>
                            <start>'.$start.'</start>
                            <limit>'.$limit.'</limit>
                        </client>';
            $body = trim($body_xml);
        }else {
            throw new NotSupportedException("只能json或xml，默认为json");
        }
        $data = $this->getResult($url, $body, $type,'post');
        return $data;
    }

    /**
     * @param $appId
     * @param $clientNumber
     * @param string $type
     * @return mixed|string
     * @throws NotSupportedException
     */
    public function getClientInfo($appId,$clientNumber,$type = 'json'){
        if ($type == 'json') {
            $type = 'json';
        } elseif ($type == 'xml') {
            $type = 'xml';
        } else {
            throw new NotSupportedException("只能json或xml，默认为json");
        }
        $url = self::BaseUrl . self::SoftVersion . '/Accounts/' . $this->accountSid . '?sig=' . $this->getSigParameter(). '&clientNumber='.$clientNumber.'&appId='.$appId;
        $data = $this->getResult($url,null,$type,'get');
        return $data;
    }

    /**
     * @param $appId
     * @param $mobile
     * @param string $type
     * @return mixed|string
     * @throws NotSupportedException
     */
    public function getClientInfoByMobile($appId,$mobile,$type = 'json'){
        if ($type == 'json') {
            $type = 'json';
        } elseif ($type == 'xml') {
            $type = 'xml';
        } else {
            throw new NotSupportedException("只能json或xml，默认为json");
        }
        $url = self::BaseUrl . self::SoftVersion . '/Accounts/' . $this->accountSid . '/ClientsByMobile?sig=' . $this->getSigParameter(). '&mobile='.$mobile.'&appId='.$appId;
        $data = $this->getResult($url,null,$type,'get');
        return $data;
    }

    /**
     * @param $appId
     * @param $date
     * @param string $type
     * @return mixed|string
     * @throws NotSupportedException
     */
    public function getBillList($appId,$date,$type = 'json'){
        $url = self::BaseUrl . self::SoftVersion . '/Accounts/' . $this->accountSid . '/billList?sig=' . $this->getSigParameter();
        if($type == 'json'){
            $body_json = array('appBill'=>array(
                'appId'=>$appId,
                'date'=>$date,
            ));
            $body = json_encode($body_json);
        }elseif($type == 'xml'){
            $body_xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                        <appBill>
                            <appId>'.$appId.'</appId>
                            <date>'.$date.'</date>
                        </appBill>';
            $body = trim($body_xml);
        }else {
            throw new NotSupportedException("只能json或xml，默认为json");
        }
        $data = $this->getResult($url, $body, $type,'post');
        return $data;
    }

    /**
     * @param $appId
     * @param $clientNumber
     * @param $chargeType
     * @param $charge
     * @param string $type
     * @return mixed|string
     * @throws NotSupportedException
     */
    public function chargeClient($appId,$clientNumber,$chargeType,$charge,$type = 'json'){
        $url = self::BaseUrl . self::SoftVersion . '/Accounts/' . $this->accountSid . '/chargeClient?sig=' . $this->getSigParameter();
        if($type == 'json'){
            $body_json = array('client'=>array(
                'appId'=>$appId,
                'clientNumber'=>$clientNumber,
                'chargeType'=>$chargeType,
                'charge'=>$charge
            ));
            $body = json_encode($body_json);
        }elseif($type == 'xml'){
            $body_xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                        <client>
                            <clientNumber>'.$clientNumber.'</clientNumber>
                            <chargeType>'.$chargeType.'</chargeType>
                            <charge>'.$charge.'</charge>
                            <appId>'.$appId.'</appId>
                        </client>';
            $body = trim($body_xml);
        }else {
            throw new NotSupportedException("只能json或xml，默认为json");
        }
        $data = $this->getResult($url, $body, $type,'post');
        return $data;

    }

    /**
     * @param $appId
     * @param $fromClient
     * @param $to
     * @param null $fromSerNum
     * @param null $toSerNum
     * @param string $type
     * @return mixed|string
     * @throws NotSupportedException
     */
    public function callBack($appId,$fromClient,$to,$fromSerNum=null,$toSerNum=null,$type = 'json'){
        $url = self::BaseUrl . self::SoftVersion . '/Accounts/' . $this->accountSid . '/Calls/callBack?sig=' . $this->getSigParameter();
        if($type == 'json'){
            $body_json = array('callback'=>array(
                'appId'=>$appId,
                'fromClient'=>$fromClient,
                'fromSerNum'=>$fromSerNum,
                'to'=>$to,
                'toSerNum'=>$toSerNum
            ));
            $body = json_encode($body_json);
        }elseif($type == 'xml'){
            $body_xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                        <callback>
                            <fromClient>'.$fromClient.'</clientNumber>
                            <fromSerNum>'.$fromSerNum.'</chargeType>
                            <to>'.$to.'</charge>
                            <toSerNum>'.$toSerNum.'</toSerNum>
                            <appId>'.$appId.'</appId>
                        </callback>';
            $body = trim($body_xml);
        }else {
            throw new NotSupportedException("只能json或xml，默认为json");
        }
        $data = $this->getResult($url, $body, $type,'post');
        return $data;
    }

    /**
     * @param $appId
     * @param $verifyCode
     * @param $to
     * @param string $type
     * @return mixed|string
     * @throws NotSupportedException
     */
    public function voiceCode($appId,$verifyCode,$to,$type = 'json'){
        $url = self::BaseUrl . self::SoftVersion . '/Accounts/' . $this->accountSid . '/Calls/voiceCode?sig=' . $this->getSigParameter();
        if($type == 'json'){
            $body_json = array('voiceCode'=>array(
                'appId'=>$appId,
                'verifyCode'=>$verifyCode,
                'to'=>$to
            ));
            $body = json_encode($body_json);
        }elseif($type == 'xml'){
            $body_xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                        <voiceCode>
                            <verifyCode>'.$verifyCode.'</clientNumber>
                            <to>'.$to.'</charge>
                            <appId>'.$appId.'</appId>
                        </voiceCode>';
            $body = trim($body_xml);
        }else {
            throw new NotSupportedException("只能json或xml，默认为json");
        }
        $data = $this->getResult($url, $body, $type,'post');
        return $data;
    }

    /**
     * @param $mobile
     * @param $param
     * @param string $type
     * @return mixed|string
     * @throws NotSupportedException
     */
    public function templateSMS($mobile, $param, $type = 'json'){
        $url = self::BaseUrl . self::SoftVersion . '/Accounts/' . $this->accountSid . '/Messages/templateSMS?sig=' . $this->getSigParameter();
        if($type == 'json'){
            $body_json = array('templateSMS'=>array(
                'appId' => $this->appId,
                'templateId' => $this->templateId,
                'to' => $mobile,
                'param' => $param
            ));
            $body = json_encode($body_json);
        }elseif($type == 'xml'){
            $body_xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                        <templateSMS>
                            <templateId>'.$this->templateId.'</templateId>
                            <to>'.$mobile.'</to>
                            <param>'.$param.'</param>
                            <appId>'.$this->appId.'</appId>
                        </templateSMS>';
            $body = trim($body_xml);
        }else {
            throw new NotSupportedException("只能json或xml，默认为json");
        }
        $data = $this->getResult($url, $body, $type,'post');
        return $data;
    }

    /**
     * @param $url
     * @param null $body
     * @param string $type
     * @param $method
     * @return mixed|string
     */
    protected function getResult($url, $body = null, $type = 'json', $method)
    {
        $data = $this->request($url, $body, $type, $method);

        $result = null;
        $this->state = '';
        if (isset($data) && !empty($data)) {
            $result = $type === 'json' ? json_decode($data) : simplexml_load_string(trim($data, " \t\n\r"));
            if ($result && is_object($result)) {
                $this->state = isset($result->resp->respCode) ? (string) $result->resp->respCode : '';
            }
        }
        $this->message = $this->getMessage($this->state);

        return $result;
    }

    /**
     * @param $url
     * @param $type
     * @param $body
     * @param $method
     * @return mixed|string
     */
    protected function request($url, $body, $type, $method)
    {
        if ($type == 'json') {
            $mine = 'application/json';
        } else {
            $mine = 'application/xml';
        }
        if (function_exists("curl_init")) {
            $header = array(
                'Accept:' . $mine,
                'Content-Type:' . $mine . ';charset=utf-8',
                'Authorization:' . $this->getAuthorization(),
            );
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            if($method == 'post'){
                curl_setopt($ch,CURLOPT_POST,1);
                curl_setopt($ch,CURLOPT_POSTFIELDS,$body);
            }
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $result = curl_exec($ch);
            curl_close($ch);
        } else {
            $opts = array();
            $opts['http'] = array();
            $headers = array(
                "method" => strtoupper($method),
            );
            $headers[]= 'Accept:'.$mine;
            $headers['header'] = array();
            $headers['header'][] = "Authorization: ".$this->getAuthorization();
            $headers['header'][]= 'Content-Type:'.$mine.';charset=utf-8';

            if(!empty($body)) {
                $headers['header'][]= 'Content-Length:'.strlen($body);
                $headers['content']= $body;
            }

            $opts['http'] = $headers;
            $result = file_get_contents($url, false, stream_context_create($opts));
        }

        return $result;
    }

    /**
     * 根据状态码获取对应的错误信息
     * @param $state
     * @return null|string
     */
    protected function getMessage($state) {
        $message = null;
        switch ($state) {
            case '000000' :
                $message = '请求成功';
                break;
            case '100006' :
                $message = '手机号不合法';
                break;
            case '105122' :
                $message = '同一天每一个手机号最多只能发超10条验证码';
                break;
            case '100008' :
                $message = '手机号码为空';
                break;
            case '100009' :
                $message = '手机号为受保护的号码';
                break;
            case '103126' :
                $message = '未上线应用只能使用白名单内的号码';
                break;
            case '' :
            case '100699' :
                $message = '系统内部错误';
                break;
            default:
                $message = '其他错误';
                break;
        }

        return $message;
    }

    /**
     * 获取包头验证信息,使用Base64编码（账户Id:时间戳）
     * @return string
     */
    protected function getAuthorization()
    {
        $data = $this->accountSid . ":" . $this->timestamp;
        return trim(base64_encode($data));
    }

    /**
     * 获取验证参数,URL后必须带有sig参数，sig= MD5（账户Id + 账户授权令牌 + 时间戳，共32位）(注:转成大写)
     * @return string
     */
    protected function getSigParameter()
    {
        $sig = $this->accountSid . $this->token . $this->timestamp;
        return strtoupper(md5($sig));
    }
} 