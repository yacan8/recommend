<?php
namespace Home\Model;
use Think\Model\RelationModel;

class ZanModel extends RelationModel{
    protected $_link = array(
        'commentNewsIdAndType' => array(
            'mapping_type' => self::BELONGS_TO,
            'class_name' => 'Comment',
            'foreign_key'=>'comment_id',
            'mapping_fields'=>'news_id',
            'as_fields' => 'news_id',
            'condition' => 'delete_tag = 0'
        )
    );

    public function getCountByContributor($user_id){
        $DB_PREFIX = C('DB_PREFIX');
        $sql = "select SUM(zan_count) count from {$DB_PREFIX}comment where news_id in (select id from {$DB_PREFIX}news where contributor = $user_id and delete_tag = 0) and delete_tag = 0";
        $result = $this->query($sql);
        return $result[0]['count'];
    }

    /**
     * [getDynamics3 description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getDynamics3($id){
        $comment_id = $this -> where(array('id'=>$id)) -> getField('comment_id');
        return D('Comment')->getDynamics2($comment_id);
    }

    public function getMessage1($id){
        $comment_id = $this -> where(array('id'=>$id)) -> getField('comment_id');
        return D('Comment')->getDynamics2($comment_id);
    }

    public function getZanListByUserIdAndBeginTime($user_id,$allowRecommendBeginTime){
        $newsModel = M('News');
        $result = $this->relation('commentNewsIdAndType')->where(array( 'user_id' => $user_id ,'time'=> array('gt',$allowRecommendBeginTime))) ->order('time desc')->select();
        foreach ( $result as &$item) {
            $item['type'] = $newsModel->where(array('id'=>$item['news_id']))->getField('type');
        }
        return $result;
    }
}
