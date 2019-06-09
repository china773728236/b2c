<?php
namespace app\index\controller;
use app\index\model\Admin;
use think\Controller;
use think\Db;
use think\Request;

class Login extends Controller
{
    public function index()
    {
        if (request()->isPost())
        {
            //$this->check(input('code'));
            $data = input('post.');
            //判断用户是否勾选自动登录
            $test = in_array("on",$data);
            //查询数据(用模型查询方式通过名字获取到用户ID)
            $res = Admin::getByUsername($data['username']);
            //如果管理员输入的用户名和密码与数据库的一致则跳转到主页
            if($data['username'] == $res['username'] )
            {
                //验证密码
                if (md5($data['pwd']) == $res['pwd'])
                {
                    /*//自动登录
                    if($test == true)
                    {
                        cookie(['prefix' => 'bick_', 'expire' => 3600]);
                        cookie('username',$data['username'],3600);
                        cookie('password',md5($data['pwd']),3600);
                        session('id',$res['id']);
                        session('username',$res['username']);
                        url('index/index');
                    }else
                    {
                        cookie(null,'b2c_');
                    }*/


                    session('id',$res['id']);
                    session('username',$res['username']);
                   // dump($a);die;
                    $this->success('登陆成功!',url('index/index'));
                    //同时初始化SESSION
                    //session('id',$res['id']);
                  //  $a =  session('username',$res['username']);


                }else
                {
                    $this->error('用户名或密码输入错误，请重新输入!');
                }
            }else
            {
                $this->error('用户名或密码输入错误，请重新输入!');
            }
        }
        return view();
    }



    // 验证码检测
   /* public function check($code='')
    {
        if (!captcha_check($code)) {
            $this->error('验证码错误');
        } else {
            return true;
        }
    }*/



}
