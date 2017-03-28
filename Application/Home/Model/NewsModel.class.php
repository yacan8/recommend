<?php
namespace Home\Model;
use Think\Model\RelationModel;
class NewsModel extends RelationModel{
	/**
	 * [$_link 关联属性]
	 * @var array
	 */
	protected $_link = array(
	    'type'  =>  array(
	    	'mapping_type' =>self::BELONGS_TO,
	        'class_name' => 'Type',
	        'foreign_key'=>'type',
	        'mapping_fields'=>'type',
			'as_fields' =>'type'
	    ),

	    'sections' => array(
	    	'mapping_type' =>self::BELONGS_TO,
	    	'class_name' => 'Sections',
	        'foreign_key'=>'sections',
	        'mapping_fields'=>'sections',
	        'as_fields'=>'sections'
	    ),
		'user' => array(
	    	'mapping_type' =>self::BELONGS_TO,
	    	'class_name' => 'Login',
	        'foreign_key'=> 'contributor',
	        'mapping_fields'=>'icon,nickname'
	    )
	);

	/**
	 * 通过ID查询NEWS
	 * @param [Integer] $[id] [<需要查询的ID>]
	 * @return [List] [<返回查询到的News>]
	 */
	public function getById($id){
		$List = $this -> relation(true) ->find($id);
		if($List!=null){
			$str = '';
			$Date = new \Org\Util\Date();
			for ($i=0; $i < count($List); $i++) {
				$str = $str.$List['label'][$i]['label_id'];
				if($i != count($List))
					$str = $str.",";
			}
			$condition['id'] = array('in',$str);
			$List['publish_time'] = substr($List['publish_time'],0,10);
			$count = count($List['comment'])>4?4:count($List['comment']);
			for ($j=0; $j < $count; $j++) {
				$List['comment'][$j]['time'] = $Date ->timeDiff($List['comment'][$j]['time']);
				if(strpos($List['comment'][$j]['time'],'秒')||$List['comment'][$j]['time']==''){
					$List['comment'][$j]['time'] = '刚刚';
				}
			}
			return $List;
		}else{
			return null;
		}

	}

	/**
	 * 获取头条，若状态为1个数小于5，则补充时间最新的新闻，直至足够5个
	 * @return [List] [<返货头条的List>]
	 */
	public function getHeadLines(){
		$List = $this->relation('type') -> where('state=1 and delete_tag = 0') ->order('publish_time desc')-> field('id,image,title,type,image_thumb,publish_time')->select();
		if(count($List)<5){
			$LackList = $this->relation('type')->where('state=0 and delete_tag = 0')->order('publish_time desc')-> field('id,image,image_thumb,title,type,publish_time')->limit('0,5')->select();
			$j = 0;
			for ($i=count($List); $i < 5; $i++) {
				$List[$i] = $LackList[$j];
				$j++;
			}
		}

		return $List;
	}

	/**
	 * [getHotTop7 获取近一个月最热新闻]
	 * @return [List] [返回查询到的七条新闻]
	 */
	public function getHotTop7(){
		// $data = date("Y-m-d H:i:s",strtotime("-1 month"));
		$data = date("Y-m-d H:i:s",strtotime("-2 year"));
		$List = $this->order('browse desc')-> where("publish_time >= '$data' and delete_tag = 0") -> field('id,image,title,type,image_thumb')->limit('0,7')->relation('type')->select();
		return $List;
	}

	/**
	 * [search 搜索]
	 * @param  [string] $key [传入的关键字]
	 * @param  [Integer] $page [传入的页数]
	 * @return [List]      [查询到的列表]
	 */
	public function search($key,$page){
		$where['name']  = array('like', '%'.$key.'%');
		$where['title']  = array('like','%'.$key.'%');
		$where['_logic'] = 'or';
		$where['delete_tag'] = false;
		$page = ($page-1)*10;
		$List = $this->relation(['type','user'])->where($where)->order('publish_time desc')->field('id,title,publish_time,browse,type,image,image_thumb,contributor')->limit("$page,10")->select();
		$List = $this->GenerateNews($List);
		for ($i=0; $i < count($List); $i++) {
			$List[$i]['title'] = str_replace($key, "<font color='red'>".$key."</font>", $List[$i]['title'] );
		}
		return $List;
	}


