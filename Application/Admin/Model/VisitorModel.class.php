<?php
namespace Admin\Model;
use Think\Model;
class VisitorModel extends Model{
	/**
	 * [area_all_uv 通过开始时间和结束时间来获取uv，id为0为总uv，不为0为文章uv]
	 * @param  [date] $start [开始时间]
	 * @param  [date] $end   [结束时间]
	 * @param  [Integer] $id    [文章ID，若为]
	 * @return [type]        [description]
	 */
	public function area_all_uv($start,$end,$id){
		$condition['date'] = array('between',array($start,$end));
		if($id == 0){//当查询所有uv
			$List = $this->field("area label,count(*) data")
						->where($condition)
						->group("area")
						->select();
			return $List;
		}else{
			$VisitorNewsModel = M('VisitorNews');
			$condition['news_id'] = array('eq',$id);
			$List = $VisitorNewsModel->field("area label,count(*) data")
						->where($condition)
						->group("area")
						->select();
			if(count($List)==0)
				return null;
			else return $List;
		}
	}


	/**
	 * [active_uv 根据时间段或者活跃用户（每天阅读文章超过3篇的用户）]
	 * @param  [date] $start [开始时间]
	 * @param  [date] $end   [结束时间]
	 * @return [List]        [查询到的列表]
	 */
	public function active_uv($start,$end){
		$condition['date']  = array('between',array($start,$end));
		$condition['read']  = array('gt',3);
		$List = $this
					->where($condition)
					->field("date 日期,count(*) 活跃用户数量")
					->group('date')
					->order("date")
					->select();
		return $List;
	}

	/**
	 * [init_active_days 将无活跃用户的当月的数目设置为0]
	 * @param  [Integer] $days_count  [传入当月的天数]
	 * @param  [Integer] $year  [传入的约]
	 * @param  [Integer] $mouth  [传入的年]
 	 * @param  [List] $browseList [要初始化的列表]
	 */
	public function init_active_days($days_count,$year,$mouth,&$List){
		for ($i=1; $i <= $days_count; $i++) { //将无浏览的日期的浏览数设置为0
    		if($List[$i]['日期'] != date('Y-m-d',mktime(0,0,0,$mouth,$i,$year))){
    			for ($j=count($List)-1; $j >= $i; $j--)
    				$List[$j+1] =$List[$j];
    			$List[$i-1]['日期']   = date('Y-m-d',mktime(0,0,0,$mouth,$i,$year));
    			$List[$i-1]['活跃用户数量'] = '0';
    		}
    	}
	}
}