<?php
namespace Home\Controller;
use Think\Controller;
//推荐引擎
class RecommendController extends Controller{

    public function recommend(){
        $json = $this->recommendEngine();
        $this->ajaxReturn($json);
    }
    public function recommendEngine(){
        $isLogin = session('?login');

        if ($isLogin) {
            $user_id = session('login');

            header("Content-type: text/html; charset=utf-8");
            $model = M('');
            $model->startTrans();
            $recommendConfigModel = M('RecommendConfig');
            //获取推荐配置信息
            $recommendConfig = $recommendConfigModel->where(array('state' => array('neq', '0')))->find();

            $recommendModel = M('Recommend');
            $timestamp = time();


            $portrayalData = $this->getPortrayal($user_id);
            $portrayalInfo = $portrayalData['data'];  //用户画像数据
            $dataSaveResult = $portrayalData['dataSaveResult']; //用户画像信息是否保存成功
            $browseList = $portrayalInfo['browseInfo'];
            $recommendNum = count($browseList) > 10 ? 10 : count($browseList);
            $portrayal = $this->portrayal($portrayalInfo);
            $similarityUserKeyword = $this->getSimilarityUserKeyword($user_id);//相似用户的关联度
            $data = $this->getRecommendWeight($portrayal, $recommendConfig,$similarityUserKeyword);
            $data = $this->multi_array_sort($data,'weightScore');

            $read = $recommendModel->where(array('user_id' => $user_id))->field('news_id')->select(false);
            $recommendArray = $this->getRecommendNum($data, $recommendNum);
            $recommendList = $this->getRecommendData($read, $recommendArray, $recommendConfig, $recommendNum);


            $recommendLength = count($recommendList);
            if ( $recommendLength < 10 ) {
                //推荐个数不够,补充推荐个数(以热度为准 或者兴趣爱好为准)
                $notInArray = array();
                foreach ($recommendList as $item) {
                    array_push($notInArray,$item['id']);
                }
                $count = 10 - $recommendLength;
                $newsModel = D('News');
                $allow_recommend_time = $timestamp -  60 * 60 * 24 * (int)$recommendConfig['allow_recommend_time'];
                $supplement = $newsModel->getByBeginTimeAndNum(date('Y-m-d H:i:s',$allow_recommend_time),$count,array($read,join(',',$notInArray)));
                $recommendList = array_merge($recommendList,$supplement);
            }


            $recommendModel = M('Recommend');
            $time = date('Y-m-d H:i:s' ,$timestamp);
            $recommendDataList = array();

            foreach ( $recommendList as $item ) {
                array_push($recommendDataList, array(
                    'news_id' => $item['id'],
                    'user_id' => $user_id,
                    'time' => $time
                ));
            }

            $recommendResult = $recommendModel->addAll($recommendDataList);

            if ( $dataSaveResult !== false && $recommendResult !== false) {

                $model->commit();
                $json =  array(
                    'success' => true,
                    'code' => 200,
                    'message' => '操作成功',
                    'attr' => $recommendList,
                    'has_more' => count($recommendList) == 10 ? true : false
                );

            } else {
                $json = array(
                    'success' => false,
                    'code' => 500,
                    'message' => '服务器内部错误'
                );
            }
        } else {
            $json = array(
                'success' => false,
                'code' => 199,
                'message' => '你还没登陆'
            );
        }
        return $json;



    }

    public function getRecommendData($read, $recommendArray, $recommendConfig, $recommendNum = 10){

        $newsModel = D('News');

        $allowRecommendTime = $recommendConfig['allow_recommend_time'];
        $timestamp = time();
        $allowRecommendTimeStamp = $timestamp - 60 * 60 * 24 * (int)$allowRecommendTime;
        $allowRecommendBeginTime = date('Y-m-d H:i:s', $allowRecommendTimeStamp);
        $result = array();

        foreach ($recommendArray as $item) {
            if ($item['type'] == 1) {
                $recommendList = $newsModel->getByKeywordId($item['id'], $allowRecommendBeginTime, $item['num'], $read);
            } else {
                $recommendList = $newsModel->getByTypeId($item['id'], $allowRecommendBeginTime, $item['num'], $read);
            }

            foreach ($recommendList as $_item) {
                if (count($result) < $recommendNum) {
                    array_push($result, $_item);
                } else {
                    break;
                }
            }
            if (count($result) >= $recommendNum) {
                break;
            }
        }
        return $result;

    }


