<?php
namespace Admin\Model;
use Think\Model\RelationModel;
class TypeModel extends RelationModel{

	protected $_link = array(
	    'sections'  =>  array(
	    	'mapping_type' =>self::HAS_MANY,
	        'class_name' => 'Sections',
	        'foreign_key'=>'type_id',
	        'mapping_fields'=>'id,sections',
	    )
	);

	/**
	 * [getUpCount 获取上线类型数量]
	 * @return [Integer] [返回已上线个数]
	 */
	public function getUpCount(){
		return $this->where("state = 1")->count();
	}
	/**
	 * [getTypeById 通过ID查找TYPE]
	 * @param  [Integer] $id [传入的ID]
	 * @return [array]     [返回查询到的数组]
	 */
	public function getTypeById($id){
		return $this-> find($id);
	}
	/**
	 * [getOnType 获取类型]
	 * @return [List] [返回查询到的列表]
	 */
	public function getType(){
		$List = $this ->select();
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