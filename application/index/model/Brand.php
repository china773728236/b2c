<?php
namespace app\index\model;
use think\Model;
use think\Db;
//后台控制器php文件
/*
 *
 *  */
class Brand extends  Model //继承一下 因为它是子类
{
    protected static function init()
    {



        Brand::beforeInsert(

            function ($data){


//如果拿到了系统默认的存放路径(_FILE常量是一个数组)
if($_FILES['brand_img'] ['tmp_name'])
{
    //获取表单上传的图片
    $file = request()->file('brand_img');
    //dump($file);die;
    // 移动到框架应用根目录/public/uploads/ 目录下
    $info = $file->move(ROOT_PATH.'public'.DS.'uploads');
    //如果成功移动，获取绝对路径并将路径赋予到数据表中
    if($info)
    {
        $file_path = '/b2c/'.'public'.DS.'uploads'.'/'.$info->getSaveName();
       $data['brand_img'] = $file_path;
    }
}
            }
        );

        Brand::beforeUpdate(

            function ($data)
            {

                //还是先拿到当前图片
                if($_FILES['brand_img'] ['tmp_name'])
                {
                    //拿到图片将其删除
                    $img = Brand::find($data->brand_id);
                   // dump($img);die;
                    $img_path = $_SERVER['DOCUMENT_ROOT'].$img['brand_img'];

                    //如果找到了图片路径将图片删除
                    if(file_exists($img_path))
                    {
                        //如果之前有图片才删除原来的图片
                        if($img['brand_img'] != "") {
                            unlink($img_path);
                        }
                        // 获取表单上传文件 例如上传了001.jpg
                        $file = request()->file('brand_img');

                        // 移动到框架应用根目录/public/uploads/ 目录下
                        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');

                        //如果成功移动，获取绝对路径并将路径赋予到数据表中
                        if ($info) {
                            $file_path = '/b2c/' . 'public' . DS . 'uploads' . '/' . $info->getSaveName();
                            //dump($file_path);die;
                            $data['brand_img'] = $file_path;

                        }


                    }
                }
            }
        );


        Brand::beforeDelete(

            function ($data)
            {

                //查找当前品牌下的所有数据
                $res = Brand::find($data->brand_id);
                if($res['brand_img'] == '')
                {
                    return true;
                }
                $img_path = $_SERVER['DOCUMENT_ROOT'].$res['brand_img'];
                //dump();die;
                //如果图片路径正确无误
                if(file_exists($img_path))
                {
                      //获取当前的文件夹路径用来判断文件夹是否为空
                    $folder = pathinfo($img_path);
                    $folder = $folder['dirname'];
                    //先删除当前图片
                    unlink($img_path);
                    //如果删除图片后当前文件夹是空的把文件夹一并删除
                    //判断文件夹是否为空
                    $judge_file = array_diff(scandir($folder),array('..',','));
if(!$judge_file)
{
    rmdir($folder);
}
                }else{
                    echo '图片路径好像有问题';die;
                }
            }
        );
    }

}