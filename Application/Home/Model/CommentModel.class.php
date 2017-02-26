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

    /**
     * [getDynamics2 description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getDynamics2($id){
        $DBPREFIX = C('DB_PREFIX');
        $result = $this->relation(['user'])->where(array('id'=>$id))->find();

        $newsModel = M('News');

        $result['newsInfo'] = $newsModel->where(array('id'=>$result['news_id']))->field('id,content,image,title')->find();
        if($result['newsInfo']['image'] == '' || $result['newsInfo']['image']== null){
            $img = getNewsImg($result['newsInfo']['content']);
            if( $img == '' || $img == null ) {
                $result['newsInfo']['image'] = '';
            }else{
                $result['newsInfo']['image'] = getNewsImg($result['newsInfo']['content']);
            }

        }else{
            $result['newsInfo']['image'] = 'news/'.$result['newsInfo']['image'];
        }
        if($result['newsInfo']['image'] !== '' ){
            $result['newsInfo']['image'] = U('Image/img',array('image'=>urlencode($result['newsInfo']['image']).'!feature'),false,false);
        }else{
            $result['newsInfo']['image'] = __ROOT__.'/Public/img/链接.png';
        }

        if($result['reply'] != '0'){
            $reply_content = M('')->query('select c.user_id user_id, l.nickname nickname,c.content content from '.$DBPREFIX.'comment c,'.$DBPREFIX.'login l where l.id = c.user_id and c.id = '.$result['reply']);
            $result['replyContent'] = $reply_content[0];
        }else{
            $result['replyContent'] = '';
        }
        return $result;
    }

    public function getMessage2($id){
        return $this->getDynamics2($id);
    }

    public function getMessage3($id){
        return $this->getDynamics2($id);
    }

}
?>
