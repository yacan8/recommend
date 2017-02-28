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