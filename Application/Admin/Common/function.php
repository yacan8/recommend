<?php

/**
 * [mouth_days 根据年月返回当月有多少天]
 * @param  [Integer] $year  [传入的年]
 * @param  [Integer] $mouth [传入的月]
 * @return [Integer]        [返回当月有多少天]
 */
function mouth_days($year,$mouth){
	$days_count = 31 ; 
	$run_year = false;
	if (($year % 4 == 0) && ($year % 100 != 0) || ($year % 400 == 0)) 
		$run_year = true;//为闰年
	if($mouth==2){
		if($run_year)
			$days_count = 29;
		else
			$days_count = 28;
	}else{
		if(in_array(array(1,3,5,7,8,10,12), $mouth))
			$days_count = 31;
		else if(in_array(array(4,6,9,11), $mouth))
			$days_count = 30;
	}
	return $days_count;
}

/**
 * [getStartEndTime 根据前*时间段获取开始时间和结束时间]
 * @param  [Integer] $pre [前pre个月]
 * @return [array]      [带'start','end'键值的开始时间和结束时间]
 */
function getStartEndTime($pre){
	$year = date('Y');
	$mouth = date('m');
	$day = date('d');
	
	if($pre == '-1'){ //当天
		$start = date('Y-m-d H:i:s',mktime(0,0,0,$mouth,$day,$year));//开始时间
		$end = date('Y-m-d H:i:s',mktime(23,59,59,$mouth,$day,$year)); //结束时间
	}else if($pre ==0){//当月
		$days_count = mouth_days($year,$mouth);//当月几天
		$start = date('Y-m-d H:i:s',mktime(0,0,0,$mouth,1,$year));//开始时间
		$end = date('Y-m-d H:i:s',mktime(23,59,59,$mouth,$days_count,$year)); //结束时间
	}else if($pre == 3){//3个月来
		if($mouth>3)//如果大于3月
			$mouth_start = $mouth-3;
		else{//如果小于3月
			$mouth = $mouth+9;
			$year_start = $year-1;
		}
		$days_count = mouth_days($year,$mouth);//当月几天
		$start = date('Y-m-d H:i:s',mktime(0,0,0,$mouth_start,1,$year_start));//开始时间
		$end = date('Y-m-d H:i:s',mktime(23,59,59,$mouth,$days_count,$year)); //结束时间
	}else if($pre == 12){//一年以来
		$days_count = mouth_days($year-1,$mouth);//当月几天
		$start = date('Y-m-d H:i:s',mktime(0,0,0,$mouth,1,$year-1));//开始时间
		$end = date('Y-m-d H:i:s',mktime(23,59,59,$mouth,$days_count,$year)); //结束时间
	}

    $result['start'] = $start;
    $result['end']   = $end;
    return $result;
}




/*
*功能：php完美实现下载远程图片保存到本地 
*参数：文件url,保存文件目录,保存文件名称，使用的下载方式 
*当保存文件名称为空时则使用远程文件原来的名称 
*/
function getImage($url,$save_dir='',$filename='',$type=0){ 
    if(trim($url)==''){ 
        return array('file_name'=>'','save_path'=>'','error'=>1); 
    } 
    if(trim($save_dir)==''){ 
        $save_dir='./'; 
    } 
    if(trim($filename)==''){//保存文件名 
        $ext=strrchr($url,'.'); 
        if($ext!='.gif'&&$ext!='.jpg'){ 
            return array('file_name'=>'','save_path'=>'','error'=>3); 
        } 
        $filename=uniqid().$ext;
    } 
    if(0!==strrpos($save_dir,'/')){ 
        $save_dir.='/';
    }
    //创建保存目录 
    if(!file_exists($save_dir)&&!mkdir($save_dir,0777,true)){ 
        return array('file_name'=>'','save_path'=>'','error'=>5); 
    } 
    //获取远程文件所采用的方法  
    if($type){ 
        $ch=curl_init(); 
        $timeout=30; 
        curl_setopt($ch,CURLOPT_URL,$url); 
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout); 
        $img=curl_exec($ch); 
        curl_close($ch); 
    }else{ 
        ob_start();  
        readfile($url); 
        $img=ob_get_contents();  
        ob_end_clean();  
    } 
    //$size=strlen($img);
    //文件大小
    $fp2=@fopen($save_dir.$filename,'a'); 
    fwrite($fp2,$img); 
    fclose($fp2); 
    unset($img,$url); 
    return array('file_name'=>$filename,'save_path'=>$save_dir.$filename,'error'=>0); 
} 




