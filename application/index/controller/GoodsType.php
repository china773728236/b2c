<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;

//商品类型
class GoodsType extends Controller
{
    public function lst()
    {
$goods_type = Db::name('goods_type')->select();
$this->assign('goods_type',$goods_type);
        return view();
    }

    public function add()
    {

if (request()->isPost())
{
    $data = input('post.');
    $res = Db::name('goods_type')->insert($data);
    if($res)
    {
        $this->success('添加商品类型成功!',url('lst'));
    }else
    {
        $this->error('添加商品类型失败!');
    }
}
        return view();
    }






    public function edit()
    {
        $id = input('id');
$goods_type = db('goods_type')->find($id);
$this->assign('goods_type_edit',$goods_type);
if(request()->isPost())
{
    $data = input('post.');

    $res = db('goods_type')->update($data);
    if($res)
    {
        $this->success('修改商品类型成功!',url('lst'));
    }else
    {
        $this->error('修改商品类型失败!');
    }
}
        return view();
    }

    public function del()
    {
$id = input('id');
$res = db('goods_type')->delete($id);
if ($res)
{
    $this->success('删除商品类型成功!',url('lst'));
}else
{
    $this->error('删除商品类型失败!');
}
    }





}
