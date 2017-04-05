<?php
namespace Home\Widget;
use Think\Controller;
class SidelistWidget extends Controller{
    public function side(){
        $NewsModel = D('News');

        if( session('?login') ) {
            $similarityController = A('Similarity');
            $userRecommendList = $similarityController->getSimilarityUserInfo(session('login'));
            $this->assign('userRecommendList',$userRecommendList);
        }
        $List = $NewsModel->getHotTop7();
        $this->assign('List',$List);
        $this->display('Widget:sidelist');
    }
}