    public function getRecommendNum($data, $recommendNum) {
        $recommendArray = array();
        for ($index = 0; $index < count($data); $index++) {
            if ($data[$index]) {
                $item = array('type' => $data[$index]['type'], 'id' => $data[$index]['id']);
                $num = (int)round($data[$index]['weightScore'] * $recommendNum);
                if ($num == 0) {
                    $num = 1;
                }
                $item['num'] = $num;
                array_push($recommendArray, $item);
            }
        }
        return $recommendArray;
    }

    public function getRecommendWeight($portrayal, $recommendConfig,$similarityUserKeyword){
        $keywordData = array();
        $typeData = array();
        $dataType = array(
            'browse_keyword' => 1,
            'comment_keyword' => 1,
            'zan_keyword' => 1,
            'browse_type' => 2,
            'comment_type' => 2,
            'zan_type' => 2,
        );
        $weightSum = 0;
        $typeArray = array();
        foreach ($portrayal as $key => $info) {
            $type = $dataType[$key];
            $weight = $recommendConfig[$key];
            foreach ($info as $_key => $value) {
                if( $similarityUserKeyword ) {
                    $weight = $recommendConfig['follow_keyword'];
                }
                $itemWeight = $value * (int)$weight;
                $item = array();
                $item['type'] = $type;
                $weightSum += $itemWeight;
                $item['id'] = $_key;
                if ($type == 1) {
                    $isExit = false;
                    foreach ($keywordData as &$keywordItem) {
                        if ($keywordItem['id'] == $_key) {
                            $isExit = true;
                            $keywordItem['weight'] += $itemWeight;
                        }
                    }
                    if (!$isExit) {
                        $item['weight'] = $itemWeight;
                        array_push($keywordData, $item);
                    }
                } else {
                    $isExit = false;
                    foreach ($typeData as &$typeItem) {
                        if ($typeItem['id'] == $_key) {
                            $isExit = true;
                            $typeItem['weight'] += $itemWeight;
                        }
                    }
                    if (!$isExit) {
                        $typeItem['weight'] = $itemWeight;
                        array_push($typeData, $item);
                    }
                    if (!in_array($key, $typeArray)) {
                        array_push($typeArray, $key);
                    }
                }
            }
        }
        if ( $similarityUserKeyword ) {
            $weightSum += $similarityUserKeyword['weight'];
            array_push($data,$similarityUserKeyword);
        }
        $data = array_merge($keywordData, $typeData);
        $typeCount = count($typeArray);
        foreach ($data as $key => &$item) {
            if ($item['type'] == 1) {
                $item['weightScore'] = $item['weight'] * $typeCount / $weightSum;
            } else {
                $item['weightScore'] = $item['weight'] / $weightSum;
            }
        }
        return $data;
    }

    //根据键值排序
    function multi_array_sort($arr, $key, $type = SORT_REGULAR, $short = SORT_DESC){
        foreach ($arr as $k => $v) {
            $name[$k] = $v[$key];
        }
        array_multisort($name, $type, $short, $arr);
        return $arr;
    }


    //用户画像
    public function portrayal($portrayalInfo){
        $browse_type = array();
        $browse_keyword = array();
        $browseInfo = $portrayalInfo['browseInfo'];
        foreach ($browseInfo as $item) {
            if ($browse_type[$item['type']]) {
                $browse_type[$item['type']] += 1;
            } else {
                $browse_type[$item['type']] = 1;
            }

            foreach ($item['keywords'] as $_keyword) {
                if ($browse_keyword[$_keyword['keyword_id']]) {
                    $browse_keyword[$_keyword['keyword_id']] += 1;
                } else {
                    $browse_keyword[$_keyword['keyword_id']] = 1;
                }
            }
        }
        $comment_type = array();
        $comment_keyword = array();
        $commentInfo = $portrayalInfo['commentInfo'];
        foreach ($commentInfo as $item) {
            if ($comment_type[$item['type']]) {
                $comment_type[$item['type']] += 1;
            } else {
                $comment_type[$item['type']] = 1;
            }

            foreach ($item['keywords'] as $_keyword) {
                if ($comment_keyword[$_keyword['keyword_id']]) {
                    $comment_keyword[$_keyword['keyword_id']] += 1;
                } else {
                    $comment_keyword[$_keyword['keyword_id']] = 1;
                }
            }
        }

        $zan_type = array();
        $zan_keyword = array();
        $zanInfo = $portrayalInfo['zanInfo'];
        foreach ($zanInfo as $item) {
            if ($zan_type[$item['type']]) {
                $zan_type[$item['type']] += 1;
            } else {
                $zan_type[$item['type']] = 1;
            }

            foreach ($item['keywords'] as $_keyword) {
                if ($zan_keyword[$_keyword['keyword_id']]) {
                    $zan_keyword[$_keyword['keyword_id']] += 1;
                } else {
                    $zan_keyword[$_keyword['keyword_id']] = 1;
                }
            }
        }
        $result = array(
            'browse_type' => $browse_type,
            'browse_keyword' => $browse_keyword,
            'comment_type' => $comment_type,
            'comment_keyword' => $comment_keyword,
            'zan_type' => $zan_type,
            'zan_keyword' => $zan_keyword
        );
        return $result;
    }


