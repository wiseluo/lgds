<?php

namespace app\admin\controller\user;

use think\Db;
use app\common\controller\Backend;
use app\user\model\User as UserModel;
use app\admin\model\Blacklist as BlacklistModel;

/**
 * 黑名单管理
 *
 * @icon fa fa-user
 */

class Blacklist extends Backend
{
    protected $model = null;
    protected $searchFields = 'id,username';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Blacklist;
    }

    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax())
        {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            $offset = $this->request->get("offset", 0);
            $limit = $this->request->get("limit", 0);
            $search = $this->request->get("search", '');
            $filter = $this->request->get("filter", '');
            $filter = (array)json_decode($filter, true);
            $filter = $filter ? $filter : [];
            
            $where = [];
            if(isset($filter['username'])) {
                $where['username'] = ["LIKE", "%{$filter['username']}%"];
            }
            if(isset($filter['phone'])) {
                $where['phone'] = ["LIKE", "%{$filter['phone']}%"];
            }
            if(isset($filter['blacklist_remark'])) {
                $where['blacklist_remark'] = ["LIKE", "%{$filter['username']}%"];
            }

            $total = $this->model
                    ->where($where)
                    ->count();
            $list = $this->model
                    ->where($where)
                    ->limit($offset, $limit)
                    ->select();

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 添加
    */
    public function add()
    {
        if ($this->request->isPost()) {
            $param = $this->request->post("row/a");
            $validate = validate('Blacklist');
            if(!$validate->scene('add')->check($param)) {
                return show(401, $validate->getError());
            }
            $data = [
                'user_id' => $param['user_id'],
                'username' => $param['username'],
                'phone' => $param['phone'],
                'blacklist_date' => $param['blacklist_date'],
                'blacklist_remark' => $param['blacklist_remark'],
            ];
            Db::startTrans();
            $blacklist = new BlacklistModel($data);
            $res = $blacklist->allowField(true)->save();
            if($res) {
                $user = new UserModel();
                $user_res = $user->allowField(true)->save(['blacklist'=> 1], ['id'=> $param['user_id']]);
                if($user_res) {
                    Db::commit();
                    $this->success();
                }else{
                    Db::rollback();
                    $this->error('添加失败');
                }
            }else{
                $this->error('添加失败');
            }
        }
        return $this->view->fetch();
    }

    //撤销
    public function cancel($ids)
    {
        $blacklist = BlacklistModel::get($ids);
        Db::startTrans();
        $res = BlacklistModel::where('id', $ids)->delete();
        if($res) {
            $user_obj = new UserModel();
            $user_res = $user_obj->allowField(true)->save(['blacklist'=> 0], ['id'=> $blacklist['user_id']]);
            if($user_res) {
                Db::commit();
                $this->success('操作成功');
            }else{
                Db::rollback();
                $this->error('操作失败');
            }
        }else{
            $this->error('操作失败');
        }
    }

}
