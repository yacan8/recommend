<?php

namespace Admin\Controller;
use Think\Controller;

class CrawlerController extends Controller{

	public function _initialize(){
		if (!isset($_SESSION['Adminlogin'])) {
			$this->redirect('Login/index');
		}
	}
	public function crawler(){
		$crawlerFromModel = M('CrawlerFrom');
		$time = $crawlerFromModel->order('last_time asc')->getField('last_time');

		$this->assign('lastTime',$time);
		$this->assign('title','新闻抓取');
		$this->display();
	}


	public function sinaNews(){
		$last_time = I('get.time','');
		$last_time = substr($last_time, 0, -3);
		$time = date('Y-m-d H:i:s',time());
		$crawlerFromModel = M('CrawlerFrom');
		$crawlerFromModel->where(array('display_name'=>'新浪'))->save(array('last_time'=>$time));
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://roll.news.sina.com.cn/interface/rollnews_ch_out_interface.php?col=89&spec=&type=&ch=01&k=&offset_page=0&offset_num=0&num=2000&asc=&page=1&last_time={$last_time}");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
		$output = curl_exec($ch);
		curl_close($ch);
		$output = mb_convert_encoding($output, 'utf-8', 'gbk');
		$this->ajaxReturn($output,'EVAL');
	}

	public function news163(){
		$ch = curl_init();
		$crawlerFromModel = M('CrawlerFrom');
		$time = date('Y-m-d H:i:s',time());

		$crawlerFromModel->where(array('display_name'=>'网易'))->save(array('last_time'=>$time));
		curl_setopt($ch, CURLOPT_URL, "http://news.163.com/special/0001220O/news_json.js");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
		$output = curl_exec($ch);
		curl_close($ch);
		$output = mb_convert_encoding($output, 'utf-8', 'gbk');
		$this->ajaxReturn($output,'EVAL');
	}

	//爬虫写入
	public function crawlerIn(){
		$crawlerModel = M('Crawler');
		$crawlerFromModel = M('CrawlerFrom');
		$from = I('post.from');
		$from_id = I('post.from_id');
		$from_site_id = $crawlerFromModel->where(array('display_name'=>$from))->getField('id');
		$is_crawler = $crawlerModel -> where(array('from_id'=>$from_id ,'form'=>$from_site_id))->find();
		if( $is_crawler ) {
			$json['success'] = false;
			$json['code'] = 300;
			$json['message'] = '内容已存在';
		}else{
			$model = M('');
			$model->startTrans(); //开启事务

			$url = I('post.url');
			$time = I('post.time');
			$type = I('post.type');




			vendor('phpquery.phpQuery.phpQuery');
			$urlInfo = parse_url($url);
			$document = \phpQuery::newDocumentFile($url);


			//站点结构  储存标题与文章内容dom
			$structures = array(
					'news.sina.com.cn' => '#artibodyTitle,#artibody',
					'sports.sina.com.cn' => '#j_title,#artibody',
					'finance.sina.com.cn' => '#artibodyTitle,#artibody',
					'tech.sina.com.cn' => '#main_title,#artibody',
					'ent.sina.com.cn' => '#main_title,#artibody',
					'news.163.com' => '#epContentLeft>h1:first,#endText',
					'money.163.com' => '#epContentLeft>h1:first,#endText',
					'mil.news.sina.com.cn' => '#main_title,#artibody',
			);

			$remove = array(  //需要过滤的dom节点
					'.caijing_bq',
					'style',
					'.special_tag_wrap',
					'pre',
					'.ep-source',
					'.article-editor',
					'.show_author',
					'script',
					'.ct_hqimg',
					'.fin_reference',
					'.finance_app_zqtg',
					'link',
					'#j_album_1',
					'#blk_weiboBox_01'
			);
			$structure = $urlInfo['host'];
			$structureArr = explode(',',$structures[$structure]);
			$document->find($structureArr[1])->find(join(',',$remove))->remove(); //过滤一些广告信息
			$title = $document->find($structureArr[0])->text();//标题
			$content = $document->find($structureArr[1])->html();//内容
			if ($structure == 'news.163.com' || $structure == 'money.163.com') { //解决网易抓取的中文乱码问题
				$title = mb_convert_encoding($title, 'ISO-8859-1', 'utf-8');
				$title = mb_convert_encoding($title, 'utf-8', 'GBK');
				$content = mb_convert_encoding($content, 'ISO-8859-1', 'utf-8');
				$content = mb_convert_encoding($content, 'utf-8', 'GBK');
			}
			if ($structure == 'tech.sina.com.cn' && !$title) {
				$title = $document->find('#artibodyTitle')->text();
			}
			if( $title != '' || trim($content) != '' || trim($content) != null) {

				$user_id = session('Adminlogin');
				//相似度计算
				// *** code

				$typeModel = M('Type');
				if( trim($type) != '' ){
					$type_id = $typeModel->where(array('type' => $type))->getField('id');
					if (!$type_id) {
						$typeResult = $typeModel->add(array('type' => $type));
						$type_id = $typeModel->getLastInsID();
					} else {
						$typeResult = 1;
					}
				}else{
					$type_id = 0;
					$typeResult = false;
				}

				//写入

				$crawlerData = array(
						'from_id' => $from_id,
						'from' => $from_site_id,
						'url' => $url,
						'time' => $time,
						'title' => $title
				);

				$crawlerResult = $crawlerModel->add($crawlerData);


				$newsModel = M('News');
				$content = $this->saveImageByContent($content);
				$newsData['title'] = $title;
				$newsData['content'] = $content;
				$newsData['contributor'] = $user_id;
				$newsData['title'] = $title;
				$newsData['type'] = $type_id;
				$newsData['publish_time'] = $time;

				$newsResult = $newsModel->add($newsData);
				$news_id = $newsModel->getLastInsId();

				$dynamicsModel = M('Dynamics');
				$dynamicsData['content_id'] = $news_id;
				$dynamicsData['user_id'] = $user_id;
				$dynamicsData['time'] = $time;
				$dynamicsData['type'] = 4;

				$dynamicsResult = $dynamicsModel->add($dynamicsData);
                $keywordStructures = array(
                    'news.sina.com.cn' => '.article-keywords',
                    'sports.sina.com.cn' => '.article-a_keywords',
                    'finance.sina.com.cn' => '.article-keywords',
                    'tech.sina.com.cn' => '.art_keywords',
                    'ent.sina.com.cn' => '.art_keywords',
                    'news.163.com' => '',
                    'money.163.com' => '',
                    'mil.news.sina.com.cn' => '.art_keywords',
                );
				$keywordsArr = array();
                if ( $keywordStructures[$structure] ) {
                    $keywords = $document->find($keywordStructures[$structure])->find('a');
                    foreach( $keywords as $articleList ) {
                        array_push($keywordsArr,pq($articleList)->text());
                    }
                }


				$keywordController = A('Keyword');
				$KeywordResult = $keywordController->keywordInByNewsId($news_id,$keywordsArr);



				if ($KeywordResult === false || $newsResult === false || $dynamicsResult === false || $crawlerResult === false || $typeResult === false) {
					$json['success'] = false;
					$json['code'] = 500;
					$json['message'] = '写入失败';
					$model->rollback();
				} else {
					$json['success'] = true;
					$json['code'] = 200;
					$json['message'] = '写入成功';
					$model->commit();
				}
			}else{
				$json['success'] = false;
				$json['code'] = 500;
				$json['message'] = '爬取失败';
				$model->rollback();
			}


		}
		$this->ajaxReturn($json);
	}

