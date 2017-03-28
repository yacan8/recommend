<?php
namespace Home\Model;
use Think\Model;
class TypeModel extends Model{



	/**
	 * [getTypeById 通过ID查找TYPE]
	 * @param  [Integer] $id [传入的ID]
	 * @return [array]     [返回查询到的数组]
	 */
	public function getTypeById($id){
		return $this-> find($id);
	}
	/**
	 * [getOnType 获取已上线类型]
	 * @return [List] [返回查询到的列表]
	 */
	public function getOnType(){
		$List = $this -> where('state=1')->select();
		for ($i=0 ; $i < count($List); $i++) { 
			if($List[$i]['type']=='其他'){
				if($i!=count($List)-1){
					$type = $List[$i];
					$List[$i] = $List[count($List)-1];
					$List[count($List)-1] = $type; 
				}
			}
		}
		return $List;
	}
}