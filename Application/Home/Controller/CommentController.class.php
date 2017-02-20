<?php
namespace Home\Controller;
use Think\Controller;
class CommentController extends Controller{


    public function comment(){
        $news_id = I('post.news_id',0);
        $replyId = I('post.reply',0);
        $commentModel = M('Comment');
        $reply = $commentModel -> where(array('id'=>$replyId))->getField('user_id');
        $content = I('post.content','');
        $user_id = session('login');
        $time = date('Y-m-d H:i:s',time());
        if( $news_id == 0 || $content == ''){
            $json['code'] = 400;
            $json['success'] = false;
            $json['message'] = $news_id == 0 ? '参数错误' :'评论内容不能为空';
        }else{
            $json['success'] = true;

            $author = $commentModel->where(array('news_id'=>$news_id))->getField('user_id');
            if( $reply == $author ){
                $json['code'] = 199;
                $json['message'] = '不能回复自己';
            }else{
                $model = M('');
                $model -> startTrans();
                $commentData = array(
                    'news_id' => $news_id,
                    'user_id' => $user_id,
                    'content' => $content,
                    'time'    => $time,
                    'reply'   => $replyId,
                    'delete_tag' => (bool)0,
                    'zan_count' => 0
                );
                //评论写入
                $commentResult = $commentModel -> add($commentData);
                $comment_id = $commentModel -> getLastInsID();

                //消息写入
                $messageModel = M('Message');
                //作者消息
                if( $author != $user_id && $author != $reply) {
                    $authorMessageData = array(
                        'user_id' => $author,
                        'type' => 2,
                        'content_id' => $comment_id,
                        'time' => $time,
                        'delete_tag' => (bool)0
                    );
                    $authorMessageResult = $messageModel->add($authorMessageData);
                }else{
                    $authorMessageResult = 1;
                }

                //被回复的人的消息
                if( $reply != 0 ) {
                    $replyMessageData = array(
                        'user_id' => $reply,
                        'type' => 3,
                        'content_id' => $comment_id,
                        'time' => $time,
                        'delete_tag' => (bool)0
                    );
                    $replyMessageResult = $messageModel->add($replyMessageData);
                }else{
                    $replyMessageResult = 1;
                }


                $dynamicsModel = M('Dynamics');

                $dynamicsData = array(
                    'user_id' => $user_id,
                    'type' => 2,
                    'content_id' => $comment_id,
                    'time' => $time,
                    'delete_tag' => (bool)0
                );
                $dynamicsResult = $dynamicsModel->add($dynamicsData);

                if ( $dynamicsResult !== false && $replyMessageResult !== false && $authorMessageResult !== false && $commentResult !== false ) {
                    $model->commit();
                    $json['code'] = 200;
                    $json['message'] = '评论成功';
                }else{
                    $model->rollback();
                    $json['success'] = false;
                    $json['code'] = 500;
                    $json['message'] = '评论错误，请与管理员联系';
                }
            }

        }
        $this->ajaxReturn($json);
    }

    public function comment_load(){
        $id = I('get.id');
        $p = I('get.page',2);
        $order = I('get.order','newest');
        $commentModel = D('Comment');
        $commentList =  $commentModel->getList($id,0,$p,5,$order);
        $zanModel = M('Zan');
        $user_id = session('login');
        if($commentList!==false){
            foreach ($commentList as &$value) {
                $value['is_zan'] = !(bool)($zanModel->where(array('user_id'=>$user_id,'comment_id'=>$value['id']))->count());
                $value['user_url'] = U('/u/'.$value['user_id'],'',false,false);
                if($value['reply_content']){
                    $value['reply_user_url'] = U('/u/'.$value['reply_content']['user_id'],'',false,false);
                }
            }
            $json['code'] = 200;
            $json['success'] = true;
            $json['attr'] = $commentList;
            $json['has_more'] = count($commentList)==5?true:false;
        }else{
            $json['code'] = 500;
            $json['success'] = false;
            $json['attr'] = array();
        }

        $this->ajaxReturn($json);
    }
}
