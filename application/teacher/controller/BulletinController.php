<?php
namespace app\teacher\controller;

use think\Request;
use app\common\model\Bulletin;

class BulletinController extends BaseController
{
    public function index(Request $request)
    {
        $pagesize = $request->param('pagesize',10);

        $res = Bulletin::alias('b')
            ->join('fa_bulletin_type bt', 'bt.id=b.type_id', 'left')
            ->field('b.id,b.title,b.release_date,bt.name bulletin_type_name,bt.icon bulletin_icon')
            ->order('b.id desc')
            ->paginate($pagesize)
            ->toArray();
        
        return show(200, '成功', $res);
    }

    public function read($id)
    {
        $bulletin = Bulletin::alias('b')
            ->join('fa_admin a', 'b.admin_id=a.id', 'left')
            ->field('b.id,b.title,b.content,b.release_date,a.nickname')
            ->where('b.id', $id)
            ->find();

        return show(200, '成功', $bulletin);
    }
}
