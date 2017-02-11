<?php
namespace Admin\Model;
use Think\Model;
class TopicPictureModel extends Model{
	
	/**
	 * [getImgByTypeAndId 通过类型和ID获取图片]
	 * @param  [Integer] $type [类型 1为话题 2为评论]
	 * @param  [Integer] $id   [传入的ID]
	 * @return [List]       [查询到的图片列表]
	 */
	public function getImgByTypeAndId($type,$id){
		$condition['type'] = $type;
		$condition['other_id'] = $id;
		return $this->where($condition)->field('image')->select();
	}
	
	/**
	 * [addDataByTypeAndImgStr 通过类型和图片路径添加字符串添加数据]
	 * @param [Integer] $other_id   [归属id 评论id或话题id]
	 * @param [Integer] $type   [图片类型 1为话题 2为评论]
	 * @param [String] $ImgStr [逗号分隔的图片路径字符串]
	 */
	public function addDataByTypeAndImgStr($other_id,$type,$ImgStr){
		$ImgArray = explode(',',$ImgStr);
		$ImgData = array();
		for ($i=0; $i < count($ImgArray); $i++) { 
			$ImgData[] = array('other_id'=>$other_id,'type'=> $type,'image'=>$ImgArray[$i]);
		}
		$PictrueResult = $this->addAll($ImgData);
		return $PictrueResult;
	}
}