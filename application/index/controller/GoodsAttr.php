<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;

//商品类型
class GoodsAttr extends Controller
{
    //设置一下只在添加和编辑才进行查询,减少代码冗余
    protected $beforeActionList = [
        'newType' =>  ['only'=>'add,edit'],
    ];

    protected function newType()
    {
        //我们需要在页面显示当前栏目以便于栏目分类
        //查询商品类型
        $goods_type = Db::name('goods_type')->select();
        $this->assign('goods_type',$goods_type);
    }
    public function lst()
    {
//db(当前表)->alias(当前表的别名)->field(当前表的字段，另一张表的字段)->join('另一张表 表的别名'，当前表与另一张表相同的字段)->select();
        $goods_attr = db('goods_attr')->alias('a')->field('a.id,attr_name,attr_type,attr_values,b.type_name')->join('b2c_goods_type b','a.type_id=b.id')->select();
$this->assign('goods_attr',$goods_attr);
        return view();
    }

    public function add()
    {

if (request()->isPost())
{
    $data = input('post.');
    $res = Db::name('goods_attr')->insert($data);
    if($res)
    {
        $this->success('添加商品属性成功!',url('lst'));
    }else
    {
        $this->error('添加商品属性失败!');
    }
}
        return view();
    }






    public function edit()
    {
        $id = input('id');
$attr = db('goods_attr')->find($id);
$this->assign('goods_attr_edit',$attr);
if(request()->isPost())
{
    $data = input('post.');
    $res = db('goods_attr')->update($data);
    if($res)
    {
        $this->success('修改商品属性成功!',url('lst'));
    }else
    {
        $this->error('修改商品属性失败!');
    }
}
        return view();
    }

    public function del()
    {
$id = input('id');
$res = db('goods_attr')->delete($id);
if ($res)
{
    $this->success('删除商品属性成功!',url('lst'));
}else
{
    $this->error('删除商品属性失败!');
}
    }





}
