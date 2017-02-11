<?php
namespace Admin\Controller;
use Think\Controller;
class SectionsController extends Controller{
	public function _initialize(){
        if (!isset($_SESSION['Adminlogin'])) {
            $this->redirect('Login/index');
        }
 	}
	public function AjaxChangeType(){
		$type_id = I('get.type_id');
		$list = M('Sections')->where("type_id = $type_id")->select();
		echo json_encode($list);
	}

	/**
	 * [AjaxAddSections ajax添加二级分类]
	 * @param [int] [type_id] [get参数 归属分类的ID]
	 * @param [string] [sections] [get参数 二级分类名]
	 */
	public function AjaxAddSections(){
		$type_id = I('get.type_id');
		$sections = I('get.sections');
		$SectionModel = D('Sections');
		$result = $SectionModel->addSections($type_id,$sections);
		if($result){
			$json['Code'] = '1';
			$json['Message'] = '二级添加成功';
			$json['id'] = $SectionModel->getLastInsID();
			$json['sections'] = $sections;
		}else{
			$json['Code'] = '2';
			$json['Message'] = '二级添加失败';
		}
		echo json_encode($json);
	}

	/**
	 * [Change 二级分类修改]
	 * @param [Integer] [id] [get参数 需要修改的二级分类ID]
	 * @param [String] [sections] [get参数 修改后的二级分类名]
	 */
	public function Change(){
		$id = I('get.id');
		$data['sections'] = I('get.sections');
		$SectionModel = M('Sections');
		$result = $SectionModel->where(array('id'=>$id))->save($data);
		if($result == 0){
			$this->error('修改失败');
		}else{
			$this->success('修改成功');
		}
	}
	/**
	 * [Delect 二级分类删除，改二级分类的新闻的分类与二级分类划分到其他]
	 * @param [Integer] [id] [get参数 需要删除的二级分类ID]
	 */
	public function Delete(){
		$id = I('get.id');
		$SectionModel = M('Sections');
		$result = $SectionModel->where(array('id'=>$id))->delete();
		if($result == 0){
			$this->error('修改失败');
		}else{
			$data['type'] = M('Type')->where(array('type'=>'其他'))->getField('id');
			$data['sections'] = $SectionModel->where(array('sections'=>'其他'))->getField('id');
			M('News')->where(array('sections'=>$id))->save($data);
			$this->success('修改成功');
		}
	}
}