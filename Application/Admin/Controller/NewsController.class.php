<?php
namespace Admin\Controller;
use Think\Controller;
class NewsController extends Controller{
	public function _initialize(){
        if (!isset($_SESSION['Adminlogin'])) {
            $this->redirect('Login/index');
        }
 	}
	//新闻管理view
	public function index(){
		$NewsModel = D('News');
		$count = 0;
		$p = I('get.p',1);
		$UpList = $NewsModel->getList(0,true,$p,$count);
		$type = I('get.type',0);
		$List = $NewsModel->getList($type,false,$p,$count);
		// dump($List);
		$Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		$TypeList = D('Type')->getType();
		$this->assign('TypeList',$TypeList);
		$this->assign('type',$type);
		$this->assign('page',$show);
		$this->assign('UpList',$UpList);
		$this->assign('List',$List);
		$this->assign('p',$p);
		$this->assign('title','新闻管理');
		$this->display();
	}
	//分类管理view
	public function type(){
		$List = D('Type')->getType();
		$this->assign('List',$List);
		$this->assign('title','分类管理');
		$this->display();
	}
	//添加新闻view
	public function add(){
		$LabelModel = D('Label');
		$List = $LabelModel->getAll();
		$TypeList = D('Type')->getType();
		$SectionsModel = M('Sections');
		$SectionList = $SectionsModel->select();
		$this->assign('title','添加新闻');
		$this->assign('List',$List);
		$this->assign('TypeList',$TypeList);
		$this->display();
	}

	//修改新闻view
	public function change(){
		$id = I('get.id');
		$NewsModel = M('News');
		$p = I('get.p',1);
		$SectionsModel = M('Sections');
		$NewsLabelModel = D('NewsLabel');
		$TypeList = D('Type')->getType();
		$LabelList =D('Label')->getAll();
		$detail = $NewsModel->where("id=$id")->find();
		$NewsLabelList = $NewsLabelModel->getLabelByNewsId($id);
		$SectionList = $SectionsModel->where("type_id =".$detail['type'])->select();
		$this->assign('SectionList',$SectionList);
		$this->assign('TypeList',$TypeList);
		$this->assign('NewsLabelList',$NewsLabelList);
		$this->assign('LabelList',$LabelList);
		$this->assign('p',$p);
		$this->assign('detail',$detail);
		$this->display();
	}
	//新闻搜索view
	public function search(){
		$key = I('get.key');
		$count = 0;
		$p = I('get.p',1);
		$NewsModel = D('News');
		$List = $NewsModel->search($key,$p,$count);
		for ($i=0; $i < count($List); $i++) { 
			$List[$i]['title'] = str_replace($key, "<font color='red'>".$key."</font>", $List[$i]['title'] );
			$List[$i]['intro'] = str_replace($key, "<font color='red'>".$key."</font>", $List[$i]['intro'] );
		}
		$Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		$this->assign('page',$show);
		$this->assign('List',$List);
		$this->assign('key',$key);
		$this->display();
	}	

	//上下线切换
	public function uplinetoggle(){
		$id = I('get.id');
		$NewsModel = D('News');
		$UpCount = $NewsModel->where("state = 1")->count();
		$state = $NewsModel->where("id = $id")->getField('state');
		if($state =='0'){
			if($UpCount>=5)
				$this->error('上线类型数量已达到最大数量5');
			else{
				$data['state'] = '1';
			}
		}else{
			$data['state'] = '0';
		}
		$result = $NewsModel->where("id= $id")->save($data);
		if($result!=0)
			$this->redirect('News/index');
		else
			$this->error('修改失败');
	}

	//删除新闻
	public function delete(){
		$id = I('get.id');
		$NewsModel = D('News');
		$result = $NewsModel->deleteById($id);
		if($result) $this->redirect('News/index');
		else $this->error('删除成功');
	}
	//查看
	public function view(){
		$id = I('get.id');
		$this->redirect('Home/News/detail',array('id'=>$id));
	}

	//添加新闻
	public function addNews(){
		$NewsModel = D("News");
		$result = $NewsModel->create();

		if(!$result){
			$this->error($NewsModel->getError());
		}else{
			if($_FILES['file']['name']!=null){
				if($NewsModel->upload()=='上传失败'){
					$this->error('上传失败');
				}
			}
			$NewsModel->publish_time = date('y-m-d H:i:s',time());
			$NewsModel->state = '0';
			$user = session('Adminlogin');
			$NewsModel->contributor = M('Login')->where("tel = '$user'")->getField('id');
			$nresult = $NewsModel->add();
			if($nresult!=0){
				$NewsLabelModel = D("NewsLabel");
				$id = $NewsModel->getLastInsID();
				$Label_str = I('post.label');
				$LabelArray = explode(',',$Label_str);
				for ($i=0; $i < count($LabelArray); $i++) { 
					$NewsLabelModel->addLabel($id,$LabelArray[$i]);
				}
				$this->success('添加成功');
			}else{
				$this->error('添加失败');
			}
			
		}
	}

	//新闻修改
	public function changeNews(){
		$id = I('get.id');
		$p = I('get.p',1);
		$NewsModel = D("News");
		$result = $NewsModel->create();
		if(!$result){
			$this->error($NewsModel->getError());
		}else{
			if($_FILES['file']['name']!=null){
				if($NewsModel->upload()=='上传失败'){
					$this->error('上传失败');
				}else{
					$detail = M('News')->find($id);
					unlink('./Data/news/'.$detail['image']);
					unlink('./Data/news_thumb/'.$detail['image_thumb']);
				}
			}
			$NewsModel->where("id = $id")->save();
			$NewsLabelModel = D("NewsLabel");
			$NewsLabelModel ->where("news_id = $id")->delete();
			$Label_str = I('post.label');
			$LabelArray = explode(',',$Label_str);
			for ($i=0; $i < count($LabelArray); $i++) { 
				$NewsLabelModel->addLabel($id,$LabelArray[$i]);
			}
			$this->redirect('News/index',array('p'=>$p));
		}
	}

	//文章pv
	public function pv(){
		$id = I('get.id');
		$title = M('News')->where(array('id'=>$id))->getField('title');
		$this->assign('title',$title);
		$this->assign('id',$id);
		$this->display('Index/index');
	}
	//二级分类管理view
	public function sections(){
		$TypeModel = D('Type');
		$List = $TypeModel->relation('sections')->field('id,type')->select();
		$this ->assign('List',$List);
		$this->display();
	}
}