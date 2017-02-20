<?php
namespace Home\Model;
use Think\Model\RelationModel;

class CommentModel extends RelationModel{


    protected $_link = array(
        'user'  =>  array(
	    	'mapping_type' => self::BELONGS_TO,
	        'class_name' => 'Login',
	        'foreign_key'=>'user_id',
	        'mapping_fields'=>'nickname,icon'
	    )
    );


    /**
	 * [getMessageCount 获取新闻评价数量]
	 * @param  [Integer] $id [传入的ID]
	 * @return [Integer]     [返回评价数量]
	 */
	public function getCommentCount($id){
		return $this->where(array('news_id'=>$id))->count();
	}

    /**
     * [getList 获取列表]
     * @param  [Integer] $page  [description]
     * @param  [Integer] $count [description]
     * @param  [Integer] $order [description]
     * @return [type]        [description]
     */
    public function getList($news_id ,$user_id = 0,$page = 1,$count = 5,$order='newest'){
        $DBPREFIX = C('DB_PREFIX');
        if( $user_id != 0){
            $condition['user_id'] = $user_id;
        }
        if( $news_id != 0 ){
            $condition['news_id'] = $news_id;
        }
        $condition['delete_tag'] = (bool)0;
        $order = $order == 'newest' ? 'time desc,zan_count desc' : 'zan_count desc,time desc';

        $result = $this ->relation(['user'])->field('id,time,content,user_id,reply,zan_count') -> where($condition) -> order($order) -> page($page,$count) -> select();
        foreach ($result as &$value) {
            if($value['reply'] != '0'){
                $reply_content = M('')->query('select c.user_id user_id, l.nickname nickname,c.content content from '.$DBPREFIX.'comment c,'.$DBPREFIX.'login l where l.id = c.user_id and c.id = '.$value['reply']);
                $value['reply_content'] = $reply_content[0];
            }else{
                $value['reply_content'] = '';
            }
        }
        return $result;
    }

}
?>
