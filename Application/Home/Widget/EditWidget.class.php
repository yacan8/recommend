<?php
namespace Home\Widget;
use Think\Controller;
class EditWidget extends Controller{
    public function edit(){
    	if(session('?login')){
    		$user_id = session('login');
    		$LoginModel = D('Login');
    		$userinfo = $LoginModel->getById($user_id);
            $this->assign('userinfo',$userinfo);
    	}
        $this->display('Widget:edit');
    }
}