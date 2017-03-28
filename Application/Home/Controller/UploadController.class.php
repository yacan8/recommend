<?php
namespace Home\Controller;
use Think\Controller;
class UploadController extends Controller{

 	 public function upl() {
 	 	$config = array(
				'maxSize' => 8145728,// 设置附件上传大小
				'exts' => array('jpg', 'gif', 'png', 'jpeg'),// 设置附件上传类型
				'savePath'=>'topic/',// 设置附件上传目录
				'subName' => null,
				'rootPath'=> './Data/'
			);

		$upload = new \Think\Upload($config);// 实例化上传类
		$info = $upload->upload();
	    if(!$info) {
	        echo $upload->getError();
	    }else{// 上传成功
	        dump($info);
	    }
 	 }
}