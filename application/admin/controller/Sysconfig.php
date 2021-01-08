<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use app\common\model\Sysconfig as SysconfigModel;

/**
 * 系统配置管理
 *
 * @icon fa fa-circle-o
 */
class Sysconfig extends Backend
{
    public function index()
    {
        $sysconfig = SysconfigModel::all();
        $list = [];
        foreach($sysconfig as $k => $v) {
            $list[] = [
                'name'=> $v['name'],
                'value'=> $v['value'],
                'image'=> $v['image'],
                'type'=> $v['type'],
                'content'=> $v['content'],
            ];
        }
        $index = 0;
        foreach ($list as $k => &$v) {
            $v['active'] = !$index ? true : false;
            $index++;
        }
        $this->view->assign('sysList', $list);
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit()
    {
        if ($this->request->isPost()) {
            $row = $this->request->post("row/a");
            if ($row) {
                if($row['type'] == 'bankcardqrcode') {
                    $sysconfig = new SysconfigModel();
                    $res = $sysconfig->allowField(true)->save(['image'=> $row['image']], ['type'=> 'bankcardqrcode']);
                }else if($row['type'] == 'contactinfo') {
                    $sysconfig = new SysconfigModel();
                    $res = $sysconfig->allowField(true)->save(['content'=> $row['content']], ['type'=> 'contactinfo']);
                }
                if($res) {
                    $this->success();
                }else{
                    $this->error('修改失败');
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
    }
}
