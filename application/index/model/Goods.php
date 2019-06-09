<?php
namespace app\index\model;
use think\Model;
use think\Db;
use think\Image;
use app\index\model\Common;
use app\index\model\GoodsPhoto;//便于批量修改相册
use app\index\model\MemberPrice;//便于修改会员价格
use app\index\model\GoodsInventory;//便于修改库存
//后台控制器php文件
/*
 *
 *  */
class Goods extends  Model //继承一下 因为它是子类
{
    protected static function init()
    {
        //商品相册的添加
        Goods::afterInsert(
            function ($data){
                $add_img = 'add_img';
                $db = 'goods_photo';
                Common::imgMoreInsert($data,$add_img,$db);

            });

//商品相册的修改
        Goods::afterUpdate(
            function ($data) {
                $goods_id = $data->goods_id;
                //首先得到商品属性的数组

                $attr_data = input('post.');
//dump($attr_data);die;




                if(!empty($attr_data['old_goods_attr']) && !empty($attr_data['old_price']) && !empty($attr_data['price'])
                    && !empty($attr_data['goods_attr']))
                {



//如果用户在某个商品属性里面没有填写内容，删除当前属性的空信息，让其不能添加到数据表中
                    //遍历价格
                    foreach ($attr_data['price'] as $k => $val)
                    {

                        //如果有没有填写某个属性的价格
                        if(empty($val))
                        {
//删除当前数组的节点
                            unset($attr_data['price'][$k]);
                        }


                    }

                    //遍历属性详细信息
                    foreach ($attr_data['goods_attr'] as $attr_id => $attr_val)
                    {

                        foreach ($attr_val as $k=>$v)
                        {
                            //如果有没有填写属性信息
                            if(empty($v))
                            {
//删除当前数组的节点
                                unset($attr_data['goods_attr'][$attr_id]);
                            }
                        }

                    }
                    //两次输入的值不能相同
                    $arr_price = array_merge_recursive($attr_data['old_goods_attr'],$attr_data['goods_attr']);
                    $arr = array();
                    foreach ($arr_price as $k3 => $v3)
                    {
                        foreach ($v3 as $k4 => $v4)
                            if($v4)
                            {
                                $arr[] = $v4;
                            }
                    }

                    if(count($arr) != count(array_unique($arr)) )
                    {

                        Common::hint('修改属性失败','javascript:history.back(-1);');
                    }
                    //dump($attr_data);die;
//定义一个空数组方便将结果返回给控制器层
                    // static $arr = array();

//如果商品属性这一栏在接收到的数组中
                    if(isset($attr_data['goods_attr']))
                    {
                        //由于属性中的详细信息与该属性的价格是一一对应的，所以如果$i=0，那么当前的该属性
                        //对应的价格对应的就是第一个属性的详细信息
                        $i = 0;
                        //dump($attr_data['goods_attr']);die;
                        //先遍历商品关联属性(遍历二维数组第一层)
                        foreach ($attr_data['goods_attr'] as $attr_id => $attr_values)
                        {
                            if(is_array($attr_values))
                            {
                                //如果该商品有商品属性信息
                                if($attr_values != '')
                                {
                                    //(遍历二维数组第二层)
                                    foreach ($attr_values as $k => $attr_value_detail)
                                    {

                                      $res_attr =  db('goods_relevance_attr')->insert(['attr_id'=>$attr_id,'rele_attr_value'=>$attr_value_detail,'rele_attr_price'=>$attr_data['price'][$i],'goods_id'=>$goods_id]);
                                        if(!empty($attr_data['price']))
                                        {
                                            $i++;
                                        }


                                    }

                                }
                            }
                        }


                    }
if($res_attr)
{
    $succ_url = 'http://localhost/b2c/public/index.php/index/goods/lst';
    Common::hint('修改商品属性成功',$succ_url);
}



                }

                //在原来的基础上修改商品属性
                if(isset($attr_data['old_goods_attr']))
                {
                    //两次输入的值不能相同
                    $arr_price = array_merge_recursive($attr_data['old_goods_attr']);

                    $arr = array();
                    foreach ($arr_price as $k3 => $v3)
                    {
                        foreach ($v3 as $k4 => $v4)

                            if($v4)
                            {
                                $arr[] = $v4;
                            }
                    }

                    if(count($arr) != count(array_unique($arr)) )
                    {

                        Common::hint('修改属性失败','javascript:history.back(-1);');
                    }
                    //把提交的价格数组的键值重新组合成新的数组与属性值一一对应
                    $arr_old_price = $attr_data['old_price'];
                    //取出键
                    $arr_old_price_k = array_keys($arr_old_price);
                    //取出值
                    $arr_old_price_v = array_values($arr_old_price);

                    //由于属性中的详细信息与该属性的价格是一一对应的，所以如果$i=0，那么当前的该属性
                    //对应的价格对应的就是第一个属性的详细信息
                    $i = 0;
                    //dump($attr_data['goods_attr']);die;
                    //先遍历商品关联属性(遍历二维数组第一层)
                    foreach ($attr_data['old_goods_attr'] as $attr_id => $attr_values)
                    {
                        if(is_array($attr_values))
                        {
                            //dump($attr_values);die;
                            //如果该商品有商品属性信息
                            if($attr_values != '')
                            {
                                //(遍历二维数组第二层)
                                foreach ($attr_values as $k => $attr_value_detail)
                                {

                                 $res_attr_two =   db('goods_relevance_attr')
                                        ->where('id',$arr_old_price_k[$i])
                                        ->update(['rele_attr_value'=>$attr_value_detail,'rele_attr_price'=>$arr_old_price_v[$i]]);

                                    $i++;



                                }

                            }
                        }
                    }
if($res_attr_two)
{
    $succ_url = 'http://localhost/b2c/public/index.php/index/goods/lst';
    Common::hint('修改商品属性成功',$succ_url);
}

                }



                //如果拿到了系统默认存放图片的路径
                $temp_name = $_FILES['img']['tmp_name'];
                if ($temp_name) {
                    //如果用户没有对相册进行任何操作
                    if(!empty($temp_name) )
                    {

                        //遍历价格
                        foreach ($temp_name as $k => $val)
                        {

                            if(!empty($val))
                            {
                                //获取到用户上传的文件
                                $pho_res = request()->file('img');
                                if ($pho_res) {

                                    foreach ($pho_res as $file)
                                    {

                                        // 移动到框架应用根目录/public/uploads/ 目录下
                                        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');

                                        if ($info) {
                                            //先把第一张图片地址单独提取出来好重新赋值
                                            $img_name = $info->getFilename();

                                            $img = date("Ymd") . DS . $img_name;
                                            $big_img =  date("Ymd") . DS . 'big_' . $img_name;
                                            $md_img =  date("Ymd") . DS . 'md_' . $img_name;
                                            $sm_img =  date("Ymd") . DS . 'sm_' . $img_name;

                                            //生成缩略图

                                            $thumb = image::open(IMG . $img);
                                            $thumb->thumb(500,500)->save(IMG . $big_img);
                                            $thumb->thumb(200,200)->save(IMG . $md_img);
                                            $thumb->thumb(80,80)->save(IMG . $sm_img);
                                            // $img_path = IMG.$img;

                                            /*   //释放info变量
                                                if(file_exists($img_path))
                                                {
                                                    //释放变量
                                                    unset($info);
                                                    //删除主图
                                                    unlink($img_path);
                                                }*/
                                            $pho_res = db('goods_photo')
                                                ->insert(['goods_id'=>$data->goods_id,'hot_img'=>$img,'big_img'=>$big_img,'md_img'=>$md_img,'sm_img'=>$sm_img]);



                                        }else
                                        {

                                            echo $file->getError();
                                        }
                                    }
                                    if($pho_res)
                                    {
                                        $succ_url = 'http://localhost/b2c/public/index.php/index/goods/lst';
                                        Common::hint('修改商品相册成功',$succ_url);
                                    }
                                }
                            }
                        }

                    }









                }
            });




        //添加商品属性
        Goods::afterInsert(
            function ($data){

                $goods_id = $data->goods_id;
                //首先得到商品属性的数组

                $attr_data = input('post.');
//dump($attr_data);die;
//如果用户在某个商品属性里面没有填写内容，删除当前属性的空信息，让其不能添加到数据表中
                if(!empty($attr_data['goods_attr']) || !empty($attr_data['price']))
                {
                    //遍历价格
                    foreach ($attr_data['price'] as $k => $val)
                    {
                        //如果有没有填写某个属性的价格
                        if(empty($val))
                        {
//删除当前数组的节点
                            unset($attr_data['price'][$k]);
                        }
                    }
                    //遍历属性详细信息
                    foreach ($attr_data['goods_attr'] as $attr_id => $attr_val)
                    {
                        foreach ($attr_val as $k=>$v)
                        {
                            //如果有没有填写属性信息
                            if(empty($v))
                            {
//删除当前数组的节点
                                unset($attr_data['goods_attr'][$attr_id]);
                            }
                        }
                    }
                }

//dump($attr_data);die;
//定义一个空数组方便将结果返回给控制器层
                // static $arr = array();

//如果商品属性这一栏在接收到的数组中
                if(isset($attr_data['goods_attr']))
                {

                    //由于属性中的详细信息与该属性的价格是一一对应的，所以如果$i=0，那么当前的该属性
                    //对应的价格对应的就是第一个属性的详细信息
                    $i = 0;
                    //dump($attr_data['goods_attr']);die;
                    //先遍历商品关联属性(遍历二维数组第一层)
                    foreach ($attr_data['goods_attr'] as $attr_id => $attr_values)
                    {
                        if(is_array($attr_values))
                        {
                            //如果该商品有商品属性信息
                            if($attr_values != '')
                            {
                                //(遍历二维数组第二层)
                                foreach ($attr_values as $k => $attr_value_detail)
                                {

                                    db('goods_relevance_attr')->insert(['attr_id'=>$attr_id,'rele_attr_value'=>$attr_value_detail,'rele_attr_price'=>$attr_data['price'][$i],'goods_id'=>$goods_id]);
                                    if(!empty($attr_data['price']))
                                    {
                                        $i++;
                                    }


                                }

                            }
                        }
                    }


                }

            });



        Goods::beforeDelete(
            function ($data)
            {

                $res = db('goods_photo')->where('goods_id',$data->goods_id)->select();


                foreach ($res as $k => $v)
                {
                    $v_pho = array_slice($v,2);//移除没有用的值
                    foreach ($v_pho as $k1 => $v1)
                    {

                        $img_path = $_SERVER['DOCUMENT_ROOT'].'/b2c/public/uploads/'.$v1;
                        if(file_exists($img_path))
                        {

//获取当前的文件夹路径用来判断文件夹是否为空
                            $folder = pathinfo($img_path);

                            $folder = $folder['dirname'];


                            //dump('不空');die;
                            unlink($img_path);
                            //判断文件夹是否为空
                            $judge = array_diff(scandir($folder),array('..','.'));
                            //dump($judge);die;
                            if(!$judge)
                            {
                                rmdir($folder);
                            }
                        }else
                        {
                            Common::hint('文件夹为空','javascript:history.back(-1);');
                        }
                    }
                    GoodsPhoto::destroy(['goods_id'=>$v['goods_id']]);
                }

//删除与当前商品关联的表
                $attr_res = db('goods_relevance_attr')->where('goods_id',$data->goods_id)->select();//删除商品属性
                foreach ($attr_res as $k_attr => $v_attr)
                {
                    GoodsRelevanceAttr::destroy(['goods_id'=>$v_attr['goods_id']]);
                }

                $goods_mem_res = db('member_price')->where('goods_id',$data->goods_id)->select();//删除会员价格
                foreach ($goods_mem_res as $k_mem => $v_mem)
                {
                    MemberPrice::destroy(['goods_id'=>$v_mem['goods_id']]);
                }

                $goods_inventory_res = db('goods_inventory')->where('goods_id',$data->goods_id)->select();//删除库存
                foreach ($goods_inventory_res as $k_inv => $v_inv)
                {
                    GoodsInventory::destroy(['goods_id'=>$v_inv['goods_id']]);
                }

            }
        );


        /* Goods::beforeUpdate(
           function($data)
           {

           }
         );*/


    }



//处理商品库存添加与修改
    public static function goodsInventoryModel($data,$goods_id)
    {
        //如果用户想要修改商品库存把当前修改的商品信息删除,而后再重新添加
        db('goods_inventory')->where('goods_id',$goods_id)->delete();
        $goods_attr = $data['goods_attr_id'];
        $goods_num = $data['goods_number'];

//为判断用户输入有没有重复输入或者根本没有输入做准备(因为要
//判断用户所有选项都一样才die掉，现在是第一次循环，我们可以在首次循环的时候就die掉程序)

        //循环内存id
        foreach ($goods_attr['内存'] as $k_nei => $v_nei)//这里只需要判断如果左边的下拉框相同就停止程序
        {
            if ($v_nei)
            {
                $nei_arr [] = $v_nei;
            }

        }

        if (count($nei_arr) != count(array_unique($nei_arr)))
        {
            Common::hint('操作库存有误','javascript:history.back(-1);');
        }



        //先遍历商品库存量
        foreach ($goods_num as $k => $num_val)
        {

//新建一个空数组用于存放打散的字符串(必须放在这里，不然这个数组之前循环的数据不会清除)
            $str = array();

            //循环商品属性id
            foreach ($goods_attr as $k1 => $attr_val)
            {

                if( intval($attr_val[$k]) <=0)
                {

                    Common::hint('操作库存有误','javascript:history.back(-1);');
                    //退出循环

                    continue 2;
                }
                //把商品属性id放到一个新的数组中(只能这样不然后面的数据会覆盖前面的数据)
                $str[] = $attr_val[$k];


            }


            $attr_id = implode(',',$str);
            $inventory_res = db('goods_inventory')->insert([
                'goods_id' => $goods_id,
                'goods_number' => $num_val,
                'goods_attr_id' => $attr_id
            ]);

            //$str['goods_attr_id'] = $attr_id;
        }
        if($inventory_res)
        {
            return 1;
        }else
        {
            return false;
        }

    }


//修改商品会员
    public function mem($data)
    {
        if($data)
        {

            $res = MemberPrice::where('goods_id', $data['goods_id'])
                ->update(['goods_cat_id' => $data['goods_cat_id'], 'brand_id' => $data['brand_id']
                    , 'goods_id' => $data['goods_id'], 'reg_pri' => $data['reg_pri'],
                    'oese_pri' => $data['oese_pri'], 'jewel_pri' => $data['jewel_pri']
                ]);
            if($res)
            {

                $succ_url = 'http://localhost/b2c/public/index.php/index/goods/lst';
                Common::hint('修改会员成功',$succ_url);
            }
        }
    }


}