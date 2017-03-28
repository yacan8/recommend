<?php
namespace Home\Controller;
use Think\Controller;
class DynamicsController extends Controller{

    public function loading(){
        $id = I('get.id',0);
        $user_id = session('login');
        $is_one_user = I('get.is_one_user','false')=='false'?false:true;
        $p = I('get.page',1);
        $count = 10;

        if($id == $user_id){
            M('Login')->where(array('id'=>$id))->save(array('last_dynamics_read_time'=>date("Y-m-d H:i:s",time())));
        }

        $dynamicsModel = D('Dynamics');
        $result = $dynamicsModel -> getList($id,$is_one_user,$p,$count);

        $zanModel = M('Zan');
        foreach( $result as &$item ){
            if( $item['type'] == '2' ) {
                $item['content']['is_zan'] = $zanModel->where(array('comment_id'=>$item['content']['id'],'user_id'=>$user_id))->count();
            }
            unset( $item['content']['newsInfo']['content']);
        }

        $this -> ajaxReturn($result);

    }
}
