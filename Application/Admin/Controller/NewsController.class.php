<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Exception;

class NewsController extends Controller{
	public function _initialize(){
        if (!session('?Adminlogin')) {
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
		$TypeList = D('Type')->getType();
		$this->assign('title','添加新闻');
		$this->assign('TypeList',$TypeList);
		$this->display();
	}

	//修改新闻view
	public function change(){
		$id = I('get.id');
		$NewsModel = M('News');
		$p = I('get.p',1);
		$SectionsModel = M('Sections');
		$TypeList = D('Type')->getType();
		$detail = $NewsModel->where(array('id'=>$id,'delete_tag'=>false))->find();
		$SectionList = $SectionsModel->where("type_id =".$detail['type'])->select();
		$keywordBelongModel = D('NewsKeywordBelong');
		$keywordList = $keywordBelongModel->relation('keyword')->where(array('news_id'=>$id))->select();

		$this->assign('keywordList',$keywordList);
		$this->assign('SectionList',$SectionList);
		$this->assign('TypeList',$TypeList);
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
		}
		$Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		$this->assign('page',$show);
		$this->assign('List',$List);
		$this->assign('key',$key);
		$this->display();

	}
	//新闻审核界面view
	public function check(){
        $newsModel = D('News');
        $page = I('get.p',1);
        $count = 10;
        $allCount = $newsModel->where("state = 1 and delete_tag=0")->count();
        $List = $newsModel->where("state = 1 and delete_tag=0")
            ->relation(true)
            ->order('publish_time desc')
            ->field('id,title,image,image_thumb,type,publish_time,contributor,sections')
            ->page($page,$count)
            ->select();
        $Page       = new \Think\Page($allCount,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        $this->assign('page',$show);
        $this->assign('List',$List);
        $this->display();
    }

    public function checkAction(){
        $operate = I('get.operate','pass');
        $id = I('get.id',0);
        $newsModel = M('News');
        if( $operate == 'pass') {
            $result = $newsModel->where(array('id'=>$id))->save(array('state'=>0));
        } else {
            $result = $newsModel->where(array('id'=>$id))->save(array('state'=>2));
        }
        $message = $operate == 'pass'?'审核通过成功':'拒绝通过成功';
        if($result!== false){
            $this->success($message);
        } else{
            $this->error('服务器错误');
        }
    }

	//上下线切换
	public function uplinetoggle(){
		$id = I('get.id');
		$NewsModel = D('News');
		$UpCount = $NewsModel->where("state = 1 and delete_tag=0")->count();
		$state = $NewsModel->where(array('id'=>$id,'delete_tag'=>false))->getField('state');
		if($state =='0'){
			if($UpCount>=5)
				$this->error('上线类型数量已达到最大数量5');
			else{
				$data['state'] = '1';
			}
		}else{
			$data['state'] = '0';
		}
		$result = $NewsModel->where(array('id'=>$id,'delete_tag'=>false))->save($data);
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
			$Model = M('');
			$Model->startTrans();
			if($_FILES['file']['name']!=null){
				if($NewsModel->upload()=='上传失败'){
					$this->error('上传失败');
				}
			}

			$NewsModel->publish_time = date('Y-m-d H:i:s',time());
			$NewsModel->state = '0';
			$NewsModel->contributor = session('Adminlogin');

			$newResult = $NewsModel->add();
			$newsId = $NewsModel->getLastInsID();

			//添加关键字
			$KeywordModel = M('NewsKeyword');
			$KeywordStr = $_POST['keyword'];
			$KeywordArr = json_decode($KeywordStr,TRUE);

			$sign = true;
			foreach ($KeywordArr as &$item) {
				if( $item['id'] == 0) {
					if ( $keyword = $KeywordModel->where(array('keyword'=>$item['keyword']))->find() ){
						$item['id'] = $keyword['id'];
					}else{
						$keywordResult = $KeywordModel->add(array('keyword'=>$item['keyword']));
						if( $keywordResult !== false ){
							$item['id'] = $KeywordModel->getLastInsID();
						}else{
							$sign = false;
						}
					}
				}
			}

			$keywordBelongSign = true;
			if($sign){
				$keywordBelongModel = M('NewsKeywordBelong');
				foreach ($KeywordArr as $item) {
					$keywordBelongResult = $keywordBelongModel->add(array('keyword_id'=>$item['id'],'news_id' => $newsId));
					if( $keywordBelongResult === false ){
						$keywordBelongSign = false;
					}
				}
			}
			if( $keywordBelongSign && $sign && $newResult!==false) {
				$Model->commit();
				$this->success('添加成功');
			}else{
				$Model->rollback();
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
			$Model = M('');
			$Model->startTrans();
			if($_FILES['file']['name']!=null){
				if($NewsModel->upload()=='上传失败'){
					$this->error('上传失败');
				}else{
					$detail = M('News')->find($id);
					unlink('./Data/news/'.$detail['image']);
					unlink('./Data/news_thumb/'.$detail['image_thumb']);
				}
			}
			$newResult = $NewsModel->where(array('id'=>$id,'delete_tag'=>false))->save();


			//添加关键字
			$KeywordModel = M('NewsKeyword');
			$KeywordStr = $_POST['keyword'];
			$KeywordArr = json_decode($KeywordStr,TRUE);

			$sign = true;
			foreach ($KeywordArr as &$item) {
				if( $item['id'] == 0) {
					if ( $keyword = $KeywordModel->where(array('keyword'=>$item['keyword']))->find() ){
						$item['id'] = $keyword['id'];
					}else{
						$keywordResult = $KeywordModel->add(array('keyword'=>$item['keyword']));
						if( $keywordResult !== false ){
							$item['id'] = $KeywordModel->getLastInsID();
						}else{
							$sign = false;
						}
					}
				}
			}

			$keywordBelongSign = true;
			if($sign){
				$keywordBelongModel = M('NewsKeywordBelong');
				$deleteResult = $keywordBelongModel ->where(array('news_id'=>$id))->delete();
				if($deleteResult !== false){
					foreach ($KeywordArr as $item) {
						$keywordBelongResult = $keywordBelongModel->add(array('keyword_id'=>$item['id'],'news_id' => $id));
						if( $keywordBelongResult === false ){
							$keywordBelongSign = false;
						}
					}
				}else{
					$keywordBelongSign = false;
				}
			}

			if( $keywordBelongSign && $sign && $newResult!==false) {
				$Model->commit();
				$this->redirect('News/index',array('p'=>$p));
			}else{
				$Model->rollback();
				$this->error('修改失败');
			}



		}
	}

	//文章pv
	public function pv(){
		$id = I('get.id');
		$title = M('News')->where(array('id'=>$id,'delete_tag'=>false))->getField('title');
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


	public function getCalculateSimilarityNews(){
	    try{
            $date = I('get.date',date('Y-m-d',time()));
            $newsModel = M('News');
            $result = $newsModel->where(array('DATE_FORMAT(publish_time,\'%Y-%m-%d\')'=>$date,'delete_tag'=>false))->field('id,title,publish_time time')->order('publish_time desc')->select();
            $json = array(
                'success' => true,
                'code' => 200,
                'message' => '获取成功',
                'attr'=>$result
            );
        }catch (Exception $e){
            $json = array(
                'success' => false,
                'code' => 500,
                'message' => '服务器错误'
            );
        }
        $this->ajaxReturn($json);
	}
}
