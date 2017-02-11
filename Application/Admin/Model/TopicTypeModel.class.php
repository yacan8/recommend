<?php
namespace Admin\Model;
use Think\Model;
class TopicTpyeModel extends Model{

	/**
	 * [getTypeNameById 通过id获取类型名]
	 * @param  [Type] $id [传入的类型ID]
	 * @return [String]     [类型名]
	 */
	public function TypeNameById($id){
		return $this->where(array('id'=>$id))->getField('type');
	}
}
	
