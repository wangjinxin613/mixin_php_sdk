<?php

vendor('mixin.ACCOUNT.config');
vendor('mixin.JWT.JWT');
vendor('mixin.RSA.RSA');


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

    /**
     * 不传参数 是机器人账户，传了参数是mixin用户账户
     * ACCOUNT constructor.
     * @param array $options
     */
    function __construct($options = []){
        if(empty($options)) {
            $this->pin = constant("PIN");
            $this->aeskey = constant("AESKEY");
            $this->client_id = constant("CLIENT_ID");
            $this->session_id = constant("SESSION_ID");
            $this->timeout = constant("TIMEOUT");
            $handle = fopen(constant("PRIVATEKEY"),"r");
            $this->privatekey = fread($handle, filesize (constant("PRIVATEKEY")));
        }
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

        $token = $this->jwtSign($payload,'RS512'); //生成token

        $header_data = array(
            "Authorization: Bearer ". $token,
            "Content-Type: application/json"
        );
        $result = $this->post('https://api.mixin.one/users',$header_data,$createuser_json_str);
        return $result;
    }

    public function updatePin($oldpin,$newpin,$old){

    }

    /**
     * JWTj加密 1
     * @param $payload
     * @param string $alg
     * @return string Token
     */
    private function jwtSign($payload,$alg = 'RS512')
    {
        $jwtObj = new \JWT();
        $payload['iat'] = $_SERVER['REQUEST_TIME'];//签发时间(必填参数)
        $payload['exp'] = $_SERVER['REQUEST_TIME'] + $this->timeout;//过期时间(必填参数)
        $jwt = $jwtObj::encode($payload, $this->privatekey ,$alg);
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
            "private_key_type" => OPENSSL_KEYTYPE_RSA
        );
        $res = openssl_pkey_new($config);
        openssl_pkey_export($res, $privKey);//私钥
        $pubKey = openssl_pkey_get_details($res);
        $pubKey = $pubKey["key"];//公钥
        return array('privKey' => $privKey, 'pubKey' => $pubKey);
    }

    /**
     * 随机返回一个UUID格式的字符串
     * @param string $prefix
     * @return string
     */
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
    public function test(){
        $result = $this->createUser('18841692393');
        print_r($result);

    }

}