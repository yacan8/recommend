<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller {

	public function _initialize(){
        if (!isset($_SESSION['Adminlogin'])) {
            $this->redirect('Login/index');
        }
 	}

    public function index(){
//		 $str = "abcd abcd";
//		 echo str_replace('abcd','---',$str);
//		 $list = M('News')->select();
//		 $Model = M('News');
//		 echo count($list);
//		 for ($i=0; $i < count($list); $i++) {
//		 	echo "$i";
//		 	$list[$i]['content'] = str_replace('http://www.campusleader.cn','/recommoned',$list[$i]['content']);
//		 	$Model->where('id = '.$list[$i]['id'])->save($list[$i]);
//		 }
    	$this->display('index');
    }

}
