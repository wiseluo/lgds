<?php

namespace app\user\Validate;

use think\Validate;

class LoginValidate extends Validate
{
    protected $rule = [
        'code' => 'require',
        'openid' => 'require',
        'nickname' => 'require|max:50',
        'avatar' => 'require',
        'gender' => 'require|in:0,1,2',
    ];
    
    protected $message = [
        'code.require' => '授权码必填',
        'openid.require' => '微信标识必填',
        'nickname.require' => '昵称必填',
        'nickname.max' => '昵称不能超过50位',
        'avatar.require' => '头像必填',
        'gender.require' => '性别必填',
    ];
    
    protected $scene = [
        'appletLogin' => ['code'],
        'appletRegister' => ['openid', 'nickname', 'avatar', 'gender'],
    ];
    
}

