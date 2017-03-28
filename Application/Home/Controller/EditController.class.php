<?php

namespace Home\Controller;
use Think\Controller;

class EditController extends Controller{



	public function province_change(){
		$province = I('post.word');
		$provinceId = M('AddressProvinces')->where(array('province'=>$province))->getField('provinceid');
		$List = M('AddressCities')->where(array('provinceid'=>$provinceId))->field('city item')->select();
		$this->ajaxReturn($List);
	}


	public function city_change(){
		$city = I('post.word');
		$cityId = M('AddressCities')->where(array('city'=>$city))->getField('cityid');
		$List = M('AddressAreas')->where(array('cityid'=>$cityId))->field('area item')->select();
		$this->ajaxReturn($List);
	}


	public function school_province_change(){
		$province = I('post.word');
		$provinceId = M('ProvinceInfo')->where(array('pr_province'=>$province))->getField('pr_id');
		$List = M('CityInfo')->where(array('ci_province'=>$provinceId))->field('ci_city item')->select();
		$this->ajaxReturn($List);
	}


	public function school_city_change(){
		$city = I('post.word');
		$cityId = M('CityInfo')->where(array('ci_city'=>$city))->getField('ci_id');
		$List = M('SchoolInfo')->where(array('sh_city'=>$cityId))->field('sh_shool item')->select();
		$this->ajaxReturn($List);
	}
}