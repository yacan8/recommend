<?php
namespace Admin\Model;
use Think\Model;
class BrowseModel extends Model{
	//关联属性




	/**
	 * [browse_time_count 根据开始时间和结束时间，返回该时间段内每天文章的浏览次数，若$id不为0，则返回该id对应文章的该时间段每天的浏览量]
	 * @param  [date] $start [开始时间]
	 * @param  [date] $end   [结束时间]
	 * @param  [Integer] $id [传入的文章的ID，若了0则查询全部]
	 * @return [List]        [查询到的列表]
	 */
	public function browse_time_count($start,$end,$id=0){
		$DB_PREFIX = C('DB_PREFIX');//获取表前缀
		$Model = M('');
		if($id==0){
			$field = "DATE_FORMAT(b.time,'%Y-%m-%d') 日期,count(*) 浏览数,(select count(*) from re_visitor v where v.date = DATE_FORMAT(b.time,'%Y-%m-%d')) 访客";
			$condition['news_id'] = array('neq','');
		}else{
			$condition['news_id'] = array('eq',$id);
			$field = "DATE_FORMAT(time,'%Y-%m-%d') 日期,count(*) 浏览数";
		}
		$condition['time']   = array('between',array($start,$end));
		$condition['_logic'] = 'AND';
		$browseList = $Model->table($DB_PREFIX.'browse b')
					->field($field)
					->where($condition)
					->group("DATE_FORMAT(time,'%Y-%m-%d')")
					->order("DATE_FORMAT(time,'%Y-%m-%d')")
					->select();

		if($id==0){
			$condition['date']   = array('between',array($start,$end));
			$Visitor = M('Visitor')
								->field("date 日期,count(*) 访客")
								->group('date')
								->where($condition)
								->select();

		}else{
			$condition['date']   = array('between',array($start,$end));
			$VisitorNewsModel = M('VisitorNews');
			$Visitor = $VisitorNewsModel
							->field("DATE_FORMAT(date,'%Y-%m-%d') 日期,count(*) 访客")
							->group("DATE_FORMAT(date,'%Y-%m-%d')")
							->where($condition)
							->select();

		}

		for ($i=0; $i < count($browseList); $i++) {
			for ($j=0; $j < count($Visitor); $j++) {
				if($browseList[$i]['日期'] == $Visitor[$j]['日期']){
					$browseList[$i]['访客'] = $Visitor[$j]['访客'];
				}
			}
		}
		return $browseList;
	}
	/**
	 * [area_browse 根据开始时间和结束时间，返回该时间段内已地区分组的列表，若$id不为0，则返回该id对应文章该时间段内已地区分组的列表]
	 * @param  [date] $start [开始时间]
	 * @param  [date] $end   [结束时间]
	 * @param  [Integer] $id [传入的文章的ID，若了0则查询全部]
	 * @return [List]        [查询到的列表]
	 */
	public function area_browse($start,$end,$id=0){
		if($id!=0)
			$condition['news_id'] = array('eq',$id);

		$condition['time'] = array('between',array($start,$end));
		$condition['_logic'] = 'AND';
		$browseList = $this->field("area label, count(*) data")->where($condition)->group("area")->select();

		return $browseList;
	}
	/**
	 * [init_days 将无浏览的当月的浏览数设置为0]
	 * @param  [Integer] $days_count  [传入当月的天数]
	 * @param  [Integer] $year  [传入的约]
	 * @param  [Integer] $mouth  [传入的年]
 	 * @param  [List] $browseList [要初始化的列表]
	 */
	public function init_days($days_count,$year,$mouth,&$browseList){
		for ($i=0; $i <$days_count; $i++) { //将无浏览的日期的浏览数设置为0
    		if($browseList[$i]['日期'] != date('Y-m-d',mktime(0,0,0,$mouth,$i+1,$year))){
    			for ($j=count($browseList)-1; $j >=$i; $j--)
    				$browseList[$j+1] =$browseList[$j];
    			$browseList[$i]['日期']   = date('Y-m-d',mktime(0,0,0,$mouth,$i+1,$year));
    			$browseList[$i]['浏览数'] = $browseList[$i]['访客'] ='0';
    		}
    	}
	}

