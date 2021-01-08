<?php

namespace app\common\model;

use think\Model;
use traits\model\SoftDelete;

class Course extends Model
{

    use SoftDelete;
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [

    ];
    

    public function courseType()
    {
        return $this->belongsTo('Coursetype', 'type_id', 'id');
    }
    







}
