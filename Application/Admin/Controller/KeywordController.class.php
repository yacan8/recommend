<?php

namespace Admin\Controller;
use Think\Controller;

class KeywordController extends Controller{


	public function keywordCalculate($content){
		//去除html中的特殊字符
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


	public function keywordInByNewsId($news_id,$keyword_arr){
		$newsModel = M('News');
		$keywordModel = M('NewsKeyword');
		$keywordBelongModel = M('NewsKeywordBelong');
		$newsInfo = $newsModel->find($news_id);
		if ( count($keyword_arr) ) {
			$sign = true;
			foreach ( $keyword_arr as $item) {
				$keywordIsExist = $keywordModel->where(array('keyword'=>$item))->find();
				if( $keywordIsExist ){
					$keyword_id = $keywordIsExist['id'];
					$keywordResult = 1;
				}else{
					$keywordResult = $keywordModel->add(array('keyword'=>$item));
					$keyword_id = $keywordModel->getLastInsID();
				}
				$keywordBelongModelResult = $keywordBelongModel->add(array('news_id'=>$news_id,'keyword_id'=>$keyword_id));

				if ( $keywordResult === false || $keywordBelongModelResult === false ) {
					$sign = false;
				}
			}
			return $sign;
		} else {
			$keywordsInfo = $this->keywordCalculate($newsInfo['content']);
			if( $keywordsInfo[0]['keyword'] ){
				//第一个关键字
				$keywordIsExist1 = $keywordModel->where(array('keyword'=>$keywordsInfo[0]['keyword']))->find();
				if( $keywordIsExist1 ){
					$keyword_id1 = $keywordIsExist1['id'];
					$keywordResult1 = 1;
				}else{
					$keywordResult1 = $keywordModel->add(array('keyword'=>$keywordsInfo[0]['keyword']));
					$keyword_id1 = $keywordModel->getLastInsID();
				}
				$keywordBelongModelResult1 = $keywordBelongModel->add(array('news_id'=>$news_id,'keyword_id'=>$keyword_id1));
			}else{
				$keywordResult1 = false;
				$keywordBelongModelResult1 = false;
			}

			if( $keywordsInfo[1]['keyword'] ){
				//第二个关键字
				$keywordIsExist2 = $keywordModel->where(array('keyword'=>$keywordsInfo[1]['keyword']))->find();
				if( $keywordIsExist2 ){
					$keyword_id2 = $keywordIsExist2['id'];
					$keywordResult2 = 1;
				}else{
					$keywordResult2 = $keywordModel->add(array('keyword'=>$keywordsInfo[1]['keyword']));
					$keyword_id2 = $keywordModel->getLastInsID();
				}
				$keywordBelongModelResult2 = $keywordBelongModel->add(array('news_id'=>$news_id,'keyword_id'=>$keyword_id2));
			}else{
				$keywordResult2 = 1;
				$keywordBelongModelResult2 = 1;
			}

			if( $keywordResult1 === false || $keywordBelongModelResult1 === false || $keywordResult2 === false || $keywordBelongModelResult2 === false ) {
				return false;
			}else{
				return true;
			}
		}
	}

	//中文分词及获取关键字
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


	public function test(){
		header('Content-Type: text/html; charset=utf-8');
		$newsModel = M('News');
		// $str1 = '余弦值越接近1，就表明夹角越接近0度，也就是两个向量越相似，这就叫"余弦相似性"。所以，上面的句子A和句子B是很相似的，事实上它们的夹角大约为20.3度。';
		// $str2 = '每篇文章各取出若干个关键词（比如20个），合并成一个集合，计算每篇文章对于这个集合中的词的词频（为了避免文章长度的差异，可以使用相对词频）；';
		// $str1 =  trim(strip_tags($newsModel->where('id=3015')->getField('content')));
		// $str2 =  trim(strip_tags($newsModel->where('id=2045')->getField('content')));
		// print_r($str1);
		// echo "<br>";
		// print_r($str2);
		// $wordsarr1 = $this->getWords($str1);
		// $wordsarr2 = $this->getWords($str2);
		// $words1 = $wordsarr1['words'];
		// $words2 = $wordsarr2['words'];
		// dump($this->xsd($words1,$words2));
		$content = $newsModel->where('id=5308')->getField('content');
	    preg_match_all('/<img.*?src=\"(.*?\.(jpg|jpeg|png|bmp|svg|pcx){1}.*?)\".*?>/i',$content,$array);
	    dump($array);

	}


	public function xsd($words1,$words2){
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

}