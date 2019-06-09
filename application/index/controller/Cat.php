<?php
namespace app\index\controller;
use think\Db;
use app\index\model\Cat as CatModel;
use app\index\model\Art as ArtModel;
use cattree\Cattree;
class Cat extends Init
{


    public function lst()
    {
        $cat = new cattree();
        //排序
        if (request()->post())
        {
            $cat_obj = db('cat');
            $data = input('post.');
            $cat->sortCat($data['cat_sort'],$cat_obj);

            $this->success('排序成功',url('lst'));
        }
      //  $cat = new CatModel();
        // dump ($cat);die;

        //把数据传到无限极分类中

        $cat_lst_res = $cat->catTree();
        //dump ($cat_lst_res);die;
        $this->assign('cat_lst',$cat_lst_res);
        return view();
    }

    //添加栏目
    public function add()
    {
        if(request()->isPost())
        {
            $data =   input('post.');
            //dump($data);die;


//这个函数可以判断某一个值是否在当前数组中
            if(!array_key_exists('cat_nav', $data))
            {
                //如果没有值说明管理员不想展示该品牌
                $data['cat_nav'] = (string)0;//将值转换为字符串然后存入数据库中(string)
            }
            $res = Db::name('cat')->insert($data);

            if($res)
            {
                $this->success('添加栏目成功!',url('cat/lst'));
            }else
            {
                $this->error('添加栏目失败!');
            }


        }

        //我们需要在页面显示当前栏目以便于栏目分类
        $cat = new cattree();
        $cat_pid_res = $cat->catTree();
        $this->assign('cat_pid',$cat_pid_res);
        return view();
    }


    //编辑栏目
    public function edit()
    {
        //我们需要在页面显示当前栏目以便于栏目分类
        $cat = new CatModel();
        $cat_pid_res = $cat->catTree();
        $this->assign('cat_pid',$cat_pid_res);
        //拿到当前的数据
        $id = input('cat_id');

        $data = Db::name('cat')->find($id);

        $this->assign('now_cat',$data);
        //如果是post提交就开始更改
        if (request()->isPost())
        {
            $data = input('post.');
//这个函数可以判断某一个值是否在当前数组中
            if(!array_key_exists('cat_nav', $data))
            {
                //如果没有值说明管理员不想展示该品牌
                $data['cat_nav'] = (string)0;//将值转换为字符串然后存入数据库中(string)
            }
            $res = db('cat')->update($data);
            if($res)
            {
                $this->success('更改栏目成功!',url('cat/lst'));
            }else
            {
                $this->error('没有更改栏目!',url('cat/lst'));
            }

        }
        return view();
    }



    //删除的方法
    public function del()
    {
        $id = input('cat_id');

        $cat = new CatModel();

        $res_tree = $cat->delTree($id);

        //删除栏目同时删除栏目下面的所有文章

        //把顶级栏目ID也赋值进去
        $res_art[] = $id;
        foreach ($res_art as $k => $v)
        {

            //把当前栏目ID赋值给所有属于该分类文章的art_cat_id字段，然后用文章表的art_cat_id字段删除文章数据
            ArtModel::destroy(['cat_id'=>$v]);
        }
        //如果有子级栏目，就进行批量删除
        //$res_tree[] = $id;
        //dump($art_id);die;
        if($res_tree)
        {
//
            $res = Db::table('b2c_cat')->delete($res_tree);
            if ($res)
            {
                $this->success('删除栏目成功!',url('cat/lst'));
            }else
            {
                $this->error('删除栏目失败!');
            }
        }


    }
}
