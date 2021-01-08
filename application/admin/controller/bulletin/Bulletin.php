<?php

namespace app\admin\controller\bulletin;

use app\common\controller\Backend;
use app\common\model\Bulletin as BulletinModel;

/**
 * 系统公告
 *
 * @icon fa fa-circle-o
 */
class Bulletin extends Backend
{
    
    /**
     * Bulletin模型对象
     * @var \app\admin\model\bulletin\Bulletin
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\bulletin\Bulletin;

    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    
    /**
     * 添加
    */
    public function add()
    {
        if ($this->request->isPost()) {
            $param = $this->request->post("row/a");
            $validate = validate('Bulletin', 'validate\bulletin');
            if(!$validate->scene('add')->check($param)) {
                return show(401, $validate->getError());
            }
            $data = [
                'admin_id' => $this->auth->id,
                'type_id' => $param['type_id'],
                'title' => $param['title'],
                'content' => $param['content'],
                'release_date' => $param['release_date'],
            ];
            $bulletin = new BulletinModel($data);
            $res = $bulletin->allowField(true)->save();
            if($res) {
                $this->success();
            }else{
                $this->error('添加失败');
            }
        }
        return $this->view->fetch();
    }

    /**
     * 编辑
    */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $param = $this->request->post("row/a");
            $validate = validate('Bulletin', 'validate\bulletin');
            if(!$validate->scene('edit')->check($param)) {
                return show(401, $validate->getError());
            }
            $data = [
                'admin_id' => $this->auth->id,
                'type_id' => $param['type_id'],
                'title' => $param['title'],
                'content' => $param['content'],
                'release_date' => $param['release_date'],
            ];
            $bulletin = new BulletinModel();
            $res = $bulletin->allowField(true)->save($data, ['id'=> $ids]);
            if($res) {
                $this->success();
            }else{
                $this->error('编辑失败');
            }
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }
}
