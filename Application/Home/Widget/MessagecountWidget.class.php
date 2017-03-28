<?php
namespace Home\Widget;
use Think\Controller;
class MessagecountWidget extends Controller{
    public function MessageCount(){
        if(isset($_SESSION['login'])){
        	$MessageModel = M('Message');
        	$tel = $_SESSION['login'];
        	$count = $MessageModel->where("receiver ='$tel' and state = 1")->count();
            $this->assign('count',$count);
        }
        $this->display('Widget:MessageCount');
    }
}