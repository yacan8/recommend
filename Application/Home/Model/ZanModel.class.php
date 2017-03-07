<?php
namespace Home\Model;
use Think\Model\RelationModel;

class ZanModel extends RelationModel{


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
}
