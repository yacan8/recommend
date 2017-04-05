<?php
namespace Home\Controller;
use Think\Controller;

class SimilarityController extends Controller{
    //离线用户相似度
    public function calculateSimilarity($user_id){

        //兴趣标签、浏览点赞评论信息
        header("Content-type: text/html; charset=utf-8");
        $time = date('Y-m-d H:i:s',time());
        $portrayalModel = M('Portrayal');
        $userPortrayalInfo = $portrayalModel->where(array('user_id'=>$user_id))->find();
        if ( $userPortrayalInfo ) {
            $portrayalInfo = json_decode($userPortrayalInfo['portrayal'],true);
            $recommendController = A('Recommend');
            $portrayal = $recommendController->portrayal($portrayalInfo);
            $browseList = $portrayalInfo['browseInfo'];
            $commentList = $portrayalInfo['commentInfo'];
            $zanList = $portrayalInfo['zanInfo'];
            $browseType = $portrayal['browse_type'];
            $browseKeyword = $portrayal['browse_keyword'];
            $commentType = $portrayal['comment_type'];
            $commentKeyword = $portrayal['comment_keyword'];
            $zanType = $portrayal['zan_type'];
            $zanKeyword = $portrayal['zan_keyword'];
            $browseLength = count($browseList);
            $commentLength = count($commentList);
            $zanLength = count($zanList);
            $calculateTimeSpan = M('RecommendConfig')->where(array('state' => array('neq', '0')))->getField('calculate_time_span');
            $timestamp = time();
            $beginTimeStamp = $timestamp - 60 * 60 * 24 * (int)$calculateTimeSpan;
            $calculatePortrayalList = $portrayalModel -> where(array(
                'user_id' => array('neq',$user_id),
                'last_modify_time'=> array('gt',date('Y-m-d H:i:s',$beginTimeStamp))
            ))->select();

            $similarityList = array();
            foreach ($calculatePortrayalList as $calculatePortrayalItem) {

                $calculatePortrayal = json_decode($calculatePortrayalItem['portrayal'],true);
                $calculatePortrayalInfo = $recommendController->portrayal($calculatePortrayal);
                $calculateNum = 0;
                $browseTypeSimilarity = 0;
                $browseKeywordSimilarity = 0;
                $commentTypeSimilarity = 0;
                $commentKeywordSimilarity = 0;
                $zanTypeSimilarity = 0;
                $zanKeywordSimilarity = 0;
                if ( $browseLength > 6 ) {
                    $calculateBrowseList = $calculatePortrayal['browseInfo'];
                    if( count($calculateBrowseList) > 10 ) {
                        $browseTypeSimilarity = $this->similarity($browseType,$calculatePortrayalInfo['browse_type']);
                        $browseKeywordSimilarity = $this->similarity($browseKeyword,$calculatePortrayalInfo['browse_keyword']);
                        $calculateNum += 2;
                    }
                }

                if( $commentLength > 3 ) {
                    $calculateCommentList = $calculatePortrayal['commentInfo'];
                    if( count($calculateCommentList) > 3 ) {
                        $commentTypeSimilarity = $this->similarity($commentType,$calculatePortrayalInfo['comment_type']);
                        $commentKeywordSimilarity = $this->similarity($commentKeyword,$calculatePortrayalInfo['comment_keyword']);
                        $calculateNum += 2;
                    }
                }

                if( $zanLength > 3 ) {
                    $calculateZanList = $calculatePortrayal['zanInfo'];
                    if( count($calculateZanList) > 3 ) {
                        $zanTypeSimilarity = $this->similarity($zanType,$calculatePortrayalInfo['zan_type']);
                        $zanKeywordSimilarity = $this->similarity($zanKeyword,$calculatePortrayalInfo['zan_keyword']);
                        $calculateNum += 2;
                    }
                }

                $similarity = ( $browseTypeSimilarity + $browseKeywordSimilarity + $commentTypeSimilarity + $commentKeywordSimilarity + $zanTypeSimilarity + $zanKeywordSimilarity ) / $calculateNum;
                if( $calculateNum !== 0) {
                    $resultItem = array(
                        'user_id1' => $user_id,
                        'user_id2' => $calculatePortrayalItem['user_id'],
                        'similarity' => $similarity,
                        'last_modify_time' => $time
                    );
                }
                array_push($similarityList,$resultItem);
            }
            $similarityModel = M('Similarity');
            $sign = true;
            foreach ($similarityList as $item) {
                $isExitOrId = $similarityModel->where(array('user_id1'=>$item['user_id1'],'user_id2'=>$item['user_id2']))->getField('id');
                if( $isExitOrId ) {
                    $saveOrUpdateResult = $similarityModel->where(array('id'=>$isExitOrId))->save($item);
                } else {
                    $saveOrUpdateResult = $similarityModel->add($item);
                }
                if( $saveOrUpdateResult === false ) {
                    $sign = false;
                }
            }
            if( $sign ) {
                dump($similarityList);
            }



        }

    }
    //计算两个两个数组的相似度
    public function similarity($words1,$words2){
        //初始化单词个数
        foreach ($words1 as $key => $num) {
            if( !$words2[$key] ){
                $words2[$key] = 0;
            }
        }
        foreach ($words2 as $key => $num) {
            if( !$words1[$key] ){
                $words1[$key] = 0;
            }
        }
        $fz = 0;
        foreach ($words1 as $key => $num) {
            $fz += $words1[$key] * $words2[$key];

        }
        $fm1 = 0.0 ;
        foreach ($words1 as $key => $num) {
            $fm1 += $num * $num;
        }
        $fm2 = 0.0;
        foreach ($words2 as $key => $num) {
            $fm2 += $num*$num;
        }
        $fm = sqrt($fm1) * sqrt($fm2) ;

        return $fz/$fm;
    }

    public function getSimilarityUserInfo($user_id){
        header("Content-type: text/html; charset=utf-8");
        $similarityModel = D('Similarity');
        $result = $similarityModel->getByUserId1($user_id,3);
        if (count($result)) {
            foreach ($result as &$item) {
                $item['similarity'] = round((float)$item['similarity'] * 100).'%';
            }
        }
        return $result;
    }


    public function test(){
        $user_id = 2;
        $portrayalModel = M('Portrayal');
        $userPortrayalInfo = $portrayalModel->where(array('user_id'=>$user_id))->find();
        if ( $userPortrayalInfo ) {
            $lastModifyTime = $userPortrayalInfo['last_modify_time'];
            $portrayalInfo = json_decode($userPortrayalInfo['portrayal'],true);
            $recommendController = A('Recommend');
            $portrayal = $recommendController->portrayal($portrayalInfo);
            $browseList = $portrayal['browseInfo'];
            $commentList = $portrayal['commentInfo'];
            $zanList = $portrayal['zanInfo'];
            $calculateNum = 0;
            $browseLength = count($browseList);
            $commentLength = count($commentList);
            $zanLength = count($zanList);
            echo $this->similarity($portrayal['browse_type'],$portrayal['browse_keyword']);





            dump($portrayal);


        }
    }


}