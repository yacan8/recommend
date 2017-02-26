<?php
namespace Home\Model;
use Think\Model\RelationModel;

class DynamicsModel extends RelationModel{

     protected $_link = array(
         'user'  =>  array(
         	'mapping_type' => self::BELONGS_TO,
             'class_name' => 'Login',
             'foreign_key'=>'user_id',
             'mapping_fields'=>'nickname,icon'
         )
     );


    public function getList($user_id,$is_one_user,$page,$count){

        if(!$is_one_user){
            $followModel = M('Follow');
            $followCondition['user_id'] = $user_id;
            $followCondition['delete_tag'] = false;
            $followUserId = $followModel -> field('follow_id') -> where($followCondition) -> select(false);
            $dynamicsCondition['_string'] = "user_id in ( $followUserId )";
            $dynamicsCondition['_logic'] = 'and';
        }else{
            $dynamicsCondition['user_id'] = $user_id;
        }

        $dynamics = $this -> where($dynamicsCondition) ->relation('user')-> order('time desc') -> page($page,$count) ->select();
        $commentModel = D('Comment');
        $newsModel = D('News');
        $zanModel = D('Zan');

        foreach ($dynamics as &$dynamic) {
            $dynamicType = $dynamic['type'];
            switch ($dynamicType) {
                case '2':
                    $contentModel = $commentModel;
                    break;
                case '3':
                    $contentModel = $zanModel;
                    break;
                case '4':
                    $contentModel = $newsModel;
                    break;
                default:
                    $contentModel = null;
                    break;
            }
            if($contentModel !== null){
                $dynamic['content'] = call_user_func(array($contentModel,'getDynamics'.$dynamicType),$dynamic['content_id']);
            }
        }

        return $dynamics;

    }
}
