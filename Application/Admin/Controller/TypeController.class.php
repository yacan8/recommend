<?php
namespace Admin\Controller;
use Think\Controller;
class TypeController extends Controller{
	public function _initialize(){
        if (!isset($_SESSION['Adminlogin'])) {
            $this->redirect('Login/index');
        }
 	}
	//修改颜色
	public function changetype(){
		$id = I('get.id');
		$data['color'] = I('get.color');
		$data['type'] = I('get.type');
		$TypeModel = M('Type');
		$result = $TypeModel->where(array("id"=>$id))->save($data);
		if($result!=0) echo "1";
		else echo "2";
	}

	//添加分类
	public function add(){
		$type = I('get.type');
		$color = I('get.color');
		if(strlen($type)>8){
			$this->error('类型长度最大为4');
		}
		$data['type'] = $type;
		$data['color'] = $color;
		$data['state'] = '0';
		$result = M('Type')->add($data);
		if($result!=0)
			$this->redirect('News/type');
		else $this->error('添加失败');
	}

	//上下线切换
	public function uplinetoggle(){
		$id = I('get.id');
		$TypeModel = D('Type');
		$UpCount = $TypeModel->getUpCount();
		$state = $TypeModel->where("id = $id")->getField('state');
		if($state =='0'){
			if($UpCount>=11)
				$this->error('上线类型数量已达到最大数量11');
			else{
				$data['state'] = '1';
			}
		}else{
			$data['state'] = '0';
		}
		$result = $TypeModel->where("id= $id")->save($data);
		if($result!=0)
			$this->redirect('News/type');
		else
			$this->error('修改失败');
	}


	//删除分类
	public function delete(){
		$id = I('get.id');
		$NewsModel = D('News');
		$TypeModel = D('Type');
		$TypeArray = $TypeModel ->getTypeById($id);
		$TypeOther = $TypeModel ->where("type='其他'")->getField('id');
		if($TypeArray['type']=='其他'){
			$this->error('无法删除该分类');
		}else{
			$result = $NewsModel ->setType($id,$TypeOther);
			if($result){
				$TypeModel->where("id=$id")->delete();
				$this->success('删除成功');
			}else{
				$this->error('删除失败');
			}
		}
	}
}