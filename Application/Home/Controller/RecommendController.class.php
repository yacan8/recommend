<?php
namespace Home\Controller;
use Think\Controller;
//推荐引擎
class RecommendController extends Controller
{


    public function recommend($user_id)
    {

        header("Content-type: text/html; charset=utf-8");
        $recommendConfigModel = M('RecommendConfig');
        //获取推荐配置信息
        $recommendConfig = $recommendConfigModel->where(array('state' => array('neq', '0')))->find();

        $calculateTimeSpan = $recommendConfig['calculate_time_span'];
        $timestamp = time();
        $calculateTimeSpanStamp = $timestamp - 60 * 60 * 24 * (int)$calculateTimeSpan;
        $calculateTimeSpanBeginTime = date('Y-m-d H:i:s', $calculateTimeSpanStamp);


        $isLogin = session('?login');
        $user_id = session('login');
        $recommendModel = M('Recommend');
        $visitorModel = D('VisitorNews');
        $keywordBelongModel = D('NewsKeywordBelong');
        $zanModel = D('Zan');
        $commentModel = D('Comment');
        if ($isLogin) {

            $browseList = $visitorModel->getVisitorListByUserIdAndBeginTime($user_id, $calculateTimeSpanBeginTime);
            $commentList = $commentModel->getCommentListByUserIdAndBeginTime($user_id, $calculateTimeSpanBeginTime);
            $zanList = $zanModel->getZanListByUserIdAndBeginTime($user_id, $calculateTimeSpanBeginTime);

            $portrayalInfo = array(
                'browseInfo' => $browseList,
                'commentInfo' => $commentList,
                'zanInfo' => $zanList
            );
            dump($portrayalInfo);
            $recommendNum = count($browseList) > 10 ? 10 : count($browseList);

            foreach ($portrayalInfo as $key => &$item) {
                foreach ($item as &$_item) {
                    $_item['keywords'] = $keywordBelongModel->getKeywordByNewsId($_item['news_id']);
                }
            }
            $portrayal = $this->portrayal($portrayalInfo);
            $data = $this->getRecommendWeight($portrayal, $recommendConfig);

            $read = $recommendModel->where(array('user_id' => $user_id))->field('id')->select(false);
        } else {
            $cookieRecommendedString = cookie('recommend');
            if ($cookieRecommendedString) {
                $portrayalInfo = json_decode($cookieRecommendedString);
            } else {
                $portrayalInfo = array();
            }
            $recommendNum = count($portrayalInfo['browseInfo']) > 10 ? 10 : count($portrayalInfo['browseInfo']);
            $browseList = $portrayalInfo['browseInfo'];
            $browseIdArray = array();
            foreach ($browseList as $item) {
                array_push($browseIdArray, $item['news_id']);
            }
            $read = join(',', $browseIdArray);
            $portrayal = $this->portrayal($portrayalInfo);

            $data = $this->getRecommendWeight($portrayal, $recommendConfig);
        }


        $recommendArray = $this->getRecommendNum($data, $recommendNum);

        $recommendList = $this->getRecommendData($read, $recommendArray, $recommendConfig, $recommendNum);
        dump($recommendList);
    }

    public function getRecommendData($read, $recommendArray, $recommendConfig, $recommendNum = 10)
    {

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


    public function getRecommendNum($data, $recommendNum)
    {
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

    public function getRecommendWeight($portrayal, $recommendConfig)
    {
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

        $data = array_merge($keywordData, $typeData);
        $typeCount = count($typeArray);
        foreach ($data as $key => &$item) {
            if ($item['type'] == 1) {
                $item['weightScore'] = $item['weight'] * $typeCount / $weightSum;
            } else {
                $item['weightScore'] = $item['weight'] / $weightSum;
            }
        }
        $data = $this->multi_array_sort($data, 'weightScore');
        return $data;


    }

    //根据键值排序
    function multi_array_sort($arr, $key, $type = SORT_REGULAR, $short = SORT_DESC)
    {
        foreach ($arr as $k => $v) {
            $name[$k] = $v[$key];
        }
        array_multisort($name, $type, $short, $arr);
        return $arr;
    }


    //用户画像
    public function portrayal($portrayalInfo)
    {
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

	public function test(){
	    $beginTime = '2017-03-15 13:39:19';
        $data = array(
            'news_id' => '2301',
            'date' => '2017-03-15 00:00:00',
            'type' => '2'
        );
        $cookie = $this->recommendCookie($beginTime,$data,'commentInfo');
        dump($cookie);
    }
	public function recommendCookie($beginTime,$data,$key){
        $beginTimeStamp = strtotime($beginTime);
	    $recommendCookie = json_decode(cookie('recommend'),true);

        if( !$recommendCookie ) {
            $recommendCookie = array(
                'browseInfo' => array(),
                'commentInfo' => array(),
                'zanInfo' => array()
            );
        }
//        dump($recommendCookie);

        $recommendCookieFilter = array(
            'browseInfo' => $this->infoFilter($recommendCookie['browseInfo'],$beginTimeStamp),
            'commentInfo' => $this->infoFilter($recommendCookie['commentInfo'],$beginTimeStamp),
            'zanInfo' => $this->infoFilter($recommendCookie['zanInfo'],$beginTimeStamp),
        );
        if ( $data  && $key ) {
            $pushArr = $recommendCookieFilter[$key];
            if ( $key == 'browseInfo') {
                $sign = false;
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

        return $recommendCookieFilter;

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