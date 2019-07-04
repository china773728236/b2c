<?php
namespace app\front\controller;
use think\Controller;
use think\Request;
use think\Db;

class Common extends Controller
{
    //面包屑导航
    public function Crumbs($pid=0)
    {
        $data = db('cat')->select();
        foreach ($data as $k => $v)
        {
            
        }
    }
}
