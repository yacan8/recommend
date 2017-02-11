<?php
namespace Home\Controller;
use Think\Controller;
class NewsController extends Controller {
	public function detail(){
		if(isset($_GET['app_nav'])){
			session('app_nav','no');
		}
		$Crawler = I('get.crawler','');//爬虫
		$NewsModel = D('News');
		$id = I('get.id');
		//浏览+1
		$NewsModel->where(array('id'=>$id)) ->setInc('browse',1);
		$List = $NewsModel->getById($id);
		if($List==null)
			$this->error("该文章不存在");
		$MessageCount = D('Message')->getMessageCount($id);
		$PreAndNextTitle = $NewsModel->getTitlePreAndNext($id);//获取上一篇和下一篇标题
		$this->assign('PreAndNextTitle',$PreAndNextTitle);
		$this->assign('Crawler',$Crawler);
		$this->assign('id',$id);
		$this->assign('MessageCount',$MessageCount);
		$this->assign('List',$List);
		if($Crawler=='')
			$this->display('detail');
		else
			$this->display('crawler');
	}


	public function search(){
		$key = I('get.key');
		$page = I('get.page',1);
		$NewsModel = D('News');
		$List = $NewsModel->search($key,$page);
		$this ->assign('List',$List);
		$this ->assign('key',addslashes($key));
		$this->display();
	}


	public function search_load(){
		$key = I('get.key');
		$page = I('get.page',1);
		$NewsModel = D('News');
		$List = $NewsModel->search($key,$page);
		echo json_encode($List);
		// echo $key."+".$page;
	}
	/**
	 * [load ajax加载新闻]
	 * @return [json] [查询到的内容]
	 */
	public function load(){
		$Model = D('News');
		$type = I('get.type');
		$page = I('get.page');
		$sections = I('get.sections');
		$json = $Model->getSelectType($type,$page,ture,$sections);
		echo json_encode($json);
	}
	/**
	 * [comment_load ajax加载评价内容]
	 * @return [json] [查询到的内容]
	 */
	public function comment_load(){
		$Model = D('Message');
		$id = I('get.id');
		$page = I('get.page');
		$json = $Model ->loading($id,$page);
		echo json_encode($json);
	}

	/**
	 * [comment 提交评价]
	 * @return [string] [成功返回1,没有登录返回2，失败返回失败原因]
	 */
	public function comment(){
		if(!isset($_SESSION['login'])){
			echo '2';
		}else{
			$receiver = I('post.receiver');
			if($receiver == $_SESSION['login']){
				echo "3";
			}else{
				$MessageModel = D('Message');
				$id = I('post.other_id');
				$content = I('post.content');
				$data['other_id'] = $id;
				$data['receiver'] = $receiver;
				$data['content'] = $content;
				$data['sender'] = session('login');
				$data['time'] = date('y-m-d H:i:s',time());
				$data['state'] = '1';
				$result = $MessageModel->addMessage($data);
				if($result!=0)
					echo "1";
				else
					echo "评论失败";
			}
		}
	}
	/**
	 * [attention 收藏]
	 * @return [type] [未登录返回2，成功返回1，否则返回收藏失败]
	 */
	public function attention(){
		if(!isset($_SESSION['login'])){
			echo '2';
		}else{
			$CollectionModel = D('Collection');
			$data['collected'] = I('post.collected');
			$data['collecting'] = session('login');
			if($CollectionModel->CheckIsCollected($data['collected'],$data['collecting'])){
				echo '3';
			}else{
				$result = $CollectionModel->add($data);
				if($result!=0)
					echo "1";
				else
					echo "收藏失败";
			}

		}
	}
}