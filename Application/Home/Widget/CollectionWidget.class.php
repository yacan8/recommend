<?php
namespace Home\Widget;
use Think\Controller;
class CollectionWidget extends Controller{
    public function collection($collected,$type){
    	if(session('?login')){
    		$user_id = session('login');
    		$CollectionModel = D('Collection');
    		$list = $CollectionModel->CheckIsCollected($collected,$user_id,$type);
            if(count($list)==0||$list[0]['delete_tag']=='1')
                $bool = false;
            else
                $bool = true;
    	}else{
    		$bool = false;
    	}
    	$this->assign('type',$type);
        $this->assign('bool',$bool);
        $this->display('Widget:collection');
    }
}