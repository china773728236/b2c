<?php
namespace app\index\model;
use think\Model;     
//后台控制器php文件
/* 
 *
 *  */
class AuthRule extends  Model //继承一下 因为它是子类
{




public  function authRuleTree($pid=0,$level=0)
{
    //echo 'ok';die;
    //先拿到所有的数据
    $data = $this->select();
   // dump($data);die;
   static  $arr=array();

    //遍历所有的数据
    foreach ($data as $k => $v)
    {

        if($v['pid'] == $pid)
        {
            $v['dataid'] = $this->getParentId($v['id'],false);
            $v['level'] = $level;

            $arr[] = $v;
            $this->authRuleTree($v['id'],$level+1);

        }

    }

    return $arr;

}



//删除管理员权限
public function delTree($id)
{

    //定义一个空数组
 static $arr = array();
    $pid_res = AuthRule::all();
    //遍历所有数据
    foreach ($pid_res as $k => $v)
    {
        if($v['pid'] == $id)
        {
            //在遍历出来的值中添加一个id字段
            $arr[] = $v['id'];
            $this->delTree($v['id']);
        }
    }
  //  dump($arr);die;
    return $arr;


}


//拿到所有的上级栏目的ID用于权限管理
    public function getParentId($id,$clear=false)
    {

        //定义一个空数组
        static $arr = array();
        //如果$clear=真清空掉数组
        if(!$clear)
        {
            $arr = array();
        }
        $pid_res = AuthRule::all();
        //遍历所有数据
        foreach ($pid_res as $k => $v)
        {
            if($v['id'] == $id)
            {
                //在遍历出来的值中添加一个id字段
                $arr[] = $v['id'];
                $this->getParentId($v['pid'],true);
            }
        }
          //我们需要的是一个字符串并且ID从小到大排列

asort($arr);
        $arr_str = implode('-',$arr);
       // dump($arr_str);die;
        return $arr_str;


    }




}
?>