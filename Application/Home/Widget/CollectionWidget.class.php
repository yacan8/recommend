<?php
namespace Home\Widget;
use Think\Controller;
class CollectionWidget extends Controller{
    public function collection($collection_id){
    	if(session('?login')){
    		$user_id = session('login');
    		$CollectionModel = M('Collection');
    		$count = $CollectionModel->where(array('collection_id'=>$collection_id,'user_id'=>$user_id))->count();
            if($count == 0)
                $bool = false;
            else
                $bool = true;
    	}else{
    		$bool = false;
    	}
    	$this->assign('collection_id',$collection_id);
        $this->assign('bool',$bool);
        $this->display('News:collection');
    }
}
