<?php

namespace Admin\Controller;
use Think\Controller;

class KeywordController extends Controller{


	public function keywordCalculate($content){
		$content = str_replace('&nbsp;', '', $content);
		$content = str_replace('&rdquo;', '', $content);
		$content = str_replace('&ldquo;', '', $content);
		$content = str_replace('&hellip;', '', $content);
		$content = str_replace('&mdash;', '', $content);
		$content = str_replace('&middot;', '', $content);
		$wordsAndKeyword = $this->getWords(trim(strip_tags($content)));

		$wordLibrarlyModel = M('WordLibrary');
		$newsCount = M('News')->count(); //文档总数
		$words = $wordsAndKeyword['words'];
		$keywordsStr = $wordsAndKeyword['keywords'];
		$keywords = explode(',',$keywordsStr);
		$keywordsObject = array(); //用于保存出现该词的文档数
		foreach($words as $word => $num){ //写入词料库
			$wordInfo = $wordLibrarlyModel -> where(array('word'=>$word)) -> find();
			if( $wordInfo ) {
				$wordInfo['num'] = (int)$wordInfo['num']+1;
				if( in_array( $word , $keywords)) { //保存出现该关键字的文档次数
					$keywordsObject[$word] = $wordInfo['num'];
				}
				$wordResult = $wordLibrarlyModel -> where(array('id'=> $wordInfo['id'])) -> save($wordInfo);
			}else{
				if( in_array( $word , $keywords ) ) {
					$keywordsObject[$word] = 1;
				}
				$wordResult = $wordLibrarlyModel -> add(array('word'=>$word,'num'=> 1));
			}
		}

		$wordsCount = count($words); //文章词个数
		$finallyKeywordsInfo = array();
		$i = 0;
		foreach( $keywords  as  $keyword ){
			$wordFrequency = $words[$keyword]/$wordsCount; //词频
			$reverseDocumentFrequency =  log((int)$newsCount)/($keywordsObject[$keyword]+1); //逆文档频率
			$TF_IDF = $wordFrequency * $reverseDocumentFrequency; //tf-idf
			$finallyKeywordsInfo[$i]['TF_IDF'] = $TF_IDF;
			$finallyKeywordsInfo[$i]['keyword'] = $keyword;
			$i++;
		}
		for( $i = 0 ; $i < count($finallyKeywordsInfo); $i++ ) {
			for ($j = $i+1 ; $j < count($finallyKeywordsInfo) ; $j++ ) {
				if( $finallyKeywordsInfo[$j]['TF_IDF'] <= $finallyKeywordsInfo[$j+1]['TF_IDF']) {
					$a = $finallyKeywordsInfo[$j];
					$finallyKeywordsInfo[$j] = $finallyKeywordsInfo[$j+1];
					$finallyKeywordsInfo[$j+1] = $a;
				}
			}
		}
		// dump($finallyKeywordsInfo[0]['keyword']);
		return $finallyKeywordsInfo;
	}


	public function keywordInByNewsId($news_id){
		$newsModel = M('News');
		$keywordModel = M('NewsKeyword');
		$keywordBelongModel = M('NewsKeywordBelong');
		$newsInfo = $newsModel->find($news_id);
		$keywordsInfo = $this->keywordCalculate($newsInfo['content']);
		$kewwordIsExist = $keywordModel->where(array('keyword'=>$keywordsInfo[0]['keyword']))->find();
		if( $kewwordIsExist ){
			$keyword_id = $kewwordIsExist['id'];
			$keywordResult = 1;
		}else{
			$keywordResult = $keywordModel->add(array('keyword'=>$keywordsInfo[0]['keyword']));
			$keyword_id = $keywordModel->getLastInsID();
		}
		$keywordBelongModelResult = $keywordBelongModel->add(array('news_id'=>$news_id,'keyword_id'=>$keyword_id));
		// print_r($newsInfo['title']);
		if( $keywordResult === false || $keywordBelongModelResult === false){
			return false;
		}else{
			return true;
		}
	}

	//中文分词及获取关键字
	public function getWords($content){
		vendor('phpanalysis.phpanalysis');
		// 严格开发模式
		ini_set('display_errors', 'On');
		ini_set('memory_limit', '64M');
		error_reporting(E_ALL);
	    // echo $str;
	    //岐义处理
	    $do_fork =  true ;
	    //新词识别
	    $do_unit = true ;
	    //多元切分
	    $do_multi = true ;
	    //词性标注
	    $do_prop =  false ;
	    //是否预载全部词条
	    $pri_dict = true ;
	    $tall = microtime(true);
	    //初始化类
	    \PhpAnalysis::$loadInit = false;
	    $pa = new \PhpAnalysis('utf-8', 'utf-8', $pri_dict);
	    //载入词典
	    $pa->LoadDict();

	    //执行分词
	    $pa->SetSource($content);
	    $pa->differMax = $do_multi;
	    $pa->unitWord = $do_unit;
	    $pa->StartAnalysis( $do_fork );
	    $result = $pa->GetFinallyIndex();
	    $keywords = $pa->GetFinallyKeywords();
	    return array(
	    	'words' => $result,
	    	'keywords' => $keywords
	    );
	}

}