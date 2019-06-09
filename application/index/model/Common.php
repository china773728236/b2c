<?php
namespace app\index\model;
use think\Model;
use think\Db;
use think\Image;
//后台公用类
/* 
 *
 *  */
class Common extends  Model //继承一下 因为它是子类
{

//添加图片公用方法
public static  function imgInsert($data,$img)
{

    //如果拿到了系统默认存放图片的路径
    if($_FILES[$img]['tmp_name'])
    {

        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file($img);

        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
        //如果成功移动，获取绝对路径并将路径赋予到数据表中

        if ($info) {
            $file_path = '/b2c/' . 'public' . DS . 'uploads' . '/' . $info->getSaveName();
           // dump($file_path);die;
            $data[$img] = $file_path;
       return $data[$img];


        }
    }
}



//批量增加相册
public static function imgMoreInsert($data,$add_img,$db)
{
    //如果拿到了系统默认存放图片的路径
    if($_FILES[$add_img]['tmp_name'])
    {

        $img_res = request()->file($add_img);

        if($img_res)
        {

            foreach ($img_res as $file)
            {

                // 移动到框架应用根目录/public/uploads/ 目录下
                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                //如果成功移动，获取绝对路径并将路径赋予到数据表中

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
                    /* $img_path = IMG.$img;
                     //释放info变量
                     if(file_exists($img_path))
                     {
                         //释放变量
                         unset($info);
                         //删除主图
                         unlink($img_path);
                     }*/
                    $pho_res = db($db)->insert(['goods_id'=>$data->goods_id,'hot_img'=>$img,'big_img'=>$big_img,'md_img'=>$md_img,'sm_img'=>$sm_img]);
                    if(!$pho_res)
                    {
                        echo '上传图片出问题了';die;
                    }

                }else
                {

                    echo $file->getError();
                }

            }
            // $arr['goods_id'] = $data->goods_id;







        }
    }
}


//用户输入错误或成功提示方法
public static function hint($msc,$url)
{
    echo "
<html>
<head>
<meta charset='utf-8'>

</head>
<body>
<p id='show'>{$msc}</p>
<a href='{$url}' >点我立即跳转</a>
</body>
<script src='https://www.imooc.com/static/lib/jquery/1.9.1/jquery.js'></script>
<script>
$(function() {
    
    var t=3;//设定跳转的时间
setInterval(function()
{
    if(t==0){
location='{$url}'; //#设定跳转的链接地址
}
document.getElementById('show').innerHTML=''+t+'秒后跳转到原网页'; // 显示倒计时
t--; // 计数器递减
},1000); //启动1秒定时
 });
</script>
</html>
";
    die;
}



}
?>