    //未登陆获取过滤过期的推荐过的信息的cookie
    public function getAlreadyRecommendCookie(){
        $recommendConfigModel = M('RecommendConfig');
        $calculateTimeSpan = $recommendConfigModel->where(array('state' => array('neq', '0')))->getField('calculate_time_span');
        $timestamp = time();
        $beginTimeStamp = $timestamp - 60 * 60 * 24 * (int)$calculateTimeSpan;
        $alreadyRecommend = json_encode(cookie('already_recommend'));
        if ( !$alreadyRecommend ) {
            $alreadyRecommend = array();
        }
        $result = array();
        $alreadyRecommendId = array();
        foreach ( $alreadyRecommend as $item ) {
            if ( strtotime($item['t']) > $beginTimeStamp ) {
                array_push($alreadyRecommendId,$item['id']);
                array_push($result,$item);
            }
        }
        return array(
            'alreadyRecommendId' => join(',',$alreadyRecommendId),
            'alreadyRecommendCookie' => $result
        );
    }



    //获取之前保存的用户画像cookie
    public function getRecommendCookie(){
        $recommendCookie = json_decode(cookie('recommend'),true);

        if( !$recommendCookie ) {
            $recommendCookie = array(
                'browseInfo' => array(),
                'commentInfo' => array(),
                'zanInfo' => array()
            );
        }
        return $recommendCookie;
    }
    //浏览信息或操作时设置用户画像cookie
    public function setRecommendCookie($data,$key){
        $recommendCookie = $this->recommendCookie($data,$key);
        $cookieInfo = $recommendCookie['cookieInfo'];
        cookie('recommend',$cookieInfo,C('COOKIE_CONFIG'));
        return $recommendCookie['isBrowse'];
    }
    //未登陆状况下获取cookie保存并过滤过期信息后的用户画像
	public function recommendCookie($data,$key){
        $recommendConfigModel = M('RecommendConfig');
        //获取推荐配置信息
        $calculateTimeSpan = $recommendConfigModel->where(array('state' => array('neq', '0')))->getField('calculate_time_span');
        $timestamp = time();
        $beginTimeStamp = $timestamp - 60 * 60 * 24 * (int)$calculateTimeSpan;


        $recommendCookie = $this->getRecommendCookie();
        $recommendCookieFilter = array(
            'browseInfo' => $this->infoFilter($recommendCookie['browseInfo'],$beginTimeStamp),
            'commentInfo' => $this->infoFilter($recommendCookie['commentInfo'],$beginTimeStamp),
            'zanInfo' => $this->infoFilter($recommendCookie['zanInfo'],$beginTimeStamp),
        );
        $sign = false;
        if ( $data  && $key ) {
            $pushArr = $recommendCookieFilter[$key];
            if ( $key == 'browseInfo') {
                foreach ( $pushArr as &$item) {
                    if( strtotime($item['date']) < strtotime($data['date']) && $data['news_id'] == $item['news_id'] ) {
                        $item['date'] = $data['date'];
                        $sign = true;
                    }
                }
                if ( $sign ) {
                    array_push($pushArr,$data);
                }
            } else {
                array_push($pushArr,$data);
            }
            $recommendCookieFilter[$key] = $pushArr;
        }
        return array(
            'cookieInfo' => $recommendCookieFilter,
            'isBrowse' => $sign
        );
    }

