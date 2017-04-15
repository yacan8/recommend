<?php
namespace Admin\Model;
use Think\Model\RelationModel;
class PortrayalModel extends RelationModel{
    //关联属性
    protected $_link = array(
        'userInfo' => array(
            'mapping_type' =>self::BELONGS_TO,
            'class_name' => 'Login',
            'foreign_key'=>'user_id',
            'mapping_fields' => 'nickname,icon'
        )
    );

    public function getList($page,&$count){
        $DB_PREFIX = C('DB_PREFIX');
        $result = $this->table("{$DB_PREFIX}login l,{$DB_PREFIX}portrayal p")
            ->field('p.user_id id,l.nickname nickname,l.icon icon,p.last_modify_time last_modify_time')
            ->page($page,$count)
            ->where(array(
                '_string' => "l.id = p.user_id"
            ))->select();

        $count = $this->table("{$DB_PREFIX}login l,{$DB_PREFIX}portrayal p")
            ->where(array(
                '_string' => "l.id = p.user_id"
            ))->count();
        return $result;
    }

    public function search($key){
        $DB_PREFIX = C('DB_PREFIX');
        $result = $this->table("{$DB_PREFIX}login l,{$DB_PREFIX}portrayal p")
                        ->field('p.user_id id,l.nickname nickname,l.icon icon,p.last_modify_time last_modify_time')
                        ->where(array(
                            '_string' => "(l.nickname like '%$key%' or l.tel like '%$key%') and l.id = p.user_id"
                        ))->select();
        return $result;
    }

    public function getByUserId($user_id){
        $DB_PREFIX = C('DB_PREFIX');
        $result = $this->table("{$DB_PREFIX}login l,{$DB_PREFIX}portrayal p")
            ->field('p.user_id user_id,l.nickname nickname,l.icon icon,p.last_modify_time last_modify_time,p.portrayal portrayal')
            ->where(array(
                '_string' => "l.id = p.user_id",
                'p.user_id' => $user_id
            ))->find();
        return $result;
    }

}