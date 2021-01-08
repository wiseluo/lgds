<?php

namespace app\admin\controller\user;

use think\Request;
use app\common\controller\Backend;
use app\user\model\User as UserModel;
use app\teacher\model\Teacher as TeacherModel;

/**
 * 会员管理
 *
 * @icon fa fa-user
 */

class User extends Backend
{

    protected $relationSearch = true;


    /**
     * @var \app\admin\model\User
     */
    protected $model = null;
    protected $searchFields = 'id,username,nickname,mobile';
    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('User');
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
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                    ->with('group')
                    ->where($where)
                    ->where('blacklist', 0)
                    ->order($sort, $order)
                    ->count();
            $list = $this->model
                    ->with('group')
                    ->where($where)
                    ->where('blacklist', 0)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
            foreach ($list as $k => $v)
            {
                $v->hidden(['password', 'salt']);
            }
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = NULL)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $this->view->assign('groupList', build_select('row[group_id]', \app\admin\model\UserGroup::column('id,name'), $row['group_id'], ['class' => 'form-control selectpicker']));
        
        $this->view->assign("genderList", $this->model->getGenderList());
        return parent::edit($ids);
    }

    //设为教师
    public function assignteacher($ids)
    {
        $user = UserModel::get($ids);
        if($user == false) {
            $this->error('用户不存在');
        }
        $teacher = TeacherModel::where('openid_wx', $user['openid_wx'])->find();
        if($teacher) {
            $this->success('该用户已经是教师了');
        }
        $teacher_data = [
            'phone' => $user['phone'],
            'openid_wx' => $user['openid_wx'],
            'nickname' => $user['nickname'],
            'username' => $user['username'],
            'avatar' => $user['avatar'],
            'gender' => $user['gender'],
            'identity_card' => $user['identity_card'],
            'work_unit' => $user['work_unit'],
            'status' => $user['status'],
        ];
        $teacher_obj = new TeacherModel();
        $res = $teacher_obj->allowField(true)->save($teacher_data);
        if($res) {
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }

    /**
     * 选择学员列表
    */
    public function userselect(Request $request)
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            $offset = $request->get("offset", 0);
            $limit = $request->get("limit", 10);
            $search = $this->request->get("search", '');
            $filter = $this->request->get("filter", '');
            $filter = (array)json_decode($filter, true);
            $filter = $filter ? $filter : [];

            if(isset($filter['username'])) {
                $where['username'] = ["LIKE", "%{$filter['username']}%"];
            }
            if(isset($filter['nickname'])) {
                $where['nickname'] = ["LIKE", "%{$filter['nickname']}%"];
            }
            if(isset($filter['phone'])) {
                $where['phone'] = ["LIKE", "%{$filter['phone']}%"];
            }
            $total = UserModel::where($where)->count();
            $list = UserModel::where($where)
                ->order('id', 'desc')
                ->limit($offset, $limit)
                ->select();
            $list = collection($list)->toArray();

            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        return $this->view->fetch();
    }
}
