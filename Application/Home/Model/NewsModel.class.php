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
		$LoginModel = D('Login');
		// $MessageModel = D('Message');
		$List = $this -> relation(true) ->find($id);
		if($List!=null){
			$str = '';
			$Date = new \Org\Util\Date();
			for ($i=0; $i < count($List); $i++) {
				$str = $str.$List['label'][$i]['label_id'];
				if($i != count($List))
					$str = $str.",";
			}
			// $LabelModel = M('Label');
			$condition['id'] = array('in',$str);
			// $LabelList =  $LabelModel -> where($condition) ->field('label')->select();
			// $List['LabelName'] = $LabelList;
			$List['publish_time'] = substr($List['publish_time'],0,10);
			$count = count($List['comment'])>4?4:count($List['comment']);
			for ($j=0; $j < $count; $j++) {
				$List['comment'][$j]['time'] = $Date ->timeDiff($List['comment'][$j]['time']);
				if(strpos($List['comment'][$j]['time'],'秒')||$List['comment'][$j]['time']==''){
					$List['comment'][$j]['time'] = '刚刚';
				}
				// $List['comment'][$j] = $MessageModel->generate($List['comment'][$j]);
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
		$List = $this->relation('type') -> where('state=1') ->order('publish_time desc')-> field('id,image,title,type,image_thumb,publish_time')->select();
		if(count($List)<5){
			$LackNum = 5-count($List);
			$LackList = $this->relation('type')->where('state=0')->order('publish_time desc')-> field('id,image,image_thumb,title,type,publish_time')->limit('0,5')->select();
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
		$List = $this->order('browse desc')-> where("publish_time >= '$data'") -> field('id,image,title,type,image_thumb')->limit('0,7')->relation('type')->select();
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
			$LackList = $this->limit('0,5') ->where('state=0')->order('publish_time desc')-> field('id')->select();
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
		$List = $this->relation(['sections','type','user'])->field('id,title,publish_time,browse,type,image,image_thumb,sections,contributor,comment_count')->order('publish_time desc')->limit('0,10')->select();
		$List = $this->GenerateNews($List);
		return $List;
	}

	/**
	 * [GenerateNews 在传入的新闻列表中添加正确的显示信息,如标签，评价个数]
	 * @param [type] $List [description]
	 */
	public function GenerateNews($List){

		$TypeModel = D('Type');
		$Date = new \Org\Util\Date();
		for ($i=0; $i < count($List); $i++) {
			$List[$i]['PublishTime'] = $Date ->timeDiff($List[$i]['publish_time']);
			$List[$i]['url'] = U('/n/'.$List[$i]['id']);
		}
		return $List;
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
		$page = ($page-1)*10;
		$List =  $this->relation(['type','user']) ->where($condition)->limit("$page,10")->field('id,title,publish_time,type,browse,image,image_thumb,contributor,comment_count')->order('publish_time desc')->select();
		$List = $this->GenerateNews($List);
		return $List;
	}

	/**
	 * [getSectionsList 通过栏目获取访谈新闻列表]
	 * @param [string] $[sections] [<传入的栏目值>]
	 * @return [List] [返回查询到的列表]
	 */
	public function getSectionsList($sections){
		$condition['sections'] = $sections;
		$List = $this->relation(true)->where($condition)->field('id,title,publish_time,type,browse,image,image_thumb')->order('publish_time desc')->limit('0,15')->select();
		$List = $this->GenerateNews($List);
		return $List;
	}

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


}
