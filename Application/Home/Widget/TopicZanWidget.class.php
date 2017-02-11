<?php
namespace Home\Widget;
use Think\Controller;
class TopicZanWidget extends Controller{
    public function TopicZan($type,$id,$zan_count){
    	if(session('?login')){
            $TopicZanModel = D('TopicZan');
    		$list = $TopicZanModel->checkByZanIdAndUserIdAndType($id,session('login'),$type);
            if(count($list)==0||$list[0]['delete_tag'] =='1')
                $count = 0;
            else
                $count = 1;
    	}else{
    		$count = 0;
    	}
        $this->assign('id',$id);
        $this->assign('type',$type);
        $this->assign('zan_count',$zan_count);
        $this->assign('count',$count);
        $this->display('Widget:TopicZan');
    }
}