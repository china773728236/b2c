<?php
namespace app\index\controller;
use app\index\model\GoodsRelevanceAttr;
use think\Controller;
use think\Db;
use think\Request;
use goodscat\GoodsCat as GoodsCatTree;
use app\index\controller\Brand;
use app\index\model\Goods as GoodsModel;
use app\index\controller\MemberPrice;
use app\index\controller\GoodsPhoto;

//商品
class Goods extends Controller
{
    //设置一下只在添加和编辑才进行查询,减少代码冗余
    protected $beforeActionList = [
        'newTypeAjax' =>  ['only'=>'add,edit'],
    ];

    protected function newTypeAjax()
    {
//商品分类
        $goods_cat_tree = new GoodsCatTree();
        $tree_res = $goods_cat_tree->catTree();

        $this->assign('goods_cat_lst',$tree_res);

    }



    public function lst()
    {
$join = [
    ['goods_cat goods_cat','goods.goods_cat_id = goods_cat.id','LEFT'],
    ['brand brand','goods.brand_id = brand.brand_id','LEFT'],
    ['goods_inventory inv','goods.goods_id = inv.goods_id','LEFT'],
    ['goods_photo pho','goods.goods_id = pho.goods_id','LEFT'],
];
        $goods_lst = db('goods')
            ->alias('goods')
            ->field('goods.*,goods_cat.id,name,brand.brand_id,brand_name,SUM(inv.goods_number) num,pho.hot_img')
            ->join($join)
            ->group('goods.goods_id')
            ->select();
       // dump($goods_lst);die;
$this->assign('goods_lst',$goods_lst);
        return view();
    }

    public function add()
    {

        //会员级别管理(为遍历每个商品会员打多少折)
        $mem_sale = Db::name('member_lv')->field('id,lv_name,lv_sale,lv_en_name')->select();
        $this->assign('mem_sale',$mem_sale);
        //查询商品类型
        $goods_type_res = Db::name('goods_type')->select();
        $this->assign('goods_type_res',$goods_type_res);
if (request()->isPost())
{

    $goods_model = new GoodsModel();

    //会员折扣率逻辑
    $sale = new MemberPrice();
    $data = input('post.');
//dump($data);die;




    //把传入的有关会员折扣率的参数传到会员关联表中处理
    $sale_data = array();
$sale_data['goods_cat_id'] = $data['goods_cat_id'];
    $sale_data['brand_id'] = $data['brand_id'];
    $sale_data['reg_pri'] = $data['reg_pri'];
    $sale_data['oese_pri'] = $data['oese_pri'];
    $sale_data['jewel_pri'] = $data['jewel_pri'];
    if($sale_data['reg_pri'] != '' && $sale_data['oese_pri'] != '' && $sale_data['jewel_pri'] != '')
    {
        //拿到新的会员关联表的自增id并存入数据库中
        $mem_new_id = $sale->addSale($sale_data);
    }else
    {
        $mem_new_id = '';
    }


//把传入的有关商品属性的参数传到商品属性关联表中处理
/*$attr_data = array();
    $attr_data['goods_attr'] = $data['goods_attr'];
    $attr_data['price'] = $data['price'];*/
//dump($attr_data);die;
/*if(!empty($attr_data['goods_attr']) && !empty($attr_data['price']))
{

}*/

//dump($data);die;
    //去掉没有用的字段好让数据能添加进商品表中
unset($data['price'],$data['goods_attr'],$data['jewel_pri'],$data['oese_pri'],$data['reg_pri']);
    $data['add_time'] = date("Y-m-d");



    $res = $goods_model->save($data);

    //获得新添加得商品id
    $new_goods_id = $goods_model->goods_id;

    $int_id = (int)$new_goods_id;

    if($res)
    {
         if ($mem_new_id != '') {

             //将会员关联表中的商品id改成刚刚添加的商品ID
             db('member_price')->where('id', $mem_new_id)->update(['goods_id' => $int_id]);
         }






        $this->success('添加商品成功!',url('lst'));
    }else
    {
        $this->error('添加商品失败!');
    }
}


        return view();
    }






