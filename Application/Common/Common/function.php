<?php
/**
 * [exportexcel 到处excel表]
 * @param  array  $data     [传入的列表]
 * @param  array  $title    [标题]
 * @param  string $filename [表名]
 */
function exportexcel($data=array(),$title=array(),$filename='报名表'){     
    header("Content-type:application/octet-stream");     
    header("Accept-Ranges:bytes");    
    header("Content-type:application/vnd.ms-excel");      
    header("Content-Disposition:attachment;filename=".$filename.".xls");    
    header("Pragma: no-cache");    
    header("Expires: 0");     //导出xls 开始    
    if (!empty($title)){
        $title= implode("\t", $title);
        echo "$title\n"; 
        }     
    if (!empty($data)){
        foreach($data as $key=>$val){
            $data[$key]=implode("\t", $data[$key]); 
        }     
    echo implode("\n",$data);
    }
}

/**
 * [checkpassword 判断密码长度是否在6~20之间]
 * @param  [string] $password [传入的密码]
 * @return [bool]           [返回bool]
 */
function checkpassword($password){
	if(strlen($password)<6||strlen($password)>20){
		return false;
	}else{
		return true;
	}
}
/**
* 可以统计中文字符串长度的函数
* @param $str 要计算长度的字符串
*
*/
function abslength($str)
{
    if(empty($str)){
        return 0;
    }
    if(function_exists('mb_strlen')){
        return mb_strlen($str,'utf-8');
    }
    else {
        preg_match_all("/./u", $str, $ar);
        return count($ar[0]);
    }
}


/**
 * [checknickname 判断昵称长度是否超过15]
 * @param  [string] $nickname [传入的昵称]
 * @return [bool]           [返回bool]
 */
function checknickname($nickname){
    if(abslength($nickname)>15){
        return false;
    }else{
        return true;
    }
}

/**
 * [area_change 省份转换]
 * @param  [array] &$area [带country的数组]
 */
function area_change(&$area){
	$sep = array('广西','新疆','宁夏','内蒙古','西藏','香港','澳门');//特别省份
	if(!strstr($area['country'],'省')&&!strstr($area['country'],'市')){
                if(!strstr($area['country'],'中国')){
                    if(strstr($area['country'], '香港')){
                        $area['country'] = '香港';
                        $area['type'] = 1;
                    }else{
                        $area['type'] = 3;
                    }
                }else{
                    $area['type'] = 2;
                }
        }else if(!strstr($area['country'],'省')&&strstr($area['country'],'市')){
                for ($k=0; $k < count($sep); $k++) { 
                    if(strstr($area['country'], $sep[$k]))
                        $area['country'] = $sep[$k];
                    if(($k == count($sep)-1)){
                        if(strstr($area['country'],'市'))
                            $area['country'] = substr($area['country'],0,strrpos($area['country'],'市'));
                        else
                            $area['country'] = str_replace('市','',$area['country']);
                        }
            }
            $area['type'] = 1;
        }else if(strstr($area['country'],'省')){
                $area['country'] = substr($area['country'],0,strrpos($area['country'],'省'));
                $area['type'] = 1;
        }//省份
}


/**
 * [isCrawler 判断是否为爬虫]
 * @return boolean [返回是否为爬虫]
 */
function isCrawler(){
    if(empty($_SERVER['HTTP_USER_AGENT'])) return false;
    $searchEngineBot = array(
        'googlebot'           =>'google',// Google 爬虫
        'mediapartners-google'=>'google',
        'baiduspider'         =>'baidu',// 百度爬虫
        'msnbot'              =>'msn',
        'yodaobot'            =>'yodao',// 有道爬虫
        'youdaobot'           =>'yodao',// 有道爬虫
        'yahoo! slurp'        =>'yahoo',// 雅虎爬虫
        'yahoo! slurp china'  =>'yahoo',// 雅虎爬虫
        'iaskspider'          =>'iask',
        'sogou web spider'    =>'sogou',//搜狗爬虫
        'sogou push spider'   =>'sogou',//搜狗爬虫
        'sosospider'          =>'soso',
        'spider'              =>'other',
        'crawler'             =>'other',
        'msnbot'              =>'yingbi' // Bingbi爬虫
    );
    $spider = strtolower($_SERVER['HTTP_USER_AGENT']);
    foreach($searchEngineBot as $key => $value)
        if(strpos($spider,$key)!== false)
            return true;
    return false;
}

function timeDiff($DateObj,$time){
    $str = $DateObj->timeDiff( $time );
    if(strstr($str,'年')||strstr($str,'月')||strstr($str,'周')||strstr($str,'天'))
        return date('Y-m-d',strtotime($time));
    else
        return $str;
}


function getNewsImg($content){
    preg_match_all('/<img.*?src="(.*?\.(jpg|jpeg|png|bmp|svg|pcx){1}.*?)".*?>/i',$content,$array);

    if( $array[1] ){
        $_src = '';
        for( $i = 0 ; $i < count($array[1]) ; $i++){
            $_src = $array[1][$i];
            if(!preg_match("/^http.*$/",$_src)){
                break;
            }
        }

        
        if( $_src !== '') {
            $result_arr = explode('/',$_src);
            $result = '';
            $sign = false;
            foreach($result_arr as $value){
                if($sign){
                    $result .= '/'.$value;
                }
                if($value == 'Data'){
                    $sign = true;
                }
            }
            $result = strlen($result)>0?substr($result,1):'';
        }else{
            $result = null;
        }
        
    }else{
        $result = null;
    }
    return $result;
}


function getNewsImg2($content,$count){
    preg_match_all('/<img.*?src="(.*?\.(jpg|jpeg|png|bmp|svg|pcx){1}.*?)".*?>/i',$content,$array);

    if($array[1]){
        $imgArr = $array[1];
        $srcs = array();
        for( $i = 0 ; $i < count($imgArr) ; $i++){
            $_src = $imgArr[$i];
            if(!preg_match("/^http.*$/",$_src)){
                $srcs[] = $_src;
            }
            if( count($srcs) == $count){
                break;
            }
        }


        foreach($srcs as &$src){
            $result_arr = explode('/',$src);

            $result = '';
            $sign = false;
            foreach($result_arr as $value){
                if($sign){
                    $result .= '/'.$value;
                }
                if($value == 'Data'){
                    $sign = true;
                }
            }
            if($sign){
                $src = strlen($result)>0?substr($result,1):'';
            }
        }
    }else{
        $srcs = array();
    }
    return $srcs;
}