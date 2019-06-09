<?php
//这一语句很重要，告诉浏览器返回的数据格式是json格式
header("Content-Type: text/xml;charset=utf8");
//告诉浏览器不要缓存
header("Cache-Control: no-cache");
$cat_id = $_POST['cat_id'];
echo '接收到';
//如何在调试过程中看到接收到的数据呢?
//可以把信息存到一个文件夹里去(php文件技术)
file_put_contents("D:/environment/PHPTutorial/WWW/b2c/application/index/controller/mylog.log", $province."\r\n",FILE_APPEND);