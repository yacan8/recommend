<?php
namespace Admin\Model;
use Think\Model\RelationModel;
class SimilarityModel extends RelationModel{

    public function getList($user_id1,$num){
        $DB_PREFIX = C('DB_PREFIX');
        $result = $this->table("{$DB_PREFIX}login l,{$DB_PREFIX}portrayal p,{$DB_PREFIX}similarity s")
            ->field('s.user_id2 user_id,l.nickname nickname,l.icon icon,p.last_modify_time last_modify_time,p.portrayal portrayal,s.similarity similarity')
            ->limit($num)
            ->order('s.similarity desc')
            ->where(array(
                '_string' => "l.id = p.user_id and l.id = s.user_id2",
                's.user_id1' => $user_id1
            ))->select();
        return $result;
    }
}