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
        $help = db('cat')
            ->alias('c')
            ->field('c.cat_name,a.art_tit')
            ->join('art a','c.cat_id = a.cat_id','LEFT')
            ->where('c.cat_type',2)
            ->select();
       $this->assign('help',$help);
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