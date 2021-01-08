<?php

namespace app\admin\model;

use think\Model;


class Teacher extends Model
{

    

    //数据库
    protected $connection = 'database';
    // 表名
    protected $name = 'teacher';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'gender_text'
    ];
    

    
    public function getGenderList()
    {
        return ['0' => __('Gender 0'), '1' => __('Gender 1'), '2' => __('Gender 2')];
    }


    public function getGenderTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['gender']) ? $data['gender'] : '');
        $list = $this->getGenderList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
