<?php

namespace app\teacher\model;

use think\Model;

/**
 * 教师模型
 */
class Teacher extends Model
{

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    // 追加属性
    protected $append = [
        
    ];

}
