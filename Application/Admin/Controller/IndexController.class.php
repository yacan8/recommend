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

//		 $Model = M('News');
//        $list = M('News')->where('id between 9000 and 9400')->select();
//		 echo count($list);
//		 for ($i=0; $i < count($list); $i++) {
//		 	$list[$i]['content'] = str_replace('/recommoned','/recommend',$list[$i]['content']);
//		 	echo $Model->where('id = '.$list[$i]['id'])->save($list[$i]);
//             echo "<br>";
//		 }
    	$this->display('index');
    }

}
