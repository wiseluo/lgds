<?php

namespace app\admin\model;

use think\Model;

class Blacklist extends Model
{

    // 表名
    protected $name = 'user_blacklist';
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    

}
