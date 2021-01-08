<?php

namespace app\admin\validate;

use think\Validate;

class Blacklist extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'user_id' => 'require|integer',
        'username' => 'require',
        'blacklist_date' => 'require|dateFormat:Y-m-d',
    ];
    /**
     * 提示消息
     */
    protected $message = [
        'user_id.require' => '用户id必填',
        'user_id.integer' => '用户id必须是整数',
        'username.require' => '用户名必填',
        'blacklist_date.require' => '添加时间必填',
    ];
    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => ['user_id', 'username', 'blacklist_date', 'blacklist_remark'],
        'edit' => [],
    ];
    
}