    public function edit()
    {
        $id = input('goods_id');

$goods_res = db('goods')->find($id);
//dump($goods_res);die;
$this->assign('goods_edit',$goods_res);
//查询品牌
        //$brand_lst = Db::name('brand')->field('brand_id,brand_name')->where('brand_id',$goods_res['brand_id'])->select();
        //db(当前表)->alias(当前表的别名)->field(当前表的字段，另一张表的字段)->join('另一张表 表的别名'，当前表与另一张表相同的字段)->select();
        $brand_lst = Db::table('b2c_brand')
            ->alias('brand')
            ->field('brand.brand_id,brand_name,goods_cat.id')
            ->join('b2c_goods_cat goods_cat','brand.goods_cat_id = goods_cat.id')
            ->where('goods_cat_id',$goods_res['goods_cat_id'])
            ->select();
        //dump($brand_lst);die;
        $this->assign('brand_lst',$brand_lst);
        //查询会员级别
        //会员级别管理(为遍历每个商品会员打多少折)(基本前端界面)
        $mem_sale = Db::name('member_lv')->field('id,lv_name,lv_sale,lv_en_name')->select();
        //dump($mem_sale);die;
        $this->assign('mem_sale',$mem_sale);
        //查询各会员打折多少
        $mem_sale_detail = Db::table('b2c_member_price')
            ->where('goods_id',$id)
            ->find();
        $this->assign('mem_sale_detail',$mem_sale_detail);
        //dump($mem_sale_detail);die;

        //查询所有商品属性
        $goods_type_res = Db::table('b2c_goods_type')
            ->alias('type')
            ->select();
        //dump($goods_type_res);die;
        $this->assign('goods_type_res',$goods_type_res);
       /* //查询该商品下的商品属性
        $attr_res = Db::table('b2c_goods_relevance_attr')
                    ->alias('rele_attr')
                    ->field('rele_attr.attr_id,rele_attr_value,rele_attr_price,goods_attr.attr_name,attr_type,attr_values')
                    ->join('b2c_goods_attr goods_attr','rele_attr.attr_id = goods_attr.id')
                    ->join('b2c_goods goods','goods.type_id = goods_attr.type_id')
                    ->where('rele_attr.goods_id',$id)
                    ->select();
        $this->assign('attr_res',$attr_res);*/

//查询该商品下的商品属性框架
        $attr_res = db('goods_attr')->where('type_id',$goods_res['type_id'])->select();
        //dump($attr_res);die;
        $this->assign('attr_res',$attr_res);
        //查询该商品下的属性值
        $attr_val = db('goods_relevance_attr')->where('goods_id',$goods_res['goods_id'])->select();

//我们要以属性框架表的id为数组索引
        foreach ($attr_val as $val_key => $val)
        {
            $attr_val_res[$val['attr_id']][] = $val;
        }
        $this->assign('attr_val',$attr_val_res);
       //dump($attr_val_res);die;

        //查询商品相册
        $pho_lst = Db::name('goods_photo')->field('id,goods_id,hot_img,big_img,md_img,sm_img')->where('goods_id',$id)->select();
       //dump($pho_lst);die;
        $this->assign('pho_lst',$pho_lst);
if(request()->isPost())
{

    $goods_model = new GoodsModel();
    $data = input('post.');

//
//dump($data);die;
    //修改商品会员
    $goods_model->mem($data);
        $data['ud_time'] = date("Y-m-d");
//删除商品会员字段
unset($data['reg_pri'],$data['oese_pri'],$data['jewel_pri'],$data['goods_attr'],$data['price'],$data['old_goods_attr'],$data['old_price']);
    $res = $goods_model->save($data,['goods_id'=>$id]);
    if($res)
    {
        $this->success('修改商品成功!',url('lst'));
    }
    else
    {

        $this->error('修改商品失败!');
    }
}
        return view();
    }

    public function del()
    {
        $id = input('goods_id');

        $res = GoodsModel::destroy($id);

        if($res)
        {
            $this->success('删除成功',url('lst'));
        }else
        {
            $this->error('删除失败');
        }
    }


public function ajaxAddBrand()
{

//查询品牌
    $goods_cat_id = input('goods_cat_id');
    //file_put_contents("D:/environment/PHPTutorial/WWW/test/testajax/gps/mylog.log", $goods_cat_id."\r\n",FILE_APPEND);
    $brand_res = db('brand')->where(array('goods_cat_id'=>$goods_cat_id))->field('brand_id,brand_name')->select();
    $json_res = json_encode($brand_res);
    if ($json_res)
    {
        echo  $json_res;
    }

}

