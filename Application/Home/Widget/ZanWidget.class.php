<?php
namespace Home\Widget;
use Think\Controller;
class ZanWidget extends Controller{
    public function zan($comment_id,$zan_count){
    	if(session('?login')){
    		$user_id = session('login');
    		$zanModel = M('Zan');
    		$count = $zanModel -> where(array('comment_id' => $comment_id,'user_id'=>$user_id))->count();
            if($count == 0)
                $bool = false;
            else
                $bool = true;
    	}else{
    		$bool = false;
    	}
        $this->assign('bool',$bool);
        $this->assign('comment_id',$comment_id);
        $this->assign('zan_count',$zan_count);
        $this->display('News:zanWidget');
    }
}