	/**
	 * [str_supplementId 获取头条不足时补充的头条新闻ID字符串 头条充足 则返回null]
	 * @return [str] [返回补充新闻ID组成的字符串]
	 */
	public function str_supplementId(){
		$HeadLinesNum = $this -> where('state=1')->count();
		if($HeadLinesNum<5){
			$str = '';
			$LackNum = 5-$HeadLinesNum;
			$LackList = $this->limit('0,5') ->where('state=0 and delete_tag = 0')->order('publish_time desc')-> field('id')->select();
			for ($i=0; $i < $LackNum; $i++) {
				$str = $str.$LackList[$i]['id'];
				if($i!= $LackNum-1)
					$str = $str.",";
			}
			return $str;
		}else{
			return null;
		}
	}
	/**
	 * [getTop10 获取除被补充外的最新10条新闻]
	 * @return [List] [返回前十条最新News]
	 */
	public function getTop10(){
		$condition['delete_tag'] = (bool)0;
		$List = $this->relation(['type','user'])->where($condition)->field('id,title,publish_time,browse,type,image,image_thumb,sections,contributor,comment_count,content')->order('publish_time desc')->limit('0,10')->select();
		$List = $this->GenerateNews($List);
		return $List;
	}

	public function getNewsById($id){
		$condition['delete_tag'] = false;
		$List = $this->relation(['type','user'])->where($condition)->field('id,title,publish_time,browse,type,image,image_thumb,sections,contributor,comment_count,content')->find($id);
		$List = $this->GenerateNewsItem($List);
		return $List;
	}




	/**
	 * [GenerateNews 在传入的新闻列表中添加正确的显示信息,如标签，评价个数]
	 * @param [type] $List [description]
	 */
	public function GenerateNews($List){
		$Date = new \Org\Util\Date();
		for ($i=0; $i < count($List); $i++) {
			$List[$i]['PublishTime'] = $Date ->timeDiff($List[$i]['publish_time']);
			$List[$i] = $this->GenerateNewsItem($List[$i]);
		}
		return $List;
	}


	function GenerateNewsItem($item){
		$item['url'] = U('/n/'.$item['id']);
		if($item['image']==''){
			$imgArr = getNewsImg2($item['content'],3);
			if( count($imgArr)== 0 ){
				$item['show_type'] = '0';
			}else if(count($imgArr)>0 && count($imgArr)<3){
				$item['show_type'] = '1';
				$item['image'] = U('Image/img',array('w'=>140,'h'=>100,'image'=> urlencode($imgArr[0].'!featrue')),'',false,false);
				$item['image_thumb'] = U('Image/img',array('w'=>140,'h'=>100,'image'=> urlencode($imgArr[0].'!featrue')),'',false,false);
			}else{
				foreach($imgArr as &$img){
					$img = U('Image/img',array('w'=>140,'h'=>100,'image'=> urlencode($img.'!featrue')),'',false,false);
				}
				$item['image'] = $imgArr;
				$item['show_type'] = '2';
			}
		}else{
			$item['image'] = U('Image/img',array('w'=>140,'h'=>100,'image'=> urlencode($item['image'].'!featrue')),'',false,false);
			$item['image_thumb'] = U('Image/img',array('w'=>140,'h'=>100,'image'=> urlencode($item['image_thumb'].'!featrue')),'',false,false);
			$item['show_type'] = '1';
		}
		unset($item['content']);
		return $item;
	}



	/**
	 * [GetSelectType 通过类型和页数和栏目获取获取10条新闻，若栏目为空，则无栏目限制]
	 * @param [Integer] $type [传入的类型的ID]
	 * @param [Integer] $page [传入的页数]
	 * @param [bool] $in_index [是否在主页]
	 * @param [string] $sections [传入的栏目值]
	 * @return [List] [查询到的列表]
	 */
	public function getSelectType($type,$page,$in_index=false,$sections=''){
		if($type!=0)
			$condition['type'] = $type;
		if($sections !='')
			$condition['sections'] = $sections;
		$condition['delete_tag'] = false;
		$page = ($page-1)*10;
		$List =  $this->relation(['type','user']) ->where($condition)->limit("$page,10")->field('id,title,publish_time,browse,type,image,image_thumb,sections,contributor,comment_count,content')->order('publish_time desc')->select();
		$List = $this->GenerateNews($List);
		return $List;
	}


	public function getIssueList($user_id,$page,$count,$order = 'newest'){
		if($order == 'newest'){
			$order = 'publish_time desc';
		}else{
			$order = 'browse desc';
		}
		$condition['contributor'] = $user_id;
		$condition['delete_tag'] = false;
		$List =  $this->relation(['type','user']) ->where($condition)->page($page,$count)->field('id,title,publish_time,browse,type,image,image_thumb,sections,contributor,comment_count,content')->order($order)->select();
		$List = $this->GenerateNews($List);
		return $List;
	}
	public function getCountByUserId($user_id){
		return $this->where(array('contributor'=>$user_id,'delete_tag'=>false))->count();
	}

//	/**
//	 * [getSectionsList 通过栏目获取访谈新闻列表]
//	 * @param [string] $[sections] [<传入的栏目值>]
//	 * @return [List] [返回查询到的列表]
//	 */
//	public function getSectionsList($sections){
//		$condition['sections'] = $sections;
//		$List = $this->relation(true)->where($condition)->field('id,title,publish_time,browse,type,image,image_thumb,sections,contributor,comment_count,content')->order('publish_time desc')->limit('0,15')->select();
//		$List = $this->GenerateNews($List);
//		return $List;
//	}

