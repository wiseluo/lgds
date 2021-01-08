<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;

Route::get('test/sms', 'user/TestController/makeSms'); //测试发短信

//登录注册（教师、学生共用）
Route::post('wechat/applet_login','user/LoginController/appletLogin'); //微信小程序授权登录
Route::post('wechat/applet_register','user/LoginController/appletRegister'); //微信小程序绑定注册

/* 学生接口 start */
Route::get('user/info', 'user/UserController/info');
Route::post('user/info', 'user/UserController/update'); //修改个人信息
Route::post('user/authentication', 'user/UserController/authentication'); //学生个人信息认证
Route::post('user/upload/img', 'user/UploadController/uploadImage'); //上传图片

Route::get('user/adsense', 'user/AdsenseController/index'); //学生首页广告位
Route::resource('user/bulletin', 'user/BulletinController'); //系统公告
Route::get('user/bankcardqrcode', 'user/SystemController/bankcardqrcode'); //银行卡二维码
Route::get('user/contact_info', 'user/SystemController/contactInfo'); //联系方式

Route::resource('user/course', 'user/CourseController'); //课程
Route::get('user/course/my_course', 'user/CourseController/myCourse'); //我报名的课程列表
Route::resource('user/course/enroll', 'user/CourseEnrollController'); //课程报名
Route::get('user/course/enroll/drop_course_list', 'user/CourseEnrollController/dropCourseList'); //学生的退课列表

Route::get('user/courselesson/qrcode_sign', 'user/CourseLessonController/qrcodeSign'); //课时扫码签到

/* 用户接口 end */

/* 教师接口 start */
Route::get('teacher/info', 'teacher/TeacherController/info');
Route::post('teacher/info', 'teacher/TeacherController/update'); //修改个人信息
Route::post('teacher/authentication', 'teacher/TeacherController/authentication'); //教师个人信息认证
Route::post('teacher/upload/img', 'teacher/UploadController/uploadImage'); //上传图片

Route::get('teacher/adsense', 'teacher/AdsenseController/index'); //教师首页广告位
Route::resource('teacher/bulletin', 'teacher/BulletinController'); //系统公告
Route::get('teacher/contact_info', 'teacher/SystemController/contactInfo'); //联系方式

Route::resource('teacher/course', 'teacher/CourseController'); //课程
Route::get('teacher/course/my_course', 'teacher/CourseController/myCourse'); //我任教的课程列表
Route::get('teacher/course/lesson/sign/:id', 'teacher/CourseLessonController/signRead'); //课时签到详情
Route::get('teacher/course/enroll', 'teacher/CourseEnrollController/index'); //课程报名人员列表
Route::get('teacher/course/enroll/drop_course_list', 'teacher/CourseEnrollController/dropCourseList'); //教师的有退课的课程列表
Route::get('teacher/course/enroll/drop_enroll_list', 'teacher/CourseEnrollController/dropEnrollList'); //某门课的退课人员列表

/* 教师接口 end */

return [
    
];
