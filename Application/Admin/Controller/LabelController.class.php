<?php
namespace Admin\Controller;
use Think\Controller;
class LabelController extends Controller{
	public function _initialize(){
        if (!isset($_SESSION['Adminlogin'])) {
            $this->redirect('Login/index');
        }
 	}

	public function add(){
		$label = I('get.label');
		if(strlen($label)>12){
			$json['Code'] = '0';
			$json['Message'] = '标签长度必须小于4';
		}else{
			$LableModel = D('Label');
			$count = $LableModel->where("label='$label'")->count();
			if($count==0){
				$result = $LableModel->addLable($label);
				if($result!=0){
					$json['Code'] = '1';
					$json['LabelId'] = $result;
					$json['Label'] = $label;
					$json['Message'] = '添加成功';
				}else{
					$json['Code'] = '2';
					$json['Message'] = '标签添加失败';
				}
			}else{
				$label_id = $LableModel->where("label='$label'")->getField('id');
				$json['Code'] = '3';
				$json['LabelId'] = intval($label_id);
				$json['Label'] = $label;
				$json['Message'] = '标签已存在';
			}
		}
		echo json_encode($json);
	}
}