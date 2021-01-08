<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
define('APP_PATH', __DIR__ . '/../application/');

// 判断是否安装FastAdmin
if (!is_file(APP_PATH . 'admin/command/Install/install.lock'))
{
    header("location:./install.php");
    exit;
}
#默认10条记录每页
define('PAGESIZE', 10);
// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';