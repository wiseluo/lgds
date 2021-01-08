<?php

namespace app\user\model;

use think\Model;

/**
 * 用户模型
 */
class User extends Model
{

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    // 追加属性
    protected $append = [
        
    ];

    //状态
    const STATUS = [
        0 => '未认证',
        1 => '认证申请',
        2 => '已认证',
        3 => '认证拒绝'
    ];
    
}
