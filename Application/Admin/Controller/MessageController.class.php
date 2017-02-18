<?php
namespace Admin\Controller;
use Think\Controller;
class MessageController extends Controller{
	public function _initialize(){
        if (!isset($_SESSION['Adminlogin'])) {
            $this->redirect('Login/index');
        }
 	}
	public function index(){
		
	}


}
