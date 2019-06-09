<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use app\index\model\GoodsPhoto as GoodsPhotoModel;
//use app\index\controller\Goods;
//商品大中图表
class GoodsPhoto extends Controller
{
    public function lst()
    {
echo 'ok';die;
    }

    public function addPhoto($data)
    {

$model = new GoodsPhotoModel();

 $res = $model->save();


 if ($res)
 {
     $new_goods_photo_id = $model->id;
     return (int) $new_goods_photo_id;

 }else
 {
     return false;
 }
    }








    public function edit()
    {
        $data = input('post.');
        dump($data);die;
        $res =GoodsPhotoModel::update();
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
