<?php
namespace Home\Widget;
use Think\Controller;
class SimilarityWidget extends Controller{

    public function newsSimilarity($news_id){
        $newsModel = D('News');
        $similarityList = $newsModel->getSimilarityContent($news_id,true);
        $similarityLength = count($similarityList);
        if ( $similarityLength < 8 ) {
            $not_in = array();
            foreach ($similarityList as $item) {
                array_push($not_in,$item['id']);
            }
            $num = 8 - $similarityLength;
            $supplement = $newsModel->getRelationNewsContentByNewsId($news_id,$num,$not_in);
            $similarityList = array_merge($similarityList,$supplement);
        }

        foreach ($similarityList as &$result) {
            if($result['image'] == '' || $result['image']== null){
                $img = getNewsImg($result['content']);
                if( $img == '' || $img == null ) {
                    $result['image'] = '';
                }else{
                    $result['image'] = getNewsImg($result['content']);
                }
            }


            if($result['image'] !== '' ){
                $result['image'] = U('Image/img',array('w'=>80,'h'=>80,'image'=>urlencode($result['image']).'!feature'),false,false);
            }else{
                $result['image'] = __ROOT__.'/Public/img/链接.png';
            }
            unset($result['content']);
        }
        $this->assign('List',$similarityList);
        $this->display('NewsContent:similarity');


    }

    public function userSimilarity(){
        if( session('?login') ) {
            $similarityController = A('Similarity');
            $userRecommendList = $similarityController->getSimilarityUserInfo(session('login'));
            $this->assign('userRecommendList',$userRecommendList);
        }
        $this->display('IndexContent/userSimilarity');
    }
}