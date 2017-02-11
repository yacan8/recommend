<?php
namespace Home\Model;
use Think\Model;
class UserModel extends Model{



	public function getById($id){
		$array = $this->where(array('id'=>$id))->find();
		return $array;
	}


}