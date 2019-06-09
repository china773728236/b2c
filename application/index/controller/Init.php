<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Db;

class Init extends Controller
{
    public function _initialize()
    {


       /* $id = session('id');
        //如果用户尚未登录系统
        if (!$id || !session('username'))
         {
             $this->error('您尚未登录系统',url('login/index'));
         }

         //分配权限
         //实例化request对象
         $request = Request::instance();
         //类名
         $conf = $request->controller();
         //方法名
         $action = $request->action();
         //拼接起来
         $conf_action = $conf.'/'.$action;
         //某些方法不需要检测
         $un_check = array('Index/index','Admin/lst','Admin/logout');
 //如果不在$un_check这个数组中才进行权限判断
         if(!in_array($conf_action,$un_check))
         {
             //进入权限验证阶段
             $auth = new Auth();
            // dump($auth);die;
             if(!$auth->check($conf_action,$id))
             {
                 $this->error('没有此权限',url('index/index'));
             }
         }*/
       // dump($conf_action);die;
    }
}