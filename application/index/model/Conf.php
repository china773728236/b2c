<?php
namespace app\index\model;
use think\Model;     
//后台控制器php文件
/* 
 *
 *  */
class Conf extends  Model //继承一下 因为它是子类
{




    //栏目的无限级分类
    public function catTree($pid=0,$level=0)
    {
        /*
         * $pid默认为最顶级
         * $level设置这个变量是为了把栏目名称单独放到新数组中
         * */
        //先拿到所有栏目
          $data = $this->order('id','asc')->select();
          //先设置一个空数组把数据重新整理
         static $arr = array();
          //遍历所有的值
           foreach ($data as $key => $value)
           {
               //如果栏目表中的PID==我们自己设置的PID(也就是说先找到最顶级的栏目，然后依次+1)
               if($value['cat_pid'] == $pid)
               {
                   //加上我们的级别
                    $value['level'] = $level;
                   //把符合条件的值加入我们自己设置的数组中
                    $arr[] = $value;
                   // unset($arr[$key]);
                   //依次向后找，直到没有符合的值后返回数据(因为数据库中的顶级栏目的PID=ID)

                   $this->catTree($value['id'],$level+1);

               }

           }

       return $arr;


    }

    //删除栏目操作
    public function delTree($id)
    {
        //先定义一个空数组
        $arr = array();
        //查询所有数据
        $data = Cat::all();
        //遍历数据
        foreach ($data as $k=>$v)
        {
            //如果发现当前栏目下的id等于查询出来的pid说明它下面有子栏目，一并取出来放到空数组中
            if($v['cat_pid'] == $id)
            {
               $arr[] = $v['id'];
               //跳出循环依次寻找
                $this->delTree($v['id']);
            }
        }
        return $arr;
    }
}
?>