	/**
	 * [getTitlePreAndNext 通过文章ID获取上一篇和下一篇的标题和id]
	 * @param  [Integer] $id [传入的ID]
	 * @return [array]     [带'pre'和'next'键值的数组]
	 */
	public function getTitlePreAndNext($id){
		$next_condition['id']  = array('gt',$id);
		$pre_condition['id']  = array('lt',$id);
		$next = $this->where($next_condition)->limit('1')->field('id,title')-> select();
		$pre  = $this->where($pre_condition)->limit('1')->order('id desc')->field('id,title')->select();
		$result['next'] = $next[0];
		$result['pre'] = $pre[0];
		return $result;
	}


	public function getDynamics4($id){
		$result1 = $this->relation(['user'])->where(array('id'=>$id,'delete_tag'=>false))-> field('id,contributor,image,content,title')->select();
		$result = $result1[0];

		if($result['image'] == '' || $result['image']== null){
			$img = getNewsImg($result['content']);
			if( $img == '' || $img == null ) {
				$result['image'] = '';
			}else{
				$result['image'] = getNewsImg($result['content']);
			}
		}


		if($result['image'] !== '' ){
			$result['image'] = U('Image/img',array('image'=>urlencode($result['image']).'!feature'),false,false);
		}else{
			$result['image'] = __ROOT__.'/Public/img/链接.png';
		}
		unset($result['content']);
		return $result;
	}



	public function getRecommendByKeyword($keyword_id_arr,$read,$begin_time){

		$DB_PREFIX = C('DB_PREFIX');
		if( $read ){
			$condition['_string'] = 'n.id not in ('.$read.')';
		}
		if( count($keyword_id_arr) ) {
			$condition['_string'] = 'nkb.keyword_id in ('.join(',',$keyword_id_arr).')';
		}
		$condition['_string'] = 'n.id = nkb.news_id';
		$condition['publish_time'] = array('gt',$begin_time);
		$result = $this	-> field('n.id')
						-> table($DB_PREFIX.'news n,'.$DB_PREFIX.'news_keyword_belong nkb')
//						-> field('n.id id,n.title title ,n.publish_time publish_time,n.browse browse,n.type type,n.image image,n.image_thumb image_thumb,n.contributor contributor,n.comment_count comment_count,n.content content')
						-> where($condition)
						-> select();
//		dump($this->getLastSql());
//		$result = $this->GenerateNews($result);
		$resultArr = array();
//		$start  = microtime(true);
		foreach($result as $item){
			array_push($resultArr,$item['id']);
		}
//		$end = microtime(true);
//		dump($end - $start);
		return $resultArr;
	}

	public function getRecommendByType($type_arr,$not_in,$begin_time){
		if( count($not_in) ) {
			$condition['_string'] = 'id not in ('.join(',',$not_in).')';
		}
		if( count($type_arr) ) {
			$condition['_string'] = 'type in ('.join(',',$type_arr).')';
			$condition['publish_time'] = array('gt',$begin_time);
			$result = $this
//					-> field('id,title,publish_time,browse,type,image,image_thumb,sections,contributor,comment_count,content')
					-> where($condition)
					-> getField('id',true);
		} else {
			$result = array();
		}
		return $result;
	}

	public function getByKeywordId($keyword_id,$begin_time,$num,$not_in){
		$DB_PREFIX = C('DB_PREFIX');
		if( $not_in ){
			$condition['_string'] = 'n.id not in ('.$not_in.')';
		}
		$condition['_string'] = 'n.id = nkb.news_id';
		$condition['nkb.keyword_id'] = $keyword_id;
		$condition['n.publish_time'] = array('gt',$begin_time);
		$result = $this
				->relation(['type','user'])
				-> table($DB_PREFIX.'news n,'.$DB_PREFIX.'news_keyword_belong nkb')
				-> field('n.id id,n.title title ,n.publish_time publish_time,n.browse browse,n.type type,n.image image,n.image_thumb image_thumb,n.contributor contributor,n.comment_count comment_count,n.content content')
				-> where($condition)
				-> order('n.publish_time')
				-> limit($num)
				-> select();
		$result = $this->GenerateNews($result);
		return $result;
	}

	public function getByTypeId($type_id,$begin_time,$num,$not_in){
		$condition['publish_time'] = array('gt',$begin_time);
		if ( $not_in ) {
			$condition['_string'] = 'id not in ('.$not_in.')';
		}
		if ( $type_id ) {
			$condition['type'] = $type_id;
			$result = $this
					->relation(['type','user'])
					-> field('id,title,publish_time,browse,type,image,image_thumb,sections,contributor,comment_count,content')
					-> where($condition)
					-> limit($num)
					-> selsct();
			$result = $this->GenerateNews($result);
		} else {
			$result = array();
		}
		return $result;
	}


}
