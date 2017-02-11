<?php
namespace Home\Widget;
use Think\Controller;
class MessageNumberWidget extends Controller{
    public function MessageNumber(){
        if(isset($_SESSION['login'])){
        	$id = session('login');
        	$MessageManagerModel = M('MessageManager');
        	$count = $MessageManagerModel->where(array('user_id'=>$id,'read_tag'=>(bool)0,'delete_tag'=>(bool)0))->count();
        	// dump($count);
            $this->assign('count',$count);
        }
        $this->display('Widget:MessageNumber');
    }
}