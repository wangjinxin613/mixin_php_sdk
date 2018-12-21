<?php

namespace app\index\controller;
vendor('mixin.ACCOUNT.ACCOUNT');

class Index
{
    public function index(){

    }

    /**
     * http://localhost/index.php/index/index/createUser
     * 调用mixin SDK 创建一个mixin用户
     */
    public function createUser()
    {
        $mixinUser = new \ACCOUNT();
        return $mixinUser->createUser('18841692393');
    }

    /**
     * 读取用户资产列表
     * 先创建一个用户，然后读取这个用户的资产列表
     */
    public function readAssets(){
        $mixinUser = new \ACCOUNT();

        $privatekey = <<<EOD
-----BEGIN PRIVATE KEY----- 
MIICdwIBADANBgkqhkiG9w0BAQEFAASCAmEwggJdAgEAAoGBANbMekh02P9lERjB 
uJxU+d3qwML5/r/Ooa+MnftZP4dnJy3OWav3qV+Vx+TG0/nfifYR9gFO55rkPYDV 
upnKjw3yIdQDWad8n0ch17ZsXTrtjXCMA+7vXZsgdGY2FNByK930vaCc1/KP5NfJ 
8vC9TgfChl/KikRUU1H9oQTuXH7xAgMBAAECgYEAgx7TTsO1a0IAy8IFtbjRxrv0 
65C8B85VONp33eU/OKKpcfbTGnzWcbj3Cxqsb44bo5CXQXkvPIgzWyAdBqB17gki 
lCgD5G7HXs8bX+YT4wYM35MYF3gWGFAmHs4I1xsWHH9Cn+WDPJU+pNBsiGrKMhzm 
SpuXuXNvFcIo/erTG7kCQQDrz8JnJJC3UOwWGRfi2J/Dc1CEEPobF387yk6feIAd 
h53/Q4oxpv1AM28EOOeZFwOrDTNbNGpgOqBVJ64r6C3LAkEA6TAvGyApRb+1/GlN 
BFRqDwJZQ6d7g9qACWEcg7xXEBmfqr3iaAxoBHVCBpdv5s5IlHt+QPc9xWfXrFjU 
zx4uswJAW1es7Bsj236DFMQ/lmVm7WS7qYyR9PCTHmvtLKSWq9megAR/gWA39Sh3 
NmF8hLZ/e0CvxgJ1ujS7aoDmXKehJQJBAI6JwYGXOyNDeH5973ICF4JtMRtFuR5z 
5WfWUJPGAIH657p6r3ZifwKamm1lDCXNWlhI1HfpqXyNaSwUcKDaFjkCQEPQY1bY 
3XVIywNiskihskpmqQZMKQZiTW/kz/M+jgGXw/24uL2APBIxVkh6fi6rO0gXE+Rp 
+2EKKGcca7YAkyA= 
-----END PRIVATE KEY-----
EOD;

        $useroptions = [
            'client_id' => 'c9044978-589c-3edd-a431-1275aaaf99aa',
            'session_id' => '67744d8d-8b02-483e-adf3-ebf13d53f950',
            'privatekey' => $privatekey,
            'pin_token' => 'J+HGfmdXz/+zXvDBbth23L6zWtymz2uvoe+bVkx5Ywpm7cL6YDFk8yH+pRGKUkq9aUmdfHJBpSvjmcMgm4PrGhmclD2ml/fHmb2IN5v4Pmce1d5koN3TSSpyc3RU14WnLC68Nr7DtiPhke2BrbaTapWEP80bZlrBVFkaCeDrSN8='
        ];

//        $useroptions = [
//            'client_id' => 'f0029b3a-23d1-486f-82e9-fee4edaf6164',
//            'session_id' => '348ca237-84a9-40ae-b327-d5eede4c1500',
//            'privatekey' => "",
//            'pin_token' => 'J+HGfmdXz/+zXvDBbth23L6zWtymz2uvoe+bVkx5Ywpm7cL6YDFk8yH+pRGKUkq9aUmdfHJBpSvjmcMgm4PrGhmclD2ml/fHmb2IN5v4Pmce1d5koN3TSSpyc3RU14WnLC68Nr7DtiPhke2BrbaTapWEP80bZlrBVFkaCeDrSN8='
//        ];
        $msg = $mixinUser->readAssets('',$useroptions);
        return json($msg);
    }

    /**
     * 更新pin
     */
    public function updatePin(){
        $mixinUser = new \ACCOUNT();

        $privatekey = <<<EOD
-----BEGIN PRIVATE KEY----- 
MIICdwIBADANBgkqhkiG9w0BAQEFAASCAmEwggJdAgEAAoGBANbMekh02P9lERjB 
uJxU+d3qwML5/r/Ooa+MnftZP4dnJy3OWav3qV+Vx+TG0/nfifYR9gFO55rkPYDV 
upnKjw3yIdQDWad8n0ch17ZsXTrtjXCMA+7vXZsgdGY2FNByK930vaCc1/KP5NfJ 
8vC9TgfChl/KikRUU1H9oQTuXH7xAgMBAAECgYEAgx7TTsO1a0IAy8IFtbjRxrv0 
65C8B85VONp33eU/OKKpcfbTGnzWcbj3Cxqsb44bo5CXQXkvPIgzWyAdBqB17gki 
lCgD5G7HXs8bX+YT4wYM35MYF3gWGFAmHs4I1xsWHH9Cn+WDPJU+pNBsiGrKMhzm 
SpuXuXNvFcIo/erTG7kCQQDrz8JnJJC3UOwWGRfi2J/Dc1CEEPobF387yk6feIAd 
h53/Q4oxpv1AM28EOOeZFwOrDTNbNGpgOqBVJ64r6C3LAkEA6TAvGyApRb+1/GlN 
BFRqDwJZQ6d7g9qACWEcg7xXEBmfqr3iaAxoBHVCBpdv5s5IlHt+QPc9xWfXrFjU 
zx4uswJAW1es7Bsj236DFMQ/lmVm7WS7qYyR9PCTHmvtLKSWq9megAR/gWA39Sh3 
NmF8hLZ/e0CvxgJ1ujS7aoDmXKehJQJBAI6JwYGXOyNDeH5973ICF4JtMRtFuR5z 
5WfWUJPGAIH657p6r3ZifwKamm1lDCXNWlhI1HfpqXyNaSwUcKDaFjkCQEPQY1bY 
3XVIywNiskihskpmqQZMKQZiTW/kz/M+jgGXw/24uL2APBIxVkh6fi6rO0gXE+Rp 
+2EKKGcca7YAkyA= 
-----END PRIVATE KEY-----
EOD;
        $useroptions = [
            'client_id' => 'c9044978-589c-3edd-a431-1275aaaf99aa',
            'session_id' => '67744d8d-8b02-483e-adf3-ebf13d53f950',
            'privatekey' => $privatekey,
            'pin_token' => 'JhmclD2ml/fHmb2IN5v4Pmce1d5koN3TSSpyc3RU14WnLC68Nr7DtiPhke2BrbaTapWEP80bZlrBVFkaCeDrSN8='
        ];

        return $mixinUser->updatePin("","123456",$useroptions);
    }
}
