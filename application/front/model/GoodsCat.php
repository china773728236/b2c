<?php
namespace app\front\model;
use think\model;
use think\Db;

class GoodsCat extends Model
{
//商品面包屑导航
	public function goodsCrumbs($cat_id)
	{
		$data = $this->filed('id,name,pid')->select();
return $this->Infinitus($data,$cat_id);
	}


	//面包屑无限极分类
	private function  Infinitus($data,$cat_id)
	{
         static $arr = array();
         //获取到当前页面的商品分类id
         
	}
}