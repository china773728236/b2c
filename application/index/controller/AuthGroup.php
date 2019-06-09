<?php
/*
 * 管理员用户组
 * */
namespace app\index\controller;
use think\Db;
use app\index\model\AuthGroup as AuthGroupModel;
//引入权限规则
use app\index\model\AuthRule as AuthRuleModel;
class AuthGroup extends Init
{
    public function lst()
    {
        $res = Db::name('auth_group')->select();
        $this->assign('authlst',$res);
        return view();
    }


    public function add()
    {
       //引入权限
        $auth_rule = new AuthRuleModel();
        $res = $auth_rule->authRuleTree();
        $this->assign('res',$res);
        if(request()->isPost())
        {
            $data = input('post.');
            if($data['rules'])
            {
                $data['rules'] = implode(',',$data['rules']);
            }
            //dump($data);die;
            $res = Db::name('auth_group')->insert($data);
            if ($res)
            {
                $this->success('添加管理组成功',url('AuthGroup/lst'));
            }else
            {
                $this->error('添加管理组失败');
            }
        }
        return view();
    }


    public function edit()
    {
        //引入权限
        $auth_rule = new AuthRuleModel();
        $res = $auth_rule->authRuleTree();
        $this->assign('res',$res);

        $id = input('id');
        $res = Db::name('auth_group')->find($id);
        $this->assign('auth_group_edit',$res);
        if (request()->isPost())
        {
            $data = input('post.');
            if($data['rules'])
            {
                $data['rules'] = implode(',',$data['rules']);
            }
           // dump($data);die;
            $res = Db::name('auth_group')->update($data);
            if ($res)
            {
                $this->success('修改管理组成功',url('AuthGroup/lst'));
            }else
            {
                $this->error('修改管理组失败');
            }
        }
        return view();
    }


    public function del()
    {
       $id = input('id');
       $res = db('auth_group')->delete($id);
        if ($res)
        {
            $this->success('删除管理组成功',url('AuthGroup/lst'));
        }else
        {
            $this->error('删除管理组失败');
        }
    }
}
