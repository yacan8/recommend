<?php
namespace Admin\Model;
use Think\Model\RelationModel;

/**
 *
 */
class CommentModel extends RelationModel{

    //关联属性
	protected $_link = array(
	    'News'  =>  array(
	    	'mapping_type' =>self::HAS_ONE,
	        'class_name' => 'News',
	        'foreign_key'=>'news_id',
	    )
	);

    public function getList($news_id,$user_id,$page,&$count,$order = 'newest'){
        $homeCommentModel = D('Home/Comment');

        $List = $homeCommentModel->getList($news_id,$user_id,$page,$count,$order,true);
        if($news_id != 0){
            $condition['news_id'] = $news_id;
        }
        if($user_id != 0){
            $condition['user_id'] = $user_id;
        }
        $count = $this -> where($condition) -> count();
        return $List;
    }
}
