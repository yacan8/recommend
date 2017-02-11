<?php

namespace Admin\Controller;
use Think\Controller;
class TopicTypeController extends Controller{
	
	/**
	 * [change 修改话题类型action ajax请求]
	 * @param [Integer] $id [post参数 要修改的话题类型的id]
	 * @param [string] $type [post参数 修改类型]
	 */
	public function change(){
		if(IS_POST){
			$id = I('post.id');
			$type = I('post.type');
			$result = M('TopicType')->where(array('id'=>$id))->save(array('type'=>$type));
			if($result!==false){
				$json['Code'] = '200';
				$json['Message'] = '修改成功';
			}else{
				$json['Code'] = '201';
				$json['Message'] = '修改失败';
			}
			$this->ajaxReturn($json);
		}
	}

	/**
	 * [add 添加话题类型action]
	 * @param [string] $type [post参数 添加的类型名]
	 */
	public function add(){
		if(IS_POST){
			$type = I('post.type');
			$TopicTypeModel = M('TopicType');
			$count = $TopicTypeModel->where(array('type'=>trim($type)))->count();
			if($count==0){
				$data['type'] = trim($type);
				$data['description'] = trim($type);
				$data['state'] = (bool)1;
				$result = $TopicTypeModel->add($data);
				if($result!==false)
					$this->success('添加成功');
				else
					$this->error('添加失败');
			}else{
				$this->error('该话题类型已存在');
			}
		}
	}
}