<?php
namespace Home\Model;
use Think\Model;
class FollowModel extends Model{
	/**
	 * [checkFollow 检查是否已关注]
	 * @param  [Integer] $user_id  [用户id]
	 * @param  [Integer] $follow_id [关注用户ID]
	 * @return [list]           [查询到的列表 如果存在长度且删除标识delete_tag为0 为已关注 否则为已取消关注]
	 */
	public function checkFollow($user_id,$follow_id){
		$condition['user_id'] = $user_id;
		$condition['follow_id'] = $follow_id;
		$condition['_logic'] = 'AND';
		$list = $this->where($condition)->field('id,delete_tag')->select();
		return $list;
	}


	/**
	 * [getFansByUserId 获取粉丝信息]
	 * @param  [Integer] $follow_id [被关注的ID]
	 * @param  [Integer] $page      [页数]
	 * @param  [Integer] $count     [每页显示个数]
	 * @return [List]
	 */
	public function getFansByUserId($follow_id,$page,$count){
		$DB_PREFIX = C('DB_PREFIX');//表前缀

		$condition['f.delete_tag'] = (bool)0;
		$condition['_logic'] = "AND";
		$condition['f.follow_id'] = $follow_id;
		$M = M('');
		$List = $M ->table($DB_PREFIX.'follow f')
					 ->field('f.id id,l.id user_id,l.icon icon,l.nickname,(select count(1) from '.$DB_PREFIX.'follow where follow_id = f.user_id and delete_tag = 0) fans_count,(select count(1) from '.$DB_PREFIX.'follow where user_id = f.user_id and delete_tag = 0) follow_count,u.province province,u.city city,u.sex sex')
					 ->join($DB_PREFIX.'login l on l.id=f.user_id ','left')
					 ->join($DB_PREFIX.'user as u on u.id = l.userId','left')
					 ->where($condition)
					 ->page($page,$count)
					 ->select();
		return $List;

	}

	/**
	 * [getFansByUserId 获取关注信息]
	 * @param  [Integer] $user_id   [用户ID]
	 * @param  [Integer] $page      [页数]
	 * @param  [Integer] $count     [每页显示个数]
	 * @return [List]
	 */
	public function getFollowByUserId($user_id,$page,$count){
		$DB_PREFIX = C('DB_PREFIX');//表前缀

		$condition['f.delete_tag'] = (bool)0;
		$condition['_logic'] = "AND";
		$condition['f.user_id'] = $user_id;
		$M = M('');
		$List = $M ->table($DB_PREFIX.'follow f')
					 ->field('f.id id,l.id user_id,l.icon icon,l.nickname,(select count(1) from '.$DB_PREFIX.'follow where follow_id = f.follow_id and delete_tag = 0) fans_count,(select count(1) from '.$DB_PREFIX.'follow where user_id = f.follow_id and delete_tag = 0) follow_count,u.province province,u.city city,u.sex sex,u.shelfIntroduction intro')
					 ->join($DB_PREFIX.'login l on l.id=f.follow_id ','left')
					 ->join($DB_PREFIX.'user as u on u.id = l.userId','left')
					 ->where($condition)
					 ->page($page,$count)
					 ->select();
		return $List;

	}



	public function getMessageType5($id){

		$M = M('');
 		$DB_PREFIX = C('DB_PREFIX');
 		$array = $M ->table($DB_PREFIX.'login u,'.$DB_PREFIX.'follow f')
 					->field('u.id u_id,u.icon u_icon,u.nickname u_nickname')
 					->where('u.id = f.user_id and f.id = '.$id)
 					->find();
 		return $array;
 	}
}