    public function getSimilarityUserKeyword($user_id){
        $similarityModel = M('Similarity');
        $followModel = M('Follow');
        $subQuery = $followModel->where(array('user_id'=>$user_id))->field('follow_id')->select(false);
        $condition['_string'] = "(user_id1 = $user_id and user_id2 in ($subQuery) )";
        $similarityResult = $similarityModel -> where($condition) -> order('similarity desc') -> find();
        $similarityUser = 0;
        $result = null;

        if ( $similarityResult ) {
            $similarityUser = $similarityResult['user_id2'];
        }
        if ( $similarityUser !== 0 ) {
            $similarityPortrayal = $this->getPortrayal($similarityUser);
            $portrayalInfo = $similarityPortrayal['data'];
            $portrayal = $this->portrayal($portrayalInfo);
            $recommendConfigModel = M('RecommendConfig');
            //获取推荐配置信息
            $recommendConfig = $recommendConfigModel->where(array('state' => array('neq', '0')))->find();
            $data = $this->getRecommendWeight($portrayal, $recommendConfig,null);
            $data = $this->multi_array_sort($data, 'weightScore');
            if ( $data ) {
                foreach ($data as $item) {
                    if( $item['type'] == 1) {
                        $result = $item;
                        break;
                    }
                }
            }
        }
        return $result;


    }

    //根据用户Id获取用户画像 并更新数据库
    public function getPortrayal($user_id){
        $recommendConfigModel = M('RecommendConfig');
        $recommendConfig = $recommendConfigModel->where(array('state' => array('neq', '0')))->find();
        $calculateTimeSpan = $recommendConfig['calculate_time_span'];
        $timestamp = time();
        $time = date('Y-m-d H:i:s',$timestamp);
        $beginTimeStamp = $timestamp - 60 * 60 * 24 * (int)$calculateTimeSpan;
        $beginTime = date('Y-m-d H:i:s',$beginTimeStamp);
        $portrayalModel = M('Portrayal');
        $portrayal = $portrayalModel->where(array('user_id'=>$user_id))->find();
        if( $portrayal ) {
            $portrayalInfo = json_decode($portrayal['portrayal'],true);
        } else {
            $portrayalInfo = array(
                'browseInfo' => array(),
                'commentInfo' => array(),
                'zanInfo' => array()
            );
        }

        if ( $portrayal && strtotime($portrayal['last_modify_time']) > $timestamp - 60 * 60 * 24 && count($portrayalInfo['browseInfo']) > 6 ) { //如果超过一天未更新用户画像,则重新计算画像
            $portrayalSave = array(
                'browseInfo' => $this->infoFilter($portrayalInfo['browseInfo'],$beginTime),
                'commentInfo' => $this->infoFilter($portrayalInfo['commentInfo'],$beginTime),
                'zanInfo' => $this->infoFilter($portrayalInfo['zanInfo'],$beginTime),
            );
            $portrayalResult = $portrayalModel->where(array('user_id'=>$user_id))->save(array(
                'portrayal' => json_encode($portrayalSave),
                'last_modify_time' => $time
            ));
        } else {
            $visitorModel = D('VisitorNews');
            $keywordBelongModel = D('NewsKeywordBelong');
            $zanModel = D('Zan');
            $commentModel = D('Comment');
            $browseList = $visitorModel->getVisitorListByUserIdAndBeginTime($user_id, $beginTime);
            $commentList = $commentModel->getCommentListByUserIdAndBeginTime($user_id, $beginTime);
            $zanList = $zanModel->getZanListByUserIdAndBeginTime($user_id, $beginTime);
            $portrayalSave = array(
                'browseInfo' => $browseList,
                'commentInfo' => $commentList,
                'zanInfo' => $zanList
            );

            foreach ($portrayalSave as $key => &$item) {
                foreach ($item as &$_item) {
                    $_item['keywords'] = $keywordBelongModel->getKeywordByNewsId($_item['news_id']);
                }
            }
            if ( $portrayal ) {
                $portrayalResult = $portrayalModel->where(array('user_id'=>$user_id))->save(array(
                    'portrayal' => json_encode($portrayalSave),
                    'last_modify_time' => $time
                ));
            } else {
                $portrayalResult = $portrayalModel->add(array(
                    'user_id' => $user_id,
                    'portrayal' => json_encode($portrayalSave),
                    'last_modify_time' => $time
                ));
            }
        }
        return array(
            'dataSaveResult' => $portrayalResult,
            'data' => $portrayalSave
        );


    }

    public function infoFilter($array,$time){
        $result = array();
        foreach ($array as $item) {
            $stamp = $item['time'] ? strtotime($item['time']) : strtotime($item['date']);
            if( $stamp >= $time ) {
                array_push($result,$item);
            }
        }
        return $result;
    }

}