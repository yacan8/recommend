<?php
namespace Home\Model;
use Think\Model\RelationModel;

class ZanModel extends RelationModel{

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
