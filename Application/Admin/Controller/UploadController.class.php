<?php
namespace Admin\Controller;
use Think\Controller;
class UploadController extends Controller{
	public function _initialize(){

        if (!isset($_SESSION['Adminlogin'])) {
            $this->redirect('Login/index');
        }
 	}
	public function upload() {
		C('SHOW_PAGE_TRACE',false);
		$config = array(
				'maxSize' => 3145728,// 设置附件上传大小
				'exts' => array('jpg', 'gif', 'png', 'jpeg'),// 设置附件上传类型
				'savePath'=>'Upload/',// 设置附件上传目录
				'subName' => null,
				'rootPath'=> './Data/'
			);
		$upload = new \Think\Upload($config);// 实例化上传类
		$info = $upload->uploadOne($_FILES['file']);
		// dump($info['file']);
	    if(!$info) {// 上传错误提示错误信息
	        $this->error($upload->getError());
	    }else{// 上传成功
	        $name = $info['savename'];
			$link['link'] = "http://".$_SERVER['HTTP_HOST'].__ROOT__."/Data/Upload/".$name;
			echo stripslashes(json_encode($link));
	    }
 	 }
 	 public function audio_upload() {
		$config = array(
				'maxSize' => 130145728,// 设置附件上传大小
				'exts' => array('wav', 'mp3', 'wma', 'ogg','cd','ape','acc'),// 设置附件上传类型
				'savePath'=>'audio/',// 设置附件上传目录
				'subName' => null,
				'rootPath'=> './Data/'
			);
		
		ini_set('post_max_size', '1024M');
		$upload = new \Think\Upload($config);// 实例化上传类
		$info = $upload->uploadOne($_FILES['audio_file']);
	    if(!$info) {// 上传错误提示错误信息
	        echo $upload->getError();
	    }else{// 上传成功
	        $result['name'] = "http://".$_SERVER['HTTP_HOST'].__ROOT__.'/Data/audio/'.$info['savename'];
			$m = new \Org\Net\mp3file('Data/audio/'.$info['savename']);
			$result['title'] = I('title','无标题');
			$massage =  $m->get_metadata();
			$result['duration'] =   $massage["Length mm:ss"];
			$result['upload_time'] =   date('Y-m-d H:i:s',time());
			M('Audio')->add($result);
			echo stripslashes(json_encode($result));
	    }
 	 }

}