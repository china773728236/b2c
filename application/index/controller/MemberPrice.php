<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use app\index\model\MemberPrice as MemberPriceModel;
//引用商品控制器的获取会员关联表新添加的主键id的方法
use app\index\controller\Goods;
//会员功能关联表
class MemberPrice extends Controller
{
    public function lst()
    {
echo 'ok';die;
    }

    public function addSale($sale_data)
    {
$model = new MemberPriceModel();
 $res = $model->save($sale_data);
 $new_member_price_id = $model->id;
 if ($res)
 {

     return (int)$new_member_price_id;

 }else
 {
     echo 'shibai';die;
 }
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
