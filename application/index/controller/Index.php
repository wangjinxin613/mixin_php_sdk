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


}
