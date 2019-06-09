<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;

//会员级别管理
class Member extends Controller
{
    public function lst()
    {
$member_lst = Db::name('member_lv')->select();
$this->assign('member',$member_lst);
        return view();
    }

    public function add()
    {

if (request()->isPost())
{
    $data = input('post.');

    $res = Db::name('member_lv')->insert($data);
    if($res)
    {
        $this->success('添加会员级别成功!',url('lst'));
    }else
    {
        $this->error('添加会员级别失败!');
    }
}
        return view();
    }






    public function edit()
    {
        $id = input('id');
$member_edit = db('member_lv')->find($id);

$this->assign('mem_edit',$member_edit);
if(request()->isPost())
{
    $data = input('post.');

    $res = db('member_lv')->update($data);
    if($res)
    {
        $this->success('修改会员级别成功!',url('lst'));
    }else
    {
        $this->error('修改会员级别失败!');
    }
}
        return view();
    }

    public function del()
    {
$id = input('id');
$res = db('member_lv')->delete($id);
if ($res)
{
    $this->success('删除会员级别成功!',url('lst'));
}else
{
    $this->error('删除会员级别失败!');
}
    }





}
