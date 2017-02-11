<?php
namespace Admin\Model;
use Think\Model\RelationModel;
class LoginModel extends RelationModel{

	//关联数据
	protected $_link = array(
		'collection' => array(
		    'mapping_type' =>  self::MANY_TO_MANY,
		    'class_name'=>'News',
		    'foreign_key' =>  'collecting',
		    'relation_foreign_key' => 'collected', 
		    'relation_table' => 'cl_collection', 
		   )
	);

	//自动检验
	protected $_validate = array(
	    array('tel','require','验证码必须！'),
	    array('repassword','password','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致
	    array('tel','/^1[34578]\d{9}$/','请输入正确的用户名'),
	    array('password','checkpassword','密码长度必须为6~20',0,'function'),
	);


	/**
	 * [setPower 权限切换，若为普通人员，设置为管理员，若为管理员，设置为普通人员]
	 * @param [string] $tel [传入的用户名]
	 * @return [bool] [返回修改成功与否]
	 */
	public function setPower($tel){
		$power = $this->where("tel = '$tel'")->getField('power');
		if($power == '1')
			$data['power'] = '0';
		else if($power == '0')
			$data['power'] = '1';
		$result = $this->where("tel = '$tel'")->save($data);
		if($result!=0)
			return true;
		else
			return false;
	}


	/**
	 * [reg_statistics 通过开始时间和结束时间获取注册统计列表]
	 * @param  [date] $start [开始时间]
	 * @param  [date] $end   [结束时间]
	 * @return [List]        [查询到的列表]
	 */
	public function reg_statistics($start,$end){
		$condition['reg_time']   = array('between',array($start,$end));
		$List = $this->field('DATE_FORMAT(reg_time,"%Y-%m-%d") 日期,count(*) 注册数')
			 ->group('DATE_FORMAT(reg_time,"%Y-%m-%d")')
			 ->where($condition)
			 ->select();
			 return $List;
	}


	/**
	 * [init_days 将无浏览的当月的浏览数设置为0]
	 * @param  [Integer] $days_count  [传入当月的天数]
	 * @param  [Integer] $year  [传入的约]
	 * @param  [Integer] $mouth  [传入的年]
 	 * @param  [List] $List [要初始化的列表]
	 */
	public function init_days($days_count,$year,$mouth,&$List){
		for ($i=0; $i < $days_count; $i++) { //将无浏览的日期的浏览数设置为0
    		if($List[$i]['日期'] != date('Y-m-d',mktime(0,0,0,$mouth,$i+1,$year))){
    			for ($j=count($List)-1; $j >= $i; $j--)
    				$List[$j+1] =$List[$j];
    			$List[$i]['日期']   = date('Y-m-d',mktime(0,0,0,$mouth,$i+1,$year));
    			$List[$i]['注册数'] ='0';
    		}
    	}
	}

	public function getInfoByid($id){
		return $this->where(array('id'=>$id))->field('id,nickname')->find();
	}

}