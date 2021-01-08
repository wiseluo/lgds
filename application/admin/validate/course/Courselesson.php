<?php

namespace app\admin\validate\course;

use think\Validate;

class Courselesson extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'course_id' => 'require|integer',
        'sort' => 'require|number|gt:0',
        'lesson_no' => 'require',
        'lesson_name' => 'require',
        'start_time' => 'require|dateFormat:Y-m-d H:i:s',
        'end_time' => 'require|dateFormat:Y-m-d H:i:s',
    ];
    /**
     * 提示消息
     */
    protected $message = [
        'course_id.require' => '课程id必填',
        'course_id.integer' => '课程id必须是整数',
        'sort.require' => '排序必填',
        'lesson_no.require' => '课号必填',
        'lesson_name.require' => '课时名称必填',
        'start_time.require' => '课时开始时间必填',
        'end_time.require' => '课时结束时间必填',
    ];
    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => ['course_id', 'sort', 'lesson_no', 'lesson_name', 'start_time', 'end_time'],
        'edit' => ['course_id', 'sort', 'lesson_no', 'lesson_name', 'start_time', 'end_time'],
    ];
    
}
