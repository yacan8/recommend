<?php
namespace Admin\Model;
use Think\Model;
class SectionsModel extends Model{


	/**
	 * [addSections 添加二级分类]
	 * @param [int] $type_id [归属的二级分类ID]
	 * @param [string] $sections [二级分类名]
	 * @return [bool] [添加成功与否]
	 */
	public function addSections($type_id,$sections){
		$data['type_id'] = $type_id;
		$data['sections'] = $sections;
		$result = $this->add($data);
		if($result!=0)
			return true;
		return false;
	}
}