<?php
namespace Admin\Model;
use Think\Model\RelationModel;
class TopicCommentModel extends RelationModel{



	/**
	 * [$_link 关联属性]
	 * @var array
	 */
	protected $_link = array(
	    'topic' => array(
			'mapping_type' =>self::BELONGS_TO,
	        'class_name' => 'Topic',
	        'foreign_key'=>'topic_id',
	        'mapping_fields'=>'id,title'
	    ),
	    'userinfo' => array(
			'mapping_type' =>self::BELONGS_TO,
	        'class_name' => 'Login',
	        'foreign_key'=>'sender',
	        'mapping_fields'=>'id,nickname,icon'
	    )
	);



	/**
	 * [getList 获取列表]
	 * @param  [Integer] $type     [类型]
	 * @param [Integer] $page [传入的页数]
	 * @param [Integer] $count [引用输出最后的条数]
	 * @return [list]           [查询到的列表]
	 */
	public function getList($user_id,$page,&$count){
		if($user_id!=0){
			$condition['user_id'] = $user_id;
		}
		$condition['delete_tag'] = (bool)0;
		$condition['_logic'] = "AND";
		$firstRow = ($page-1)*10;
		$List = $this->relation(array('topic','userinfo'))
					 ->where($condition)
					 ->limit("$firstRow,10")
					 ->field("id,sender,comment_id,content,time,topic_id")
					 ->order('time desc')
					 ->select();
		$count = $this->where($condition)->count();
		for ($i=0; $i < count($List); $i++) { 
			$List[$i]['content'] =  preg_replace('/\[\:(\S{5})\:\]/', '<span class="fr-emoticon fr-emoticon-img" style="background: url(https://cdnjs.cloudflare.com/ajax/libs/emojione/2.0.1/assets/svg/${1}.svg)">&nbsp;</span>', $List[$i]['content']);//特定字符替换为表情
			$List[$i]['content'] = $this->replaceUserText($List[$i]['content']);//@回复替换

			if($List[$i]['userinfo']['icon']=='')
				$List[$i]['userinfo']['icon'] = C('__DATA__').'/login_thumb/default.jpg';
			else{
				$List[$i]['userinfo']['icon'] = C('__DATA__')."/login_thumb/".$List[$i]['userinfo']['icon'];
			}
			
		}
		return $List;
	}



	/**
 	 * [replaceUserText 替换内容中的@[user_id][username]正则表达式内容]
 	 * @param  [String] $content [内容]
 	 * @return [String]          [替换后内容]
 	 */
 	public function replaceUserText($content){
 		$content_first_two = substr($content,0,2);
	 	if($content_first_two == '@['){
			$pattern = '/@\[(\d*?)\]\[username\]:/';
			preg_match($pattern , $content ,$matches);
			$user_id = $matches[1];
			$LoginModel = M('Login');
			$nickname = $LoginModel->where(array('id'=>$user_id))->getField('nickname');
			$url = U('TopicComment/index',array('user_id'=>$user_id),false,false);
			$replace_content= preg_replace('/@\[(\d*?)\]\[username\]:/', '<a href="'.$url.'">@'.$nickname.':</a>', $content);//特定字符替换为表情
			return $replace_content;
		}else
			return $content;
 	}
}