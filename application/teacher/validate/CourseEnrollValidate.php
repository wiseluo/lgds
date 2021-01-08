<?php

namespace app\teacher\Validate;

use think\Validate;

class CourseEnrollValidate extends Validate
{

    protected $rule = [
        'course_id' => 'require|integer',
    ];
    
    protected $message = [
        'course_id.require' => '课程id必填',
        'course_id.integer' => '课程id必须是数字',
    ];
    
    protected $scene = [
        'dropEnrollList' => ['course_id'],
    ];
    
}

