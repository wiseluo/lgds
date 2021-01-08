<?php

namespace app\user\Validate;

use think\Validate;
use app\common\behavior\IdentityCardTool;

class UserValidate extends Validate
{

    protected $rule = [
        'nickname' => 'require|max:50',
        'gender' => 'require|in:0,1,2',
        'username' => 'require|max:20',
        'phone' => 'require|integer|checkPhone',
        'identity_card' => 'require|checkIdentityCard',
        'work_unit' => 'require',
    ];
    
    protected $message = [
        'nickname.require' => '昵称必填',
        'nickname.max' => '昵称不能超过50位',
        'gender.require' => '性别必填',
        'username.require' => '姓名必填',
        'username.max' => '姓名不能超过20位',
        'phone.require' => '手机号必填',
        'phone.integer' => '手机号必须是数字',
        'identity_card.require' => '身份证必填',
        'work_unit.require' => '工作单位必填',
    ];
    
    protected $scene = [
        'authentication' => ['nickname', 'gender', 'username', 'phone', 'identity_card', 'work_unit'],
        'update' => ['nickname', 'gender', 'phone', 'work_unit'],
    ];
    
    public function checkPhone($value, $rule, $data)
    {
        $match = '/^(13|14|15|17|18)[0-9]{9}$/';
        $result = preg_match($match, $value);
        if($result) {
            return true;
        }else{
            return '手机号不正确';
        }
    }

    public function checkIdentityCard($value, $rule, $data)
    {
        if (IdentityCardTool::isValid($value)) {
            return true;
        } else {
            return '证件号码不是一个合法的证件号码';
        }
    }
}

