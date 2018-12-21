<?php

vendor('mixin.ACCOUNT.config');
vendor('mixin.JWT.JWT');


/**
 * 这是一个接入mixin网络的sdk
 * Created by PhpStorm.
 * User: wangjinxin
 * Date: 2018/11/28
 * Time: 13:41
 * 可参考 https://github.com/myrual/mixin_dev_resource/blob/master/mixin_network_intro_for_dev.md
 * 或 https://developers.mixin.one/api
 */
class ACCOUNT
{
    public $pin;

    public $aeskey;

    public $client_id;

    public $session_id;

    public $timeout;

    public $privatekey;

    public $pintoken;

    function __construct($options = []){
        $this->pin = constant("PIN");
        $this->aeskey = constant("AESKEY");
        $this->client_id = constant("CLIENT_ID");
        $this->session_id = constant("SESSION_ID");
        $this->timeout = constant("TIMEOUT");
        $handle = fopen(constant("PRIVATEKEY"),"r");
        $this->privatekey = fread($handle, filesize (constant("PRIVATEKEY")));
        $this->pintoken = constant("PINTOKEN");
    }

    /**
     * 创建一个mixin用户
     * @param $username
     * @return mixed
     */
    public function createUser($username){

        $key = $this->pkey_new();

        $public_key = $key['pubKey']; //公钥
        $privKey = $key['privKey']; //私钥  ======>理论上私钥要保存到本地
        echo $privKey;

        $lines = explode("\n",trim($public_key));
        array_splice($lines,count($lines) - 1,1);
        array_splice($lines,0,1);

        foreach ($lines as $key => $value) {
            $lines[$key] = trim($lines[$key]);
        }

        $pubkeystring =  implode("", $lines);

        $createuser_json = array(
            "session_secret" => $pubkeystring, //RSA public Key
            "full_name" => $username
        );

        $createuser_json_str = json_encode($createuser_json,JSON_UNESCAPED_SLASHES);
        $createuser_sig_str = 'POST/users' . $createuser_json_str;

        $payload = array(
            'uid' => $this->client_id,
            'sid' => $this->session_id,
            'jti' => $this->uuid(),
            'sig' => hash("sha256", $createuser_sig_str)
        );

        $token = $this->jwtSign($payload,$this->privatekey,'RS512'); //生成token

        $header_data = array(
            "Authorization: Bearer ". $token,
            "Content-Type: application/json"
        );

        $result = $this->post('https://api.mixin.one/users',$header_data,$createuser_json_str);
        return $result;
    }

    /**
     * Top Assets 资产列表
     * @param $asset_id 资产ID 可不传值 若不传值，则返回所有资产
     * @param $useroptions 用户信息
     * @return mixed
     */
    public function readAssets($asset_id,$useroptions){
        $transfer_sig_str = 'GET/assets';
        $url = 'https://api.mixin.one/network';
        if($asset_id && strlen($asset_id) == 36 ) {
            $transfer_sig_str = 'GET/assets/' . $asset_id;
            $url = 'https://api.mixin.one/assets/' . $asset_id;
        }
        $payload = array(
            'uid' => $useroptions['client_id'],
            'sid' => $useroptions['session_id'],
            'jti' => $this->uuid(),
            'sig' => hash("sha256", $transfer_sig_str)
        );
        $token = $this->jwtSign($payload,$useroptions['privatekey'],'RS512'); //生成token
        $header_data = array(
            "Authorization: Bearer ". $token,
            "Content-Type: application/json"
        );
        $result = $this->get($url,$header_data);
        return $result;
    }
    /**
     * 更新Pin码
     * 每个 Mixin 的用户都需要有一个 PIN 码，转帐要用。创建新用户的时候是不包含这个密码的，所以需要更新
     * @param $oldpin
     * @param $newpin
     * @param $useroption
     */
    public function updatePin($oldpin,$newpin,$useroption){

        $aeskey = $this->rsaEncrypt($useroption['pin_token'],$useroption['privatekey']);

    //   return $this->encryptCustomPIN($newpin,$pintoken,$useroption['privatekey']);
    }

    /**
     * 得到加密的PIN,很多接口操作需要
     * @param $pincode
     * @param $pintoken
     * @param $privatekey
     */
    public function encryptCustomPIN($pincode,$aeskeybase64){


    }
    // jwt加密
    private function jwtSign($payload,$privatekey,$alg = 'RS512')
    {
        if($privatekey == '') {
            $privatekey = $this->privatekey;
        }
        $jwtObj = new \JWT();
        $payload['iat'] = $_SERVER['REQUEST_TIME'];//签发时间(必填参数)
        $payload['exp'] = $_SERVER['REQUEST_TIME'] + $this->timeout;//过期时间(必填参数)
        $jwt = $jwtObj::encode($payload, $privatekey ,$alg);
        return $jwt;
    }


    /*
     * 生成RSA私钥和公钥
     * php环境需开启 php_openssl扩展
     * 如果报错openssl_pkey_export(): cannot get key from parameter 1，需设置一个环境变量
     * 变量名：OPENSSL_CONF
     * 值openssl.cnf 文件的路径： 【根目录】/apache/conf/openssl.cnf
     * @success array privKey,pubKey
     * */
    public function pkey_new()
    {
        $config = array(
            "private_key_bits" => 1024,//位数
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
            "config" => "F:\phpstudy\PHPTutorial\Apache\conf\openssl.cnf"
        );

        $res = openssl_pkey_new($config);
        openssl_pkey_export($res, $privKey);//私钥
        $pubKey = openssl_pkey_get_details($res);
        $pubKey = $pubKey["key"];//公钥
        return array('privKey' => $privKey, 'pubKey' => $pubKey);

    }

    /**
     * RSA 私钥解密
     * @param $encryptedData 要解密的内容
     * @param $privatekey 私钥
     */
    public function rsaEncrypt($encryptedData,$privatekey){
        $encryptedData	=	str_replace(' ','+', $encryptedData);
        if (empty($encryptedData)) {
            return '';
        }
        $encryptedData = base64_decode($encryptedData);

        $decryptedList = array();
        $step          = 512;    //解密长度限制
        for ($i = 0, $len = strlen($encryptedData); $i < $len; $i += $step) {
            $data      = substr($encryptedData, $i, $step);
            $decrypted = '';
            openssl_private_decrypt($data, $decrypted, $this->privatekey,3);
            $decryptedList[] = $decrypted;
        }

        return join('', $decryptedList);
    }

    public function uuid($prefix = '')
    {
        $chars = md5(uniqid(mt_rand(), true));
        $uuid  = substr($chars,0,8) . '-';
        $uuid .= substr($chars,8,4) . '-';
        $uuid .= substr($chars,12,4) . '-';
        $uuid .= substr($chars,16,4) . '-';
        $uuid .= substr($chars,20,12);
        return $prefix . $uuid;
    }

    public function post($url,$header_data,$post_data){
        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header_data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt ($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec ($ch);
        curl_close($ch);
        return $result;
    }
    public function get($url,$header_data){
        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header_data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt ($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec ($ch);
        curl_close($ch);
        return $result;
    }


}