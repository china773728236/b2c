<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use app\index\model\Brand as BrandModel;
use goodscat\GoodsCat as GoodsCatTree;
use app\index\model\Goods ;
class Brand extends Controller
{

    //设置一下只在添加和编辑才进行查询,减少代码冗余
    protected $beforeActionList = [
        'newType' =>  ['only'=>'add,edit'],
    ];

    protected function newType()
    {
        //我们需要在页面显示当前栏目以便于栏目分类
        //查询商品分类
        $goods_cat_tree = new GoodsCatTree();
        $tree_res = $goods_cat_tree->catTree();
        $this->assign('goods_cat_lst',$tree_res);


    }



    public function lst()
    {
        //db(当前表)->alias(当前表的别名)->field(当前表的字段，另一张表的字段)->join('另一张表 表的别名'，当前表与另一张表相同的字段)->select();
        $res = db('brand')->alias('a')->field('a.brand_id,brand_name,brand_url,brand_img,brand_desc,brand_sort,status,b.name')->join('b2c_goods_cat b','a.goods_cat_id=b.id')->select();
        $this->assign('brand_lst',$res);
        return view();
    }

    public function add()
    {
        $bra = new BrandModel();

        if(request()->isPost())
        {
            $data = input('post.');
            //这个函数可以判断某一个值是否在当前数组中
 if(!array_key_exists('status', $data))
 {
    //如果没有值说明管理员不想展示该品牌
    $data['status'] = (string)0;//将值转换为字符串然后存入数据库中(string)

    
 }         
//dump($data);die;
            //$data['brand_time'] = date('Y-m-d');
            $res = $bra->save($data);

if($res)
{
    $this->success('添加文章成功!',url('lst'));
}else
{
    $this->error('添加文章失败!');
}


        }

        return view();
    }






    public function edit()
    {
        $id = input('brand_id');
        $res = Db::name('brand')->find($id);
        $this->assign('bar_edit',$res);
        if(request()->isPost())
        {
            //如果图片没有更换不会执行更改图片
            $bar = new BrandModel();
            $data = input('post.');
            //这个函数可以判断某一个值是否在当前数组中
            if(!array_key_exists('status', $data))
            {
                //如果没有值说明管理员不想展示该品牌
                $data['status'] = (string)0;//将值转换为字符串然后存入数据库中(string)
            }
            $res = $bar->save($data, ['brand_id' => $id]);
            if($res)
            {
                $this->success('更新品牌成功',url('lst'));
            }else{
                //dump($data);die;
                $this->error('更新失败');
            }
        }

        return view();
    }

    public function del()
    {

        $id = input('brand_id');
        //把当前品牌下的所有商品删除
        $goods_id[] = $id ;
        foreach ($goods_id as $k=>$v)
        {
Goods::destroy(['brand_id'=>$v]);
        }
        $res = BrandModel::destroy($id);
        if($res)
        {
            $this->success('删除品牌成功',url('lst'));
        }else{
            $this->error('删除失败');
        }
    }
}