	/**
	 * [type_browse 根据开始时间和结束时间，返回该时间段内已类型的列表]
	 * @param  [date] $start [开始时间]
	 * @param  [date] $end   [结束时间]
	 * @return [List]        [查询到的列表]
	 */
	public function type_browse($start,$end){
		$DB_PREFIX = C('DB_PREFIX');//获取表前缀
		$Model = M('');
		$condition['b.news_id'] = array('neq','');
		$condition['n.id']      = array('neq','');
		$condition['time']      = array('between',array($start,$end));
		$condition['_logic']    = 'AND';
		$List = $Model
				->table($DB_PREFIX.'browse b')
				->field('t.type label,count(*) data')
				->join($DB_PREFIX.'news n on n.id = b.news_id')
				->join($DB_PREFIX.'type t on t.id = n.type')
				->where($condition)
				->group('t.type')
				->select();
		return $List;
	}

	/**
	 * [average_pv_uv 根据时间间隔interval获取该时间间隔的pv和uv列表列表，interval为day、week、month]
	 * @param  [string] $interval [时间间隔]
	 * @return [List]           [查询到的列表]
	 */
	public function average_pv_uv($interval){

		$mouth = date('m');
        $year = date('Y');
        $day = date('d');
        $begin = 0;
        //计算sql语句的开始时间和结束时间
        if($interval == 'day' || $interval == 'week'){
            $t =($interval=='day')?-1:-8;
            for ($i=1; $i <=30; $i++) {
                // $data[] = date("Y-m-d",strtotime($begin." day"));
                $date[$i-1]['end'] = date("Y-m-d H:i:s",mktime(23,59,59,$mouth,$day+$begin,$year));
                $begin = $begin+$t;
                $date[$i-1]['begin'] = date("Y-m-d H:i:s",mktime(0,0,0,$mouth,$day+$begin+1,$year));
            }
        }else if($interval == 'month'){
            $begin ++;
            $t = -1;
            for ($i=1; $i <=12; $i++) { //mktime(0,0,0,$mouth,$i+1,$year)
                $date[$i-1]['end'] = date("Y-m-d",mktime(0,0,-1,$mouth+$begin,1,$year));
                // $data[] = date("Y-m-d",strtotime($begin." month"));
                $begin = $begin+$t;
                $date[$i-1]['begin'] = date("Y-m-d",mktime(0,0,0,$mouth+$begin,1,$year));
            }
        }

		$result = array();
		$model = M('');
		$length = count($date);
		for ($i=0; $i < $length; $i++) {
			$begin = $date[$i]['begin'];
			$end = $date[$i]['end'];
			$beginArray = explode(' ',$begin);
			$endArray = explode(' ',$end);
			$timeBegin = $beginArray[0];
			$timeEnd = $endArray[0];
			$l = $model->query("select count(*) uv,(select count(*) from re_browse where news_id in (select id from re_news where publish_time between '$begin' and '$end')) pv,
				(select count(*) from re_news where publish_time between '$begin' and '$end') count
from re_visitor_news where news_id in (select id from re_news where publish_time between '$begin' and '$end')");
			if($interval != 'day')
				$result[$i]['时间'] = $timeBegin."~".$timeEnd;
			else
				$result[$i]['时间'] = $timeBegin;
			if($l[0]['count'] !='0'){
				$result[$i]['uv'] = round((float)$l[0]['uv']/(int)$l[0]['count']);
				$result[$i]['pv'] = round((float)$l[0]['pv']/(int)$l[0]['count']);
			}else{
				$result[$i]['pv'] = 0;
				$result[$i]['uv'] = 0;
			}

		}
		$f_result = array();
		$resultlength= count($result);
		for ($i=0; $i < $resultlength; $i++) {
			$f_result[] = $result[$resultlength-$i-1];
		}
		return $f_result;
	}

}
