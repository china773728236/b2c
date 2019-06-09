<?php
namespace app\index\controller;
use think\Db;
use app\index\model\Art as ArtModel;
use cattree\Cattree as CatModel;
class Art extends Init
{
//设置一下只在添加和编辑才进行无限极分类查询,减少代码冗余
   protected $beforeActionList = [
        'newCat' =>  ['only'=>'add,edit'],
    ];

    protected function newCat()
    {
        //我们需要在页面显示当前栏目以便于栏目分类
        $cat = new CatModel();
        $cat_pid_res = $cat->catTree();
        $this->assign('cat_pid',$cat_pid_res);
    }
    public function lst()
    {
        //db(当前表)->alias(当前表的别名)->field(当前表的字段，另一张表的字段)->join('另一张表 表的别名'，当前表与另一张表相同的字段)->select();
        $res = db('art')->alias('a')->field('a.art_id,art_tit,art_img,art_author,art_keywords,art_desc,art_email,art_url,art_status,art_nav,art_time,b.cat_name')->join('b2c_cat b','a.cat_id=b.cat_id')->select();
        //dump($res);die;
        $this->assign('art',$res);
        return view();
    }

    public function add()
    {
		
        $art = new ArtModel();

        if(request()->isPost())
        {
			
            $data = input('post.');
//这个函数可以判断某一个值是否在当前数组中
            if(!array_key_exists('art_status', $data))
            {
                //如果没有值说明管理员不想展示该品牌
                $data['art_status'] = (string)0;//将值转换为字符串然后存入数据库中(string)
            }
            if (!array_key_exists('art_nav', $data)) {
                //如果没有值说明管理员不想展示该品牌
                $data['art_nav'] = (string)0;//将值转换为字符串然后存入数据库中(string)
            }
            //dump($data);die;
           /* $validate = validate('article');
            if(!$validate->check($data))
            {
                $this->error($validate->getError());
            }*/
            //把时间添加进是数据库中

          $data['art_time'] = date('Y-m-d');
            $res = $art->save($data);
            if($res)
            {
                $this->success('添加文章成功!',url('art/lst'));
            }else
            {
                $this->error('添加文章失败!');
            }
        }
        return view();
    }

    public function edit()
    {
        $id = input('art_id');

        //先查询数据
        $art_res = Db::name('art')->find($id);
//dump($art_res);die;
        $this->assign('art_res',$art_res);
        if(request()->isPost())
        {
            $data = input('post.');
            //这个函数可以判断某一个值是否在当前数组中
            if(!array_key_exists('art_nav', $data))
            {
                //如果没有值说明管理员不想展示该品牌
                $data['art_nav'] = (string)0;//将值转换为字符串然后存入数据库中(string)
            }

            //这个函数可以判断某一个值是否在当前数组中
            if(!array_key_exists('art_status', $data))
            {
                //如果没有值说明管理员不想展示该品牌
                $data['art_status'] = (string)0;//将值转换为字符串然后存入数据库中(string)
            }
            
            $art = new ArtModel();
			//dump($data);die;
            //把时间更换
            $data['art_time'] = date('Y-m-d');
            $res = $art->save($data, ['art_id' => $id]);
            //dump($res);die;
            if ($res) {

                $this->success('成功更新一篇文章', url('art/lst'));
            } else {
                $this->error('更新文章失败');
            }
/*//如果把这段代码放在最前面会把数据库中的缩略图变为空，他会先执行前面的代码所以设在后面没有影响
//缩略图为空的情况下的文章修改逻辑
            $data['art_img'] = "" ;
            if($data['art_img'] == "")
            {
                $res = db('art')->update($data);
                if ($res)
                {
                    $this->success('更新文章成功',url('art/lst'));
                }else
                {
                    $this->error('退出更新',url('art/lst'));
                }
            }
           // dump($data);die;

            /*if($_FILES['art_img']['tmp_name'] == "")
            {
                //把时间更换
                $data['art_time'] = date('Y-m-d');
               //
               $res = db('article')->update($data);
               // dump($res);die;
                   if($res)
                   {
                       $this->success('成功更新一篇文章',url('article/lst'));
                   }else
                   {
                       $this->error('您已放弃更新',url('article/lst'));
                   }
            }else
                {

           // }*/
        }
        return view();
    }


    public function del()
    {
        $id = input('art_id');
        //dump($id);die;
        $res = ArtModel::destroy($id);
        if($res)
        {
            $this->success('删除成功',url('art/lst'));
        }else
        {
            $this->error('删除失败');
        }

    }
}
