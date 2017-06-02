<?php
namespace Home\Controller;
use Think\Controller;
class ZanController extends Controller{
    public function zan(){
        if(session('?login')){
            $comment_id = I('post.comment_id');
            $user_id = session('login');
            $time = date('Y-m-d H:i:s',time());
            $commentModel = M('Comment');
            $author = $commentModel->where(array('id'=>$comment_id))->getField('user_id');



            $zanModel = D('Zan');
            if($zanModel->where(array('comment_id'=>$comment_id,'user_id'=>$user_id))->count()==0){
                $zanData = array(
                    'comment_id' => $comment_id,
                    'user_id' => $user_id,
                    'time' => $time
                );
                $model = M('');
                $model -> startTrans();
                $zanCountResult =  $commentModel->where(array('id'=>$comment_id)) ->setInc('zan_count',1);
                $zanResult = $zanModel->add($zanData);

                $zan_id = $zanModel->getLastInsID();

                if( $author != $user_id ){
                    $messageModel = M('Message');
                    $messageData = array(
                        'user_id' => $author,
                        'content_id' => $zan_id,
                        'type' => 1,
                        'time' => $time,
                        'delete_tag' => (bool)0
                    );
                    $messageResult = $messageModel->add($messageData);
                }else{
                    $messageResult = 1;
                }



                $dynamicsModel = M('Dynamics');
                $dynamicsData = array(
                    'user_id' => $user_id,
                    'content_id' => $zan_id,
                    'type' => 3,
                    'time' => $time,
                    'delete_tag' => (bool)0
                );
                $dynamicsResult = $dynamicsModel->add($dynamicsData);

                $newsModel = M('News');
                $zanPortrayalData = $zanModel->relation('commentNewsIdAndType')->where(array( 'id' => $zan_id)) ->find();
                $zanPortrayalData['type'] = $newsModel->where(array('id'=>$zanPortrayalData['news_id']))->getField('type');
                //修改用户画像

                $portrayalModel = M('Portrayal');
                $portrayalData = A('Recommend')->getPortrayal($user_id);

                $portrayal = $portrayalData['data'];
                $zanInfo = $portrayal['zanInfo'];

                $sign = true;
                $date = date('Y-m-d',time());
                foreach ($zanInfo as $item) {
                    if( substr($item['time'],0,9) == $date && $item['comment_id'] == $comment_id) {
                        $sign = false;
                        break;
                    }
                }
                if( $sign ) {
                    $keywordBelongModel = D('NewsKeywordBelong');
                    $zanPortrayalData['keywords'] = $keywordBelongModel->getKeywordByNewsId($zanPortrayalData['news_id']);
                    array_push($portrayal['zanInfo'],$zanPortrayalData);
                    $zanPortrayalResult = $portrayalModel->where(array('user_id'=>$user_id))->save(array(
                        'portrayal' => json_encode($portrayal),
                        'last_modify_time' => $time
                    ));

                } else {
                    $zanPortrayalResult = true;
                }


                if ( $dynamicsResult !== false && $messageResult !== false && $zanResult !== false && $zanCountResult !== false && $zanPortrayalResult !== false){
                    //执行写cookie操作

                    $model -> commit();
                    $json['success'] = true;
                    $json['code'] = 200;
                    $json['message'] = '操作成功';
                }else{
                    $model ->rollback();
                    $json['success'] = false;
                    $json['code'] = 500;
                    $json['message'] = '操作失败';
                }
            }else{
                $json['success'] = true;
                $json['code'] = 500;
                $json['message'] = '操作失败';
            }
        }else{
            $json['success'] = false;
            $json['code'] = 199;
            $json['message'] = '你还没登录';
        }
        $this->ajaxReturn($json);
    }


}