	public function saveImageByContent($content){
		preg_match_all('/<img.*?src="(.*?)".*?>/is',$content,$array);
		$date = date('Y-m-d');
		if( $array[1] ) {
			foreach ($array[1] as $src) {
				$result = getImage($src,'./Data/news/'.$date);
				if( $result['error'] == 0){
					$content = str_replace($src, __ROOT__."/Data/news/".$date.'/'.$result['file_name'], $content);
				}
			}
		}
		return $content;
	}
	public function test(){
		header("Content-type: text/html; charset=utf-8");
		$url = 'http://ent.sina.com.cn/m/c/2017-03-30/doc-ifycwunr8146094.shtml';
		vendor('phpquery.phpQuery.phpQuery');
		$urlInfo = parse_url($url);
		$document = \phpQuery::newDocumentFile($url);


		//站点结构  储存标题与文章内容dom
		$structures = array(
				'news.sina.com.cn' => '#artibodyTitle,#artibody',
				'sports.sina.com.cn' => '#j_title,#artibody',
				'finance.sina.com.cn' => '#artibodyTitle,#artibody',
				'tech.sina.com.cn' => '#main_title,#artibody',
				'ent.sina.com.cn' => '#main_title,#artibody',
				'news.163.com' => '#epContentLeft>h1:first,#endText',
				'money.163.com' => '#epContentLeft>h1:first,#endText',
				'mil.news.sina.com.cn' => '#main_title,#artibody',
		);

		$remove = array(  //需要过滤的dom节点
            '.caijing_bq',
            'style',
            '.special_tag_wrap',
            'pre',
            '.ep-source',
            '.article-editor',
            '.show_author',
            'script',
            '.ct_hqimg',
            '.fin_reference',
            '.finance_app_zqtg',
            'link',
            '#j_album_1',
            '#blk_weiboBox_01',
            '.xb_new_finance_app'
		);
        $keywordStructures = array(
            'news.sina.com.cn' => '.article-keywords',
            'sports.sina.com.cn' => '.article-a_keywords',
            'finance.sina.com.cn' => '.article-keywords',
            'tech.sina.com.cn' => '.art_keywords',
            'ent.sina.com.cn' => '.art_keywords',
            'news.163.com' => '',
            'money.163.com' => '',
            'mil.news.sina.com.cn' => '.art_keywords',
        );

		$structure = $urlInfo['host'];
		$structureArr = explode(',',$structures[$structure]);
		$document->find($structureArr[1])->find(join(',',$remove))->remove(); //过滤一些广告信息
		$title = $document->find($structureArr[0])->text();//标题
		$content = $document->find($structureArr[1])->html();//内容
		if ($structure == 'news.163.com' || $structure == 'money.163.com') { //解决网易抓取的中文乱码问题
			$title = mb_convert_encoding($title, 'ISO-8859-1', 'utf-8');
			$title = mb_convert_encoding($title, 'utf-8', 'GBK');
			$content = mb_convert_encoding($content, 'ISO-8859-1', 'utf-8');
			$content = mb_convert_encoding($content, 'utf-8', 'GBK');
		}
		if ($structure == 'tech.sina.com.cn' && !$title) {
			$title = $document->find('#artibodyTitle')->text();
		}

		$keywordsArr = array();
		if ( $keywordStructures[$structure] ) {
            $keywords = $document->find($keywordStructures[$structure])->find('a');
            foreach( $keywords as $articleList ) {
                array_push($keywordsArr,pq($articleList)->text());
            }
        }

        dump($keywordsArr);

	}




}
