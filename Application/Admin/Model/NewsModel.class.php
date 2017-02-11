<?php
namespace Admin\Model;
use Think\Model\RelationModel;
class NewsModel extends RelationModel{
	
	protected $_auto = array(
	    array('content', 'htmlspecialchars_decode', self::MODEL_BOTH, 'function'),
	);
	//自动检验
	protected $_validate = array(
	    array('title','require','标题必须！'),
	    array('intro','require','简介必须！'),
	);
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
	        'as_fields'=>'type'
	    ),
	    'user'  =>  array(
	    	'mapping_type' =>self::BELONGS_TO,
	        'class_name' => 'Login',
	        'foreign_key'=>'contributor',
	        'mapping_fields'=>'icon,nickname'
	    ),
	    'sections' => array(
	    	'mapping_type' =>self::BELONGS_TO,
	    	'class_name' => 'Sections',
	        'foreign_key'=>'sections',
	        'mapping_fields'=>'id,sections',
	    )
	);

	/**
	 * [getList 获取列表]
	 * @param  [Integer] $type     [类型]
	 * @param  [bool] $upline   [是否为头条]
	 * @param [Integer] $page [传入的页数]
	 * @return [list]           [查询到的列表]
	 */
	public function getList($type,$upline,$page,&$count){
		if($sections!='')
			$data['sections'] = $sections;
		if($type!=0)
			$data['type'] = $type;
		if($upline){
			$data['state'] = '1';
			$List = $this->where($data)->field('id,title,intro,image,image_thumb,type,publish_time,contributor,sections,state')->order('publish_time desc')->relation(true)->select();
		}else if(!$upline){
			$page = ($page-1)*10;
			$List = $this->where($data)->limit("$page,10")->field('id,title,intro,image,image_thumb,type,publish_time,contributor,sections,state')->order('publish_time desc')->relation(true)->select();
			$count = $this->where($data)->count();
		}
		for ($i=0; $i < count($List); $i++) { 
			if($List[$i]['user']['icon'] == '')
				$List[$i]['user']['icon']='default.jpg';
		}
			
		return $List;
	}
	
	/**
	 * [search 新闻搜索]
	 * @param  [string] $key    [传入的关键字]
	 * @param  [Integer] $page   [传入的页数]
	 * @param  [Integer] &$count [返回的总数]
	 * @return [List]         [查询到的列表]
	 */
	public function search($key,$page,&$count){
		$page = ($page-1)*10;
		$List = $this->where("title like '%$key%' or intro like '%$key%'")->limit("$page,10")->field('id,title,intro,image,image_thumb,type,publish_time,contributor,sections')->order('publish_time desc')->relation(true)->select();
		$count = $this->where("title like '%$key%' or intro like '%$key%'")->count();
		return $List;
	}
	/**
	 * [setType 修改分类]
	 * @param [Integer] $o_id [要修改的分类的ID]
	 * @param [Integer] $[n_id] [修改后的分类ID>]
	 * @return [bool] [返回成功与否]
	 */
	public function setType($o_id,$n_id){
		$data['type']=$n_id;
		$this->where("type = $o_id")->save($data);
		$result = $this->where("type = $o_id")->count();
		if($result==0) return true;
		return false;
	}

	/**
	 * [delectById 通过id删除新闻]
	 * @param  [Integer] $id [传入的id]
	 * @return [bool]     [删除是否成功]
	 */
	public function deleteById($id){
		$result = $this->where("id = $id")->delete();
		if($result!=0) return true;
		else return false;
	}

	/**
	 * [upload 图片上传 设置图片名字]
	 */
	public function upload(){
		$config = array(
				'maxSize' => 3145728,// 设置附件上传大小
				'exts' => array('jpg', 'gif', 'png', 'jpeg'),// 设置附件上传类型
				'savePath'=>'news/',// 设置附件上传目录
				'subName' => null,
				'rootPath'=> './Data/'
			);
		$upload = new \Think\Upload($config);// 实例化上传类
		$info = $upload->uploadOne($_FILES['file']);
		if(!$info){
			return '上传错误';
		}else{
			$Savename = $info['savename'];
			$SavenameArray = explode('.',$Savename);
			$thumbname = $SavenameArray[0]."_thumb.jpg";
			$Image = new \Think\Image(\Think\Image::IMAGE_GD);
			$Image->open('./Data/news/'.$Savename);
			$Image->thumb(600,600)->save('./Data/news/'.$SavenameArray[0].'.jpg');
			$Image->thumb(300,300)->save('./Data/news_thumb/'.$thumbname);
			$this->image = $SavenameArray[0].'.jpg';
			$this->image_thumb = $thumbname;
		}

	}
}