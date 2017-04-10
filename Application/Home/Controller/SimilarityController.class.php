<?php
namespace Home\Controller;
use Think\Controller;

class SimilarityController extends Controller{
    //离线用户相似度、在新闻推荐的时候计算
    public function calculateSimilarity(){
        //兴趣标签、浏览点赞评论信息
        if( session('?login') ) {
            $user_id = session('login');
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
                        $saveOrUpdateResult1 = $similarityModel->where(array('id'=>$isExitOrId))->save($item);
                        $saveOrUpdateResult2 = 1;
                    } else {
                        $saveOrUpdateResult1 = $similarityModel->add($item);
                        $saveOrUpdateResult2 = $similarityModel->add(array('user_id1'=>$item['user_id2'],'user_id2'=>$item['user_id1'],'similarity'=>$item['similarity'],'last_modify_time'=>$time));
                    }
                    $sign = ( $saveOrUpdateResult1 === false || $saveOrUpdateResult2 === false ) ? false : true;
                }
                if( $sign ) {
                    $json = array(
                        'success' => true,
                        'code' => 200,
                        'message' => '计算成功'
                    );
                } else {
                    $json = array(
                        'success' => false,
                        'code' => 500,
                        'message' => '数据保存错误'
                    );
                }

            } else {
                $json = array(
                    'success' => false,
                    'code' => 300,
                    'message' => '缺少画像信息'
                );
            }
        } else {
            $json = array(
                'success' => false,
                'code' => 199,
                'message' => '你还没登录'
            );
        }
        $this->ajaxReturn($json);


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

    public function calculateNewsSimilarityByUserId(){
        ini_set('max_execution_time', '0');
        $news_id = I('post.news_id',0);
        if( $news_id !== 0){
            $newsSimilarityTimeModel = M('NewsSimilarityTime');
            $newsSimilarityTimeInfo = $newsSimilarityTimeModel->where(array('news_id'=>$news_id))->find();
            $newsSimilarityModel = M('NewsSimilarity');
            $calculateSign = false;
            $saveSign = true;
            $timestamp = time();
            $time = date('Y-m-d H:i:s',$timestamp);
            $newsModel = D('News');
            $model = M('');
            $model->startTrans();
            if( $newsSimilarityTimeInfo ) {
                $lastCalculateTime = $newsSimilarityTimeInfo['last_calculate_time'];
                if( strtotime($lastCalculateTime) < time() - 60 * 60 * 24 ) {
                    $calculateSign = true;
                }
                $newsSimilarityTimeInfo['last_calculate_time'] = $time;
                $newsSimilarityTimeResult = $newsSimilarityTimeModel->where(array('id'=>$newsSimilarityTimeInfo['id']))->save($newsSimilarityTimeInfo);
            } else {
                $calculateSign = true;
                $newsSimilarityTimeResult = $newsSimilarityTimeModel->add(array('news_id'=>$news_id,'last_calculate_time'=>$time));
            }

            if ( $calculateSign ) {
                $recommendConfigModel = M('RecommendConfig');
                $recommendConfig = $recommendConfigModel->where(array('state' => array('neq', '0')))->find();
                $allowRecommendTime = $recommendConfig['allow_recommend_time'];


                $allowRecommendTimeStamp = $timestamp - 60 * 60 * 24 * (int)$allowRecommendTime;
                $allowRecommendBeginTime = date('Y-m-d H:i:s', $allowRecommendTimeStamp);
                $calculateDataBeforeFilter = $newsModel->getRelationNewsByNewsId($news_id,$allowRecommendBeginTime);
                $calculateData = array();
                foreach ($calculateDataBeforeFilter as &$item) {
                    $isExit  = $newsSimilarityModel->where(array('news_id1'=>$news_id,'news_id2'=>$item['id']))->find();
                    if ( !$isExit ) {
                        array_push($calculateData,$item);
                    }
                }

                $newsContent = $newsModel->where(array('id'=>$news_id))->getField('content');
                $newsContentWords = $this->getWords($newsContent);
                foreach ($calculateData as &$item) {
                    $words = $this->getWords($item['content']);
                    $similarityInfo =  $newsSimilarityModel->where(array('news_id1'=>$news_id,'news_id2'=>$item['id']))->find();
                    if ( !$similarityInfo ){
                        $item['similarity'] = $this->similarity($words,$newsContentWords);
                        $addResult1  = $newsSimilarityModel->add(array('news_id1'=>$news_id,'news_id2'=>$item['id'],'similarity'=>$item['similarity'],'last_modify_time'=>$time));
                        $addResult2  = $newsSimilarityModel->add(array('news_id2'=>$news_id,'news_id1'=>$item['id'],'similarity'=>$item['similarity'],'last_modify_time'=>$time));
                    } else {
                        $item['similarity'] = $similarityInfo['similarity'];
                        $addResult1 = $addResult2 = 1;
                    }

                    $saveSign = ( $addResult1 === false || $addResult2 === false ) ? false:true;
                    unset($item['content']);
                }
                if( $newsSimilarityTimeResult === false || $saveSign === false ) {
                    $json = array(
                        'success' => false,
                        'code' => 500,
                        'message' => '服务器内部错误',
                    );
                    $model->rollback();
                } else {
                    $json = array(
                        'success' => true,
                        'code' => 200,
                        'message' => '计算成功',
                    );
                    if( I('post.showData') ){
                        $json['attr'] = $calculateData;
                    }
                    $model->commit();
                }
            } else {
                $json = array(
                    'success' => true,
                    'code' => 300,
                    'message' => '内容在最近已被计算',
                );
                if( I('post.showData') ) {
                    $json['attr'] = $newsModel -> getSimilarityContent($news_id,null);

                }
            }
        } else {
            $json = array(
                'success' => false,
                'code' => 400,
                'message' => '参数错误',
            );
        }
        $this->ajaxReturn($json);

    }

    public function getWords($content){
        vendor('phpanalysis.phpanalysis');
        // 严格开发模式
        ini_set('display_errors', 'On');
        ini_set('memory_limit', '64M');
        error_reporting(E_ALL);
        //岐义处理
        $do_fork =  true ;
        //新词识别
        $do_unit = true ;
        //多元切分
        $do_multi = true ;
        //是否预载全部词条
        $pri_dict = true ;
        //初始化类
        \PhpAnalysis::$loadInit = false;
        $pa = new \PhpAnalysis('utf-8', 'utf-8', $pri_dict);
        //载入词典
        $pa->LoadDict();
        $content = str_replace('&nbsp;', '', $content);
        $content = str_replace('&rdquo;', '', $content);
        $content = str_replace('&ldquo;', '', $content);
        $content = str_replace('&hellip;', '', $content);
        $content = str_replace('&mdash;', '', $content);
        $content = str_replace('&middot;', '', $content);
        $content = trim(strip_tags($content));
        //执行分词
        $pa->SetSource($content);
        $pa->differMax = $do_multi;
        $pa->unitWord = $do_unit;
        $pa->StartAnalysis( $do_fork );
        return $pa->GetFinallyIndex();
    }
    public function setWordsArray($data,$calculateContent){
        vendor('phpanalysis.phpanalysis');
        // 严格开发模式
        ini_set('display_errors', 'On');
        ini_set('memory_limit', '64M');
        error_reporting(E_ALL);
        //岐义处理
        $do_fork =  true ;
        //新词识别
        $do_unit = true ;
        //多元切分
        $do_multi = true ;
        //是否预载全部词条
        $pri_dict = true ;
        //初始化类
        \PhpAnalysis::$loadInit = false;
        $pa = new \PhpAnalysis('utf-8', 'utf-8', $pri_dict);
        //载入词典
        $pa->LoadDict();
        if( !$calculateContent ) {
            $content = $data;
            $content = str_replace('&nbsp;', '', $content);
            $content = str_replace('&rdquo;', '', $content);
            $content = str_replace('&ldquo;', '', $content);
            $content = str_replace('&hellip;', '', $content);
            $content = str_replace('&mdash;', '', $content);
            $content = str_replace('&middot;', '', $content);
            $content = trim(strip_tags($content));
            //执行分词
            $pa->SetSource($content);
            $pa->differMax = $do_multi;
            $pa->unitWord = $do_unit;
            $pa->StartAnalysis( $do_fork );
            return $pa->GetFinallyIndex();
        } else {
            foreach ($data as &$item) {
                $content = $item['content'];
                $content = str_replace('&nbsp;', '', $content);
                $content = str_replace('&rdquo;', '', $content);
                $content = str_replace('&ldquo;', '', $content);
                $content = str_replace('&hellip;', '', $content);
                $content = str_replace('&mdash;', '', $content);
                $content = str_replace('&middot;', '', $content);
                $content = trim(strip_tags($content));
                //执行分词
                $pa->SetSource($content);
                $pa->differMax = $do_multi;
                $pa->unitWord = $do_unit;
                $pa->StartAnalysis( $do_fork );
                $item['words'] = $pa->GetFinallyIndex();
                $item['similarity'] = $this->similarity($item['words'],$calculateContent);

            }
            return $data;
        }


    }

    public function test(){
        $a = array(
            'sds' => 'dsadsa'
        );
        echo gettype($this);
    }


}