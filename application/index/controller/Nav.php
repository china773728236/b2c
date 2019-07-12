<?php
namespace app\index\controller;
use think\Db;
class Nav extends Init
{
    public function lst()
    {
    	$nav_lst = db('nav')->select();
    	$this->assign('nav_lst',$nav_lst);
        return view();
    }


    public function add()
    {
    	if(request()->isPost())
    	{
    		$data = input('post.');
    	$res =	db('nav')->insert($data);
if($res)
{
	$this->success('添加导航成功!',url('lst'));
}else
{
	$this->error('添加导航失败!');
}
    	}
        return view();
    }


    public function edit()
    {
    	$id = input('nav_id');
    	
    	$nav_lst = db('nav')->where('nav_id',$id)->find();

    	$this->assign('nav_edit',$nav_lst);
    	if(request()->isPost())
    	{
    		$data = input('post.');
    		$res = db('nav')->where('nav_id',$id)->update($data);
    		if($res)
    		{
    		$this->success('修改导航成功!',url('lst'));
}else
{
	$this->error('修改导航失败!');
}
    	}
        return view();
    }


    public function del()
    {
        $id = input('nav_id');
        $res = db('nav')->delete($id);
        if($res)
    		{
    		$this->success('删除导航成功!',url('lst'));
}else
{
	$this->error('删除导航失败!');
}
    }
}
