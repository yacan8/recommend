<?php
namespace Admin\Controller;
use Think\Controller;
class MessageController extends Controller{
	public function _initialize(){
        if (!isset($_SESSION['Adminlogin'])) {
            $this->redirect('Login/index');
        }
 	}
	public function index(){
		$MessageModel = D('Message');
		$TypeModel = M('Type');
		$count      = $MessageModel->count();
		$Page       = new \Think\Page($count,10);
		$show       = $Page->show();
		$List = $MessageModel->relation(true)->limit($Page->firstRow.','.$Page->listRows)->order("time desc")->select();
		for ($i=0; $i < count($List); $i++) {
			if($List[$i]['News']['type']!='')
				$List[$i]['News']['type'] = $TypeModel->where("id=".$List[$i]['News']['type'])->getField('type');
			if($List[$i]['senderinfo']['icon'] == '')
				$List[$i]['senderinfo']['icon'] = 'default.jpg';
			if($List[$i]['receiverinfo']!='')
				if($List[$i]['receiverinfo']['icon'] == '')
					$List[$i]['receiverinfo']['icon'] = 'default.jpg';
		}
		// dump($List);
		$this->assign('title','评论管理');
		$this->assign('page',$show);
		$this->assign('List',$List);
		$this->display();
	}

	//删除评论
	public function delete(){
		$id = I('get.id');
		$result = M('Message')->where("id = $id")->delete();
		if($result!=0)
			$this->success('删除成功');
		else
			$this->error('删除失败');
	}
}