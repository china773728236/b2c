<?php
namespace app\index\controller;
use think\Db;
use app\index\model\Cat as CatModel;
use app\index\model\GoodsCat as GoodsCatModel;
use goodscat\GoodsCat as GoodsCatTree;
use app\index\model\Goods ;
use app\index\model\Brand ;
class GoodsCat extends Init
{


    public function lst()
    {
        //我们需要在页面显示当前栏目以便于栏目分类
        $goods_cat_tree = new GoodsCatTree();


        //排序
        if (request()->post())
        {
            $cat_obj = db('goods_cat');

            $data = input('post.');

            $goods_cat_tree->sortCat($data['sort'],$cat_obj);

            $this->success('排序成功',url('lst'));
        }
      //  $cat = new CatModel();
        // dump ($cat);die;

        //把数据传到无限极分类中

$tree_res = $goods_cat_tree->catTree();

        $this->assign('goods_cat_lst',$tree_res);
        return view();
    }

    //添加栏目
    public function add()
    {

        if(request()->isPost())
        {
            $data =   input('post.');
            $goos_cat = new GoodsCatModel();
            //dump($data);die;



            $res = $goos_cat->save($data);

            if($res)
            {
                $this->success('添加商品分类成功!',url('lst'));
            }else
            {
                $this->error('添加商品分类失败!');
            }


        }

        //我们需要在页面显示当前栏目以便于栏目分类
       $goods_cat_tree = new GoodsCatTree();
        $cat_pid_res = $goods_cat_tree->catTree();
        $this->assign('cat_pid',$cat_pid_res);
        return view();
    }


    //编辑栏目
    public function edit()
    {
        //我们需要在页面显示当前栏目以便于栏目分类
        $goods_cat_tree = new GoodsCatTree();
        $cat_pid_res = $goods_cat_tree->catTree();
        $this->assign('cat_pid',$cat_pid_res);
        //拿到当前的数据
        $id = input('id');

        $data = Db::name('goods_cat')->find($id);

        $this->assign('now_goods_cat',$data);
        //如果是post提交就开始更改
        if (request()->isPost())
        {
            $data = input('post.');
            //dump($data);die;
$goods_model = new GoodsCatModel();
            $res = $goods_model->save($data, ['id' => $id]);
            if($res)
            {
                $this->success('更改栏目成功!',url('lst'));
            }else
            {
                $this->error('没有更改栏目!',url('lst'));
            }

        }
        return view();
    }



    //删除的方法
    public function del()
    {
        $id = input('id');

        $goods_cat_tree = new GoodsCatTree();

        $res_tree = $goods_cat_tree->delTree($id);

        //删除栏目同时删除栏目下面的所有文章

        //把顶级栏目ID也赋值进去
        $res_brand_goods[] = $id;
        foreach ($res_brand_goods as $k => $v)
        {

            Brand::destroy(['goods_cat_id'=>$v]);
            Goods::destroy(['goods_cat_id'=>$v]);
        }
        //如果有子级栏目，就进行批量删除
        //$res_tree[] = $id;
        //dump($art_id);die;
        if($res_tree)
        {
//
            $res = Db::table('b2c_goods_cat')->delete($res_tree);
            if ($res)
            {
                $this->success('删除栏目成功!',url('lst'));
            }else
            {
                $this->error('删除栏目失败!');
            }
        }


    }
}
