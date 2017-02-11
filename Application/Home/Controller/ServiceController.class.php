<?php
namespace Home\Controller;
use Think\Controller;
//短信API接口、外部service服务
class ServiceController extends Controller{


	/**
	 * [sendTemplateSMS 发送短信]
	 * @param [string] $tel [post过来的手机号码]
	 */
	function sendTemplateSMS(){
		if(!IS_POST){
			$json['Code'] = '-1';
			$json['Message'] = "service error!";
		}else{
			//主帐号
			$accountSid= '8a48b55150b23c5b0150b29acd92024c';
			//主帐号Token
			$accountToken= 'd114c68a2dd54ec0aa035c2bc6dffac3';
			//应用Id
			$appId='aaf98f895350b68801535f67cf03198d';
			//请求地址，格式如下，不需要写https://
			$serverIP='sandboxapp.cloopen.com';
			//请求端口 
			$serverPort='8883';
			//REST版本号
			$softVersion='2013-12-26';
			//短信模板
			$tempId = '72349';
			import('ORG.Util.CCPRestSDK');
			$to = I('post.tel');
			if($to == 'forget')
				$to = session('forget');
		    $rest = new \Org\Net\Rest($serverIP,$serverPort,$softVersion);
		    $rest->setAccount($accountSid,$accountToken);
		    $rest->setAppId($appId);
		    $r = rand(100000,999999);
	    	session('SMS',''.$r);
		    $datas[] = ''.$r;
		    $datas[] = '5';
		     // 发送模板短信
		     $result = $rest->sendTemplateSMS($to,$datas,$tempId);
		     if($result == NULL ) {
				$json['Code'] = '0';
				$json['Message'] = "result error!";
		     }
		     if($result->statusCode!=0) {
		        $json['Code'] = $result->statusCode;
		        $json['Message'] = $result->statusMsg;
		         //TODO 添加错误处理逻辑
		     }else{
		     	$json['Code'] = '200';
		        $json['Message']= 'success';
		         //TODO 添加成功处理逻辑
		     }
		     echo json_encode($json);
		}
		
	}
	//Demo调用,参数填入正确后，放开注释可以调用 
	// sendTemplateSMS("18380441225",array('345252','2'),"72349");
	

	/**
	 * [checkSMS 检测验证码]
	 *@param [string] $SMS [POST过来的短信验证码]
	 */
	public function checkSMS(){
		if(!IS_POST){
			$json['Code'] = '-1';
			$json['Message'] = "service error!";
		}else{
			if(!session('?SMS')){
				$json['Code'] = '0';
				$json['Message'] = "验证码无效!";
			}else{
				$SMS = I('post.SMS');
				if($SMS!=session('SMS')){
					$json['Code'] = '2';
					$json['Message'] = "验证码错误!";
				}else{
					$json['Code'] = '200';
					$json['Message'] = "success!";
				}
			}
		}
		echo json_encode($json);
	}


	public function headlines(){
		$token = I('get.token');
		if($token == md5('campusleader/headlines')){
			$Model = D('News');
	        $HeadLines = $Model ->getHeadLines();
	        for ($i=0; $i < count($HeadLines); $i++) {
	        	$HeadLines[$i]['link'] = "http://".$_SERVER['HTTP_HOST'].U('/n/'.$HeadLines[$i]['id']).'?app_nav=null';
	        	$HeadLines[$i]['image'] = "http://".$_SERVER['HTTP_HOST'].U('Image/img',array(
	        		'w' => 640,//图片宽度
	        		'h' => 300,//图片高度
	        		't' => 0,//不为缩略图
	        		'image'=>$HeadLines[$i]['image'].'!feature',
	        		),false,false);
	        	$HeadLines[$i]['image_thumb'] = "http://".$_SERVER['HTTP_HOST'].U('Image/img',array(
	        		'w' => 320,//缩略图宽度
	        		'h' => 150,//缩略图高度
	        		't' => 1,//为缩略图
	        		'image'=>$HeadLines[$i]['image_thumb'].'!feature',
	        		),false,false);
	        	$HeadLines[$i]['type'] = $HeadLines[$i]['type']['type'];
	        	unset($HeadLines[$i]['id']);
	        }
	        echo json_encode($HeadLines,JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		}
	}

	public function hot(){
		$token = I('get.token');
		$page = I('get.page',1);
		if($token == md5('campusleader/hot')){
			$Model = D('News');
	        $List = $Model -> getSelectType(0,$page,ture);
	        for ($i=0; $i < count($List); $i++) {
	        	$List[$i]['link'] = "http://".$_SERVER['HTTP_HOST'].U('/n/'.$List[$i]['id']).'?app_nav=null';
	        	$List[$i]['image'] = "http://".$_SERVER['HTTP_HOST'].U('Image/img',array(
	        		'w' => 640,//图片宽度
	        		'h' => 300,//图片高度
	        		't' => 0,//不为缩略图
	        		'image'=>$List[$i]['image'].'!feature',
	        		),false,false);
	        	$List[$i]['image_thumb'] = "http://".$_SERVER['HTTP_HOST'].U('Image/img',array(
	        		'w' => 320,//缩略图宽度
	        		'h' => 150,//缩略图高度
	        		't' => 1,//为缩略图
	        		'image'=>$List[$i]['image_thumb'].'!feature',
	        		),false,false);
	        	$List[$i]['type'] = $List[$i]['type']['type'];
	        	$List[$i]['type_two'] = $List[$i]['sections'];
	        	$List[$i]['publishTime'] = $List[$i]['PublishTime'];
	        	unset($List[$i]['PublishTime']);
	        	unset($List[$i]['id']);
	        	unset($List[$i]['LabelName']);
	        	unset($List[$i]['browse']);
	        	unset($List[$i]['MessageCount']);
	        	unset($List[$i]['url']);
	        	unset($List[$i]['sections']);
	        }
	        echo json_encode($List,JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		}
	}


}