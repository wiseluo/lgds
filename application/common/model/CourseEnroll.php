<?php

namespace app\common\model;

use think\Model;
use traits\model\SoftDelete;

class CourseEnroll extends Model
{

    use SoftDelete;

    // 表名
    protected $name = 'course_enroll';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [

    ];
    

}
