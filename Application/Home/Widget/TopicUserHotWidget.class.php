<?php
namespace Home\Widget;
use Think\Controller;
class TopicUserHotWidget extends Controller{
    public function hotUser(){
        $TopicZanModel = D('TopicZan');
        $List = $TopicZanModel->getTopicHotUser(10);
        $this->assign('List',$List);
        $this->display("Widget:TopicUserHot");
    }
}