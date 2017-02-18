<?php
namespace Home\Model;
use Think\Model\RelationModel;
class MessageModel extends RelationModel{

	//关联属性
	protected $_link = array(
	    'News'  =>  array(
	    	'mapping_type' =>self::BELONGS_TO,
	        'class_name' => 'News',
	        'foreign_key'=>'other_id',
	        'mapping_fields'=>'id,type,title'
	    )
	);

	/**
	 * [getCountById 通过ID获取新闻评价次数]
	 * @param  [Integer] $id [传入的id]
	 * @return [Integer]     [返回评价个数]
	 */
	public function getCountById($id){
		$Count = $this->where(array('other_id'=>$id))->count();
		return $Count;
	}

	/**
	 * [generate 写入评论者和接收者信息]
	 * @param  [array] $data [传入的数据]
	 * @return [array] [写入后的数据]
	 */
	public function generate($data){
		$LoginModel = D('Login');
		$data['senderinfo'] = $LoginModel ->getById($data['sender']);
		if($data['receiver']!=''){
			$data['receiverinfo'] = $LoginModel ->getById($data['receiver']);
		}
		return $data;
	}
	/**
	 * [getMessageCount 获取新闻评价数量]
	 * @param  [Integer] $id [传入的ID]
	 * @return [Integer]     [返回评价数量]
	 */
	public function getMessageCount($id){
		return $this->where(array('other_id'=>$id))->count();
	}
	/**
	 * [loading 加载评价]
	 * @param  [Integer] $other_id [传入的ID]
	 * @param  [Integer] $page     [传入的页数]
	 * @return [$List]           [查询到的列表]
	 */
	public function loading($other_id,$page){
		$Date = new \Org\Util\Date();
		$page = ($page-1)*4;
		$List = $this->limit("$page,4")->order('time desc')->where(array('other_id'=>$other_id))->select();
		for ($i=0; $i < count($List); $i++) {
			$List[$i]['time'] = $Date ->timeDiff($List[$i]['time']);
			$List[$i] = $this->generate($List[$i]);
		}
		return $List;
	}

	/**
	 * [getByReceiver 通过接收者获取消息]
	 * @param  [string] $receiver [接收者]
	 * @return [List]           [查询到的列表]
	 */
	public function getByReceiver($receiver){
		$Date = new \Org\Util\Date();
		$TypeModel = M('Type');
		$List = $this->relation(true)->where(array('receiver'=>$receiver,'state'=>1))->order('time desc')->select();
		for ($i=0; $i < count($List); $i++) {
			$List[$i]['time'] = $Date ->timeDiff($List[$i]['time']);
			$List[$i] = $this->generate($List[$i]);
			$List[$i]['News']['type'] = $TypeModel->where("id=".$List[$i]['News']['type'])->getField('type');
		}
		return $List;
	}


	/**
	 * [addMessage 添加数据]
	 * @param [array] $data [传入的数据]
	 * @return [bool] [<是否添加成功>]
	 */
	public function addMessage($data){
		$result = $this->add($data);
		if($result!=0) return true;
		else return false;
	}

	/**
	 * [read 标记为已读]
	 * @param  [Integer] $id [传入的ID]
	 * @return [bool]     [返回成功与否]
	 */
	public function read($id){
		$data['state'] = '0';
		$result = $this->where(array('id'=>$id))->data($data)->save();
		if($result!=0) return true;
		else return false;
	}
}
