<?php
namespace app\index\controller;
use think\Db;
use app\index\model\Conf as ConfModel;
class Conf extends Init
{
    public function lst()
    {
        $res = Db::name('conf')->select();
        $this->assign('res',$res);
        return view();
    }

    public function add()
    {
        if(request()->isPost())
        {
            $data = input('post.');
            //把所有中文逗号替换成英文逗号

            if($data['conf_optional_value'])
            {
                $data['conf_optional_value'] = str_replace('，',',',$data['conf_optional_value']);
            }
            $res = Db::name('conf')->insert($data);
            if ($res)
            {
                $this->success('添加配置成功',url('conf/lst'));
            }else
            {
                $this->error('添加配置失败');
            }
        }
        return view();
    }

    public function edit()
    {
        $id = input('id');
        //先查询数据
        $res = db('conf')->find($id);
        $this->assign('conf',$res);
        if(request()->isPost())
        {
            $data = input('post.');
            //把所有中文逗号替换成英文逗号
            if($data['conf_optional_value'])
            {
                $data['conf_optional_value'] = str_replace('，',',',$data['conf_optional_value']);
            }
            $res = db('conf')->update($data);
            if ($res)
            {
                $this->success('更新配置成功',url('conf/lst'));
            }else
            {
                $this->error('更新配置失败');
            }
        }
        return view();
    }


    public function del()
    {
$id =input('id');
$res = db('conf')->delete($id);
if ($res)
{
    $this->success('删除配置成功',url('conf/lst'));
}else
{
    $this->error('删除配置失败');
}
    }


    //网站的配置选项
    public function conf()
    {
        $res = Db::name('conf')->select();
        $this->assign('conf',$res);

        if(request()->isPost())
        {
            $data = input('post.');
           // dump($data);die;
           //判断一下是否都有值

                //dump($data);die;
                //这里没有根据ID修改而是根据英文名称更改所以需要逐条的修改数据
                foreach ($data as $k => $v)
                {
                  $res =  db('conf')->where('conf_enname',$k)->update(['conf_value'=>$v]);

                }
                if ($res)
                {
                    $this->success('配置已生成');
                }else
                {
                    $this->success('配置已生成',url('conf/conf'));
                }

        }
        return view();
    }

    //测试
    public function test()
    {
        $res_shangpin = Db::name('conf')->where(array('shop_type'=>'shangpin'))->select();
        $this->assign('conf_shangpin',$res_shangpin);
       // dump($res_shangpin);die;
        $res_dianpu = Db::name('conf')->where(array('shop_type'=>'dianpu'))->select();
        $this->assign('conf_dianpu',$res_dianpu);

        if(request()->isPost())
        {
            $data = input('post.');
             dump($data);die;
            //判断一下是否都有值

            //dump($data);die;
            //这里没有根据ID修改而是根据英文名称更改所以需要逐条的修改数据
            foreach ($data as $k => $v)
            {
                $res =  db('conf')->where('conf_enname',$k)->update(['conf_value'=>$v]);

            }
            if ($res)
            {
                $this->success('配置已生成');
            }else
            {
                $this->success('配置已生成',url('conf/conf'));
            }

        }
        return view();
    }


}
