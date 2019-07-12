<?php
namespace app\front\controller;
use think\Controller;
use think\Request;
use think\Db;

class Init extends Controller
{
    public function _initialize()
    {

//网站帮助
        //db(当前表)->alias(当前表的别名)->field(当前表的字段，另一张表的字段)->join('另一张表 表的别名'，当前表与另一张表相同的字段)->select();
        $cat_res = db('cat')
            
            ->field('cat_id,cat_name')
            ->where('cat_type',2)
           
            ->select();
            //循环取出当前栏目的文章
            foreach ($cat_res as $key => $value) {
              $cat_res[$key]['art'] = db('art')->field('art_tit')->where('cat_id',$value['cat_id'])->select();
            }
           // dump($cat_res);die;
       $this->assign('help',$cat_res);
        //网站左侧品牌分类
       // $left = db('goods_cat')->field('id,name,pid')->select();
       // $this->brandCatArr();

    }




    //处理商品分类数组
    public function brandCatArr($pid=0)
    {

       static $brand_cat = array();
        $left = db('goods_cat')->field('id,name,pid')->select();
        //dump($left);die;
        foreach ($left as $k => $v)
        {
            if($pid == $v['pid'])
            {

                $brand_cat[] = $v;
                $this->brandCatArr($v['id']);
            }

        }
        dump($brand_cat);die;
    }



}