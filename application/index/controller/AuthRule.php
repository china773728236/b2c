<?php
/*
 * 管理员用户组
 * */
namespace app\index\controller;
use think\Db;
use app\index\model\AuthRule as AuthRuleModel;
class AuthRule extends Init
{



    public function lst()
{

    $rule = new AuthRuleModel();
    $res = $rule->authRuleTree();
    $this->assign('res',$res);
    return view();
}


    public function add()
    {
        $rule = new AuthRuleModel();
       $res = $rule->authRuleTree();
        $this->assign('res',$res);
     if(request()->isPost())
     {
         $data = input('post.');
         $res = Db::name('auth_rule')->insert($data);

         if($res)
         {
             $this->success('添加权限成功!');
         }else
         {
             $this->error('添加权限失败!');
         }
     }
        return view();
    }


    public function edit()
    {
        $rule = new AuthRuleModel();
        $res = $rule->authRuleTree();
        $this->assign('res',$res);
        //拿到当前的数据
        $id = input('id');
        $data = Db::name('auth_rule')->find($id);
        //防止用户乱输入id
        if(!$data)
        {
            $this->error('用户不存在');
        }
        $this->assign('rule',$data);
        if (request()->isPost())
        {
            $data = input('post.');
            $res = db('auth_rule')->update($data);
            if($res)
            {
                $this->success('更改权限成功!',url('lst'));
            }else
            {
                $this->error('没有权限成功!',url('lst'));
            }
        }
        return view();
    }




    public function del()
    {
          $id = input('id');
          $rule = new AuthRuleModel();
          $rule->getParentId($id);
         $res = $rule->delTree($id);
         //把主栏目的ID赋值给数组
         $res[] = $id;
         //dump($res);die;
        if($res)
        {
            $res = Db::table('b2c_auth_rule')->delete($res);
            if ($res)
            {
                $this->success('删除权限成功!',url('lst'));
            }else
            {
                $this->error('删除权限失败!');
            }
        }
    }



}
