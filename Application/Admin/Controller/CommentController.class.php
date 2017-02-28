<?php
namespace Admin\Controller;
use Think\Controller;

/**
 *
 */
class CommentController extends Controller{


    public function _initialize(){
        if (!isset($_SESSION['Adminlogin'])) {
            $this->redirect('Login/index');
        }
    }


    public function comment(){
        $news_id = I('get.news_id',0);
        $user_id = I('get.user_id',0);
        $p = I('get.p',1);
        $commentModel = D('Comment');
        $count = $show_count =  20;
        $result = $commentModel->getList($news_id,$user_id,$p,$count);
        $Page       = new \Think\Page($count,$show_count);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
        if($user_id!=0){
            $nickname = M('Login')->where(array('id'=>$user_id))->getField("nickname");
            $this->assign('nickname',$nickname);
        }
        if($news_id!=0){
            $news_title = M('News')->where(array('id'=>$news_id))->getField('title');
            $this->assign('news_title',$news_title);
        }
        // var_dump($result);
        $this->assign('commentList',$result);
        $this->assign('all_count',$count);
        $this->assign('page',$show);
        $this->assign('user_id',$user_id);
        $this->assign('news_id',$news_id);
        $this->display('index');
    }

    //评论删除active
	public function delete(){
		$id = I('get.id');
		if($id!=0){
			$CommentModel = M('Comment');
			$data['delete_tag'] = (bool)1;
			$result = $CommentModel->where(array('id'=>$id))->save($data);
			if($result !== false){
				$this->success('操作成功');
			}else{
				$this->error('操作失败');
			}
		}else{
			$this->error('参数错误');
		}
	}


}
