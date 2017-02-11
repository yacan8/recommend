<?php
namespace Home\Model;
use Think\Model\RelationModel;
class TopicModel extends RelationModel{
	protected $_auto = array(
	    array('content', 'htmlspecialchars_decode', self::MODEL_BOTH, 'function'),
	);
	protected $_link = array(
	    'comment'  =>  array(
	    	'mapping_limit'=>'0,10',
	    	'mapping_type' =>self::HAS_MANY,
	        'class_name'   => 'TopicComment',
	        'condition'    => 'comment_id = 0 and delete_tag = 0',
	        'foreign_key'  => 'topic_id',
	        'mapping_order'=> 'time desc'
	    ),
	    'user' => array(
	    	'mapping_type' => self::BELONGS_TO,
	    	'class_name'   => 'Login',
	    	'foreign_key'  => 'user_id',
	    	'mapping_fields'=> 'id,nickname,icon',
	    )
	);


	/**
	 * [getList 获取话题列表信息]
	 * @param  [Integer] $type  [类型Id]
	 * @param  [Integer] $page  [页数]
	 * @param  [Integer] $count [每页显示个数]
	 * @return [List]        [List查询到的列表]
	 */
	public function getList($type,$page,$count,$user_id=0){
		$firstrow = ($page-1)*$count;
		$DB_PREFIX = C('DB_PREFIX');/*获取数据库前缀*/
		$M = M('');
		if($type!=0)
			$condition = "and ttb.type_id =$type";
		else
			$condition = '';
		if((int)$user_id!=0){
			if(is_int((int)$user_id))
				$condition_user = ' and t.user_id ='.$user_id;
			else
				exit('参数错误');
		}else
			$condition_user = '';
		$List = $M->table($DB_PREFIX.'topic_type_belong ttb,'.$DB_PREFIX.'topic t')
				  ->field('distinct t.id t_id,t.title t_title,t.browse t_browse,t.zan_count t_zan,update_time,l.id top_user_id,l.nickname top_user_nickname,sl.id user_id,sl.nickname user_nickname,sl.icon user_icon,ttb.type_id type_id,(select type from '.$DB_PREFIX.'topic_type where id = ttb.type_id) type,(select count(distinct sender) from '.$DB_PREFIX.'topic_comment where topic_id = t.id and sender != t.user_id) discuss_count,(select count(*) from '.$DB_PREFIX.'topic_comment where topic_id = t.id and comment_id = 0) comment_count')
				  ->join($DB_PREFIX.'login l on l.id = (select sender from '.$DB_PREFIX.'topic_comment where topic_id = t.id order by time desc limit 1)','left')
				  ->join($DB_PREFIX.'login sl on sl.id = t.user_id','left')
				  ->where('t.id = ttb.topic_id and t.delete_tag = 0 '.$condition.$condition_user)
				  ->limit("$firstrow,$count")
				  ->group('t_id')
				  ->order('update_time desc')
				  ->select();
		$Date = new \Org\Util\Date();
		for ($i=0; $i < count($List); $i++) {
			$List[$i]['discuss_count'] = $List[$i]['discuss_count']+1 ;
			$List[$i]['update_time'] = timeDiff($Date,$List[$i]['update_time']) ;
			$List[$i]['content'] =  preg_replace('/\[\:(\S{5})\:\]/', '<span class="fr-emoticon fr-emoticon-img" style="background: url(https://cdnjs.cloudflare.com/ajax/libs/emojione/2.0.1/assets/svg/${1}.svg)">&nbsp;</span>', $List[$i]['content']);//特定字符替换为表情
		}
		return $List;
	}
	

	public function getById($id){
		$List = $this->relation('user')->find($id);
		$List['content'] =  preg_replace('/\[\:(\S{5})\:\]/', '<span class="fr-emoticon fr-emoticon-img" style="background: url(https://cdnjs.cloudflare.com/ajax/libs/emojione/2.0.1/assets/svg/${1}.svg)">&nbsp;</span>', $List['content']);//特定字符替换为表情
		return $List;
	}

	/**
	 * [getAllCount 获取总个数]
	 * @param  [Integer] $type  [类型Id]
	 * @return [Integer]        [返回总个数]
	 */
	public function getAllCount($type=0,$user_id=0){
		$model = M('');
		$DB_PREFIX = C('DB_PREFIX');/*获取数据库前缀*/
		if((int)$type!=0)
			$condition = 'and ttb.type_id ='.$type;
		if((int)$user_id!=0){
			if(is_int((int)$user_id))
				$condition_user = ' and t.user_id ='.$user_id;
			else
				exit('参数错误');
		}else
			$condition_user = '';
		$result = $model->query("select count(distinct ttb.topic_id) count from ".$DB_PREFIX."topic_type_belong ttb,".$DB_PREFIX."topic t where t.id = ttb.topic_id and t.delete_tag = 0 ".$condition.$condition_user);
		$count = $result[0]['count'];
		return $count;
	}


	/**
	 * [getInfoById 通过Id获取信息]
	 * @param  [Integer] $topic_id [话题ID]
	 * @return [array]           
	 */
	public function getInfoById($topic_id){
		$DB_PREFIX = C('DB_PREFIX');/*获取数据库前缀*/
		$M = M('');
		$List = $M->table($DB_PREFIX.'topic_type_belong ttb,'.$DB_PREFIX.'topic t')
				  ->field('distinct t.id t_id,t.title t_title,t.browse t_browse,t.zan_count t_zan,update_time,l.id top_user_id,l.nickname top_user_nickname,sl.id user_id,sl.nickname user_nickname,sl.icon user_icon,ttb.type_id type_id,(select type from '.$DB_PREFIX.'topic_type where id = ttb.type_id) type,(select count(distinct sender) from '.$DB_PREFIX.'topic_comment where topic_id = t.id and sender != t.user_id) discuss_count,(select count(*) from '.$DB_PREFIX.'topic_comment where topic_id = t.id and comment_id = 0) comment_count')
				  ->join($DB_PREFIX.'login l on l.id = (select sender from '.$DB_PREFIX.'topic_comment where topic_id = t.id order by time desc limit 1)','left')
				  ->join($DB_PREFIX.'login sl on sl.id = t.user_id','left')
				  ->where('t.id = ttb.topic_id and t.delete_tag = 0 and t.id = '.$topic_id)
				  ->find();
		$Date = new \Org\Util\Date();
		$List['discuss_count'] = $List[$i]['discuss_count']+1 ;
		$List['update_time'] = timeDiff($Date,$List[$i]['update_time']) ;
		$List['content'] =  preg_replace('/\[\:(\S{5})\:\]/', '<span class="fr-emoticon fr-emoticon-img" style="background: url(https://cdnjs.cloudflare.com/ajax/libs/emojione/2.0.1/assets/svg/${1}.svg)">&nbsp;</span>', $List['content']);//特定字符替换为表情
		return $List;
	}
}