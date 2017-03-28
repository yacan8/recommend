<?php
namespace Home\Widget;
use Think\Controller;
class UserWidget extends Controller{
    public function user(){
        if(isset($_SESSION['login'])){
        	$LoginModel = D('Login');
        	$id = $_SESSION['login'];
        	$usericon = $LoginModel->where("id =".$id)->getField('icon');
            
            
        	if($usericon=='')
        		$usericon = 'default.jpg';
            $this->assign('icon',$usericon);
        }
        $this->display("Widget:user");
    }
}