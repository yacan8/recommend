<?php
namespace Home\Model;
use Think\Model\RelationModel;

class SimilarityModel extends RelationModel{

    protected $_link = array(
        'userInfo' => array(
            'mapping_type' => self::BELONGS_TO,
            'class_name' => 'Login',
            'foreign_key'=>'user_id2',
            'mapping_fields'=>'icon,nickname,userId'
        )
    );

    public function getByUserId1($user_id1,$num){
        $result = $this->relation('userInfo')->where(array('user_id1'=>$user_id1))->order('similarity desc')->limit($num)->select();
        if ( count($result) ) {
            $userModel = M('User');
            foreach ($result as &$item) {
                if ($item['userInfo']['userid'] != '0') {
                    $item['sex'] = $userModel->where(array('id'=>$item['userInfo']['userid']))->getField('sex');
                }
            }
        }
        return $result;
    }
}