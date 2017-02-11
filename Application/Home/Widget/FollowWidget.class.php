<?php
namespace Home\Widget;
use Think\Controller;
class FollowWidget extends Controller{
    public function follow($follow_id,$UserView=false){
    	if(session('?login')){
    		$user_id = session('login');
    		$FollowModel = D('Follow');
    		$list = $FollowModel->checkFollow($user_id,$follow_id);
            if(count($list)==0||$list[0]['delete_tag']=='1')
                $bool = false;
            else
                $bool = true;
    	}else{
    		$bool = false;
    	}
        $this->assign('follow_id',$follow_id);
        $this->assign('bool',$bool);
        if($UserView){//如果在个人信息视图
            $this->display('Widget:followUserView');
        }
        else
            $this->display('Widget:follow');
    }
}