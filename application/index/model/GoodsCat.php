<?php
namespace app\index\model;
use think\Model;
use think\Db;
//后台控制器php文件
/* 
 *
 *  */
class GoodsCat extends  Model //继承一下 因为它是子类
{

    protected static function init()
    {

     /*  Article::beforeInsert(
                      function ($data){
echo 'oksss';die;*/
        GoodsCat::beforeInsert(

            function ($data){
           //如果拿到了系统默认存放图片的路径
           if($_FILES['img']['tmp_name'])
           {

               // 获取表单上传文件 例如上传了001.jpg
               $file = request()->file('img');
               // 移动到框架应用根目录/public/uploads/ 目录下
               $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
               //如果成功移动，获取绝对路径并将路径赋予到数据表中

               if ($info) {
                   $file_path = '/b2c/' . 'public' . DS . 'uploads' . '/' . $info->getSaveName();
                   // dump($file_path);die;
                   $data['img'] = $file_path;

                  

               }
           }
                  });


        GoodsCat::beforeUpdate(
            function ($data){

                //如果拿到了系统默认存放图片的路径
               if($_FILES['img']['tmp_name']) {

                   //更改前先删除原来的图片
                   $img = GoodsCat::find($data->id);
                  //dump($img);die;
                   $img_path = $_SERVER['DOCUMENT_ROOT'].$img['img'];
                   //如果找到了图片路径将图片删除
                   if(file_exists($img_path))
                   {
                       //如果之前有图片才删除原来的图片
                       if($img['img'] != "") {
                           unlink($img_path);
                       }
                       // 获取表单上传文件 例如上传了001.jpg
                       $file = request()->file('img');
                       //dump($file);die;
                       // 移动到框架应用根目录/public/uploads/ 目录下
                       $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                       //如果成功移动，获取绝对路径并将路径赋予到数据表中
					  // dump($info);die;
                       if ($info) {
                           $file_path = '/b2c/' . 'public' . DS . 'uploads' . '/' . $info->getSaveName();
                           //dump($file_path);die;
                           $data['img'] = $file_path;
                   }


                   }
                }
            });



        GoodsCat::beforeDelete(
          function ($data)
          {

              $res =  GoodsCat::find($data->id);
              $img_path = $_SERVER['DOCUMENT_ROOT'].$res['img'];
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
                  return '删除失败';die;
              }
          }
        );
}






    
}
?>