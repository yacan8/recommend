<?php
namespace Home\Model;
use Think\Model;
class CancelFollowModel extends Model{

	public function getGroupByTime($follow_id,$startTime,$endTime){
		$startTime = $startTime.' 00:00:00';
		$endTime = $endTime.' 23:59:59';
		$DB_PREFIX = C('DB_PREFIX');
//		$result = $this->query("select (select count(1) from {$DB_PREFIX}follow f where UNIX_TIMESTAMP(f.time) < UNIX_TIMESTAMP(DATE_FORMAT(b.time,'%Y-%m-%d')) and b.follow_id = f.follow_id and f.delete_tag = 0) count,DATE_FORMAT(b.time,'%Y-%m-%d') date from {$DB_PREFIX}follow b where b.follow_id = {$follow_id} and b.time BETWEEN '{$startTime}' and '{$endTime}' and b.delete_tag = 0 group by DATE_FORMAT(b.time,'%Y-%m-%d') order by time asc");
		$result = $this->query("select count(1) count,DATE_FORMAT(time,'%Y-%m-%d') date from {$DB_PREFIX}follow where follow_id = $follow_id and delete_tag = 0 and time BETWEEN '{$startTime}' and '{$endTime}'  group by DATE_FORMAT(time,'%Y-%m-%d') order by time asc");
		return $result;
	}

}