    public function ajaxEditBrand()
    {

//查询品牌
        $goods_cat_id = input('goods_cat_id');
        //file_put_contents("D:/environment/PHPTutorial/WWW/test/testajax/gps/mylog.log", $goods_cat_id."\r\n",FILE_APPEND);
        $brand_res = db('brand')->where(array('goods_cat_id'=>$goods_cat_id))->field('brand_id,brand_name')->find();
        $json_res = json_encode($brand_res);
        if ($json_res)
        {
            echo  $json_res;
        }

    }

//商品属性AJAX
public function attrAjax()
{
      $type_id = input('type_id');
    //file_put_contents("D:/environment/PHPTutorial/WWW/test/testajax/gps/mylog.log", $type_id."\r\n",FILE_APPEND);
    $attr_res = db('goods_attr')->where(array('type_id'=>$type_id))->select();

    $json_res = json_encode($attr_res);
    if ($json_res)
    {
        echo  $json_res;
    }
}


//商品库存
public function goodsInventory($goods_id)
{


//库存添加操作
    //查询商品属性
    //db(当前表)->alias(当前表的别名)->field(当前表的字段，另一张表的字段)->join('另一张表 表的别名'，当前表与另一张表相同的字段)->select();
    $attr_res = db('goods_relevance_attr')->alias('relevance_attr')
                ->field("relevance_attr.*,attr.attr_type,attr_name")
                ->join('goods_attr attr','relevance_attr.attr_id=attr.id')
               ->where(array('relevance_attr.goods_id'=>$goods_id,'attr.attr_type'=>1))
                ->select();
    //dump($attr_res);die;
//按照属性名称循环动态的显示库存界面
    //array(4) {
    //  ["颜色"] => array(1) {
    //    [0] => array(6) {
    //      ["id"] => int(15)
    //      ["attr_id"] => int(5)
    //  ["内存"]
    //...

    foreach ($attr_res as $k => $v)
    {
           $attr_arr[$v['attr_name']][]  = $v;
    }
    $this->assign('goods',$attr_res);
    $this->assign('attr',$attr_arr);
    //库存修改查询
    $inventory_edit = db('goods_inventory')->where('goods_id',$goods_id)->select();

    $this->assign('inventory_edit',$inventory_edit);
    //dump($attr_arr);die;
    //执行添加操作
    if(request()->isPost())
    {


        $data = input('post.');

       $res =  GoodsModel::goodsInventoryModel($data,$goods_id);
        if($res)
        {
            $this->success('操作库存成功',url('lst'));
        }else
        {
            $this->error('操作库存失败或者没有正确填写属性信息!');
        }
        // dump($data);die;
    }
    return view();
}


public function ajaxEditimg()
{
    //删除该商品的相册
     $pho_id = input('pho_id');

    //拿到图片路径将其删除
    $pho = db('goods_photo')->field('id,hot_img,big_img,md_img,sm_img')->where('id', $pho_id)->select();

    foreach ($pho as $k => $v) {
        foreach ($v as $k1 => $v1) {
            //把当前图片搞成绝对路径
            $pho_way = $_SERVER['DOCUMENT_ROOT'] . '/b2c/' . 'public/' . 'uploads/' . $v1;
            //如果找到了图片路径将图片删除
            if (file_exists($pho_way)) {
                unlink($pho_way);
            }
        }
    }
     $res = db('goods_photo')->where('id',$pho_id)->delete();

}



//商品修改之删除当前的商品属性
public function ajaxDelAttr()
{

    $arr = array();
    $arr['attr_id'] = input('attr_id');
    $arr['goods_id'] = input('goods_id');

    db('goods_relevance_attr')->delete($arr['attr_id']);
    //修改库存之前先把属性ID打散成字符串
   // $str_attr_id = implode(',',$a);
  //  db('goods_inventory')->where('goods_id',$arr['goods_id'])->setField('goods_attr_id');
}
}
