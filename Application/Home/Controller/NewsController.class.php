<?php
namespace Home\Controller;
use Think\Controller;
class NewsController extends Controller {
	public function detail(){
		if(isset($_GET['app_nav'])){
			session('app_nav','no');
		}
		$Crawler = I('get.crawler','');//爬虫
		$NewsModel = D('News');
		$id = I('get.id');
		//浏览+1
		$NewsModel->where(array('id'=>$id)) ->setInc('browse',1);
		$List = $NewsModel->getById($id);
		if($List==null)
			$this->error("该文章不存在");
		$commentModel = D('Comment');
		$keywordBelongModel = D('NewsKeywordBelong');
		$keywords = $keywordBelongModel->getKeywordByNewsId($id);
		$commentList =  $commentModel->getList($id,0,1,5,'newest');
		$comment_count =  $commentModel->getCommentCount($id);
		$PreAndNextTitle = $NewsModel->getTitlePreAndNext($id);//获取上一篇和下一篇标题
		$this->assign('commentList',$commentList);
		$this->assign('PreAndNextTitle',$PreAndNextTitle);
		$this->assign('comment_count',$comment_count);
		$this->assign('Crawler',$Crawler);
		$this->assign('keywords',$keywords);
		$this->assign('id',$id);
		$this->assign('List',$List);
		if($Crawler=='')
			$this->display('detail');
		else
			$this->display('crawler');
	}


	public function search(){
		$key = I('get.key');
		$page = I('get.page',1);
		$NewsModel = D('News');
		$List = $NewsModel->search($key,$page);
		$this ->assign('List',$List);
		$this ->assign('keyword',$key);
		$this->display();
	}


	public function search_load(){
		$key = I('get.key');
		$page = I('get.page',1);
		$NewsModel = D('News');
		$List = $NewsModel->search($key,$page);
		$this->ajaxReturn($List);
		// echo $key."+".$page;
	}
	/**
	 * [load ajax加载新闻]
	 * @return [json] [查询到的内容]
	 */
	public function load(){
	    try{
            $Model = D('News');
            $type = I('get.type',0);
            $page = I('get.page',1);
            $count = 10;
            $result = $Model->getSelectType($type,$page,$count);
            $json = array(
                'success' => true,
                'code' => 200,
                'message' => '加载成功',
                'attr' => $result
            );
        } catch (Exception $e) {
            $json = array(
                'success' => true,
                'code' => 500,
                'message' => '服务器内部错误',
                'attr' => array()
            );
        }
		$this->ajaxReturn($json);
	}
	/**
	 * [comment_load ajax加载评价内容]
	 * @return [json] [查询到的内容]
	 */
	// public function comment_load(){
	// 	$Model = D('Message');
	// 	$id = I('get.id');
	// 	$page = I('get.page');
	// 	$json = $Model ->loading($id,$page);
	// 	echo json_encode($json);
	// }

	/**
	 * [comment 提交评价]
	 * @return [string] [成功返回1,没有登录返回2，失败返回失败原因]
	 */
	// public function comment(){
	// 	if(!isset($_SESSION['login'])){
	// 		echo '2';
	// 	}else{
	// 		$receiver = I('post.receiver');
	// 		if($receiver == $_SESSION['login']){
	// 			echo "3";
	// 		}else{
	// 			$MessageModel = D('Message');
	// 			$id = I('post.other_id');
	// 			$content = I('post.content');
	// 			$data['other_id'] = $id;
	// 			$data['receiver'] = $receiver;
	// 			$data['content'] = $content;
	// 			$data['sender'] = session('login');
	// 			$data['time'] = date('y-m-d H:i:s',time());
	// 			$data['state'] = '1';
	// 			$result = $MessageModel->addMessage($data);
	// 			if($result!=0)
	// 				echo "1";
	// 			else
	// 				echo "评论失败";
	// 		}
	// 	}
	// }
	/**
	 * [attention 收藏]
	 * @return [type] [未登录返回2，成功返回1，否则返回收藏失败]
	 */
	public function attention(){
		if(!isset($_SESSION['login'])){
			echo '2';
		}else{
			$CollectionModel = D('Collection');
			$data['collected'] = I('post.collected');
			$data['collecting'] = session('login');
			if($CollectionModel->CheckIsCollected($data['collected'],$data['collecting'])){
				echo '3';
			}else{
				$result = $CollectionModel->add($data);
				if($result!=0)
					echo "1";
				else
					echo "收藏失败";
			}

		}
	}


	public function comment(){
        $content = array(
                '希望我的回答能帮到你哈，有问题可以追问，觉得好就点个赞，有用就采纳！',
                '还不错吧，但是既然已经没什么印象，大概属于那种不去会心痒痒，但是去了其实也就那样的地方。',
                '这个说说写的好，平淡中显示出不凡的文学功底，可谓是字字珠玑，句句经典，是我辈应当学习之典范。',
                '头一次评论啊，好紧张啊，该怎么说啊，打多少字才显的有文采啊，这样说好不好啊，会不会成热门啊，我写的这么好会不会太招遥，写的这么深奥别人会不会看不懂啊，怎样才能写出飘逸潇洒的水平呢，半小时写了这么多会不会太快啊，好激动啊！',
                '我来了，既然来了我就得说几句！只说几句而已！如果我不说几句！',
                '看完您的说说后,我的心久久不能平静!这条说说构思新颖,题材独具匠心,段落清晰,情节诡异,跌宕起伏,主线分明,引人入胜,平淡中显示出不凡的文学功底,可谓是字字珠玑,句句经典,是我辈应学习之典范.就小说艺术的角度而言,可能不算太成功,但它的实验意义却远大于成功本身',
                '从文学的角度来讲，选材很是新颖，角度清晰可见，语言平实而不失风采，简洁而富有寓意，堪称现代说说之典范！',
                '可能是因为去的那天下雨又是晚上，路上没什么人，个人感觉不是很有趣。3月份去的，风好~~~~大~~~~。想要去那边拍照的MM们，一定要照顾好自己的头发，我在那拍的照片，张张惨不忍睹。灯光很暗。明星们的手印和名字都要很仔细看。我是“宫崎骏主题店”--维多利亚港湾---钟楼---星光大道，这样的路线的。都在一条道上，顺带游玩的心情的话，就还好。',
                '英语四六级能过么？生活规划有了么？大学是让你每天发说说么？',
                '你又在这里发说说，工头到处找你，叫你赶紧回工地去，还有20吨水泥要卸，工头说再不回来你那10天300块钱的工钱一分都别想拿到',
                '我觉得没有想象中的好玩，就为了看看明星的手掌印逛了一逛~很干净，无聊了还可以去星巴克坐一坐，或者看看周边有什么活动',
                '对它的期望很大，不过去了3次，感觉都一般，无论是白天，还是夜晚，除了维港的水比较干净，景色还是魔都的赞。',
                '第一次回复，好紧张啊！有没有潜规则？用不用脱啊？该怎么说啊？打多少字才显的有文采啊？',
                'xx啊，你上次托我帮你问的事情我已经问过了，不能勃起可能是因为手淫过频而引起的，手淫过频容易导致前列腺发炎，可能会引起不孕不育，严重者甚至会阳痿早泄，之前私信给你你没回，所以直接回复了。 ',
                '传说中到此一游的景点~ 可以去看看~ 吹吹风看看手掌散漫地走走停停 时常有展览和各种活动 还是不错滴~',
                '夜景很美，大道上有明星的手印，晚上有灯光表演。很多人慕名而来。',
                '周围的氛围不如洛杉矶有感觉，印有手掌和签名的石材颜色太暗，显不出巨星的星光。当时去的时候还有一个维修的挡板挡住了一个明星的手印，感觉政府对这景点也不是很重视。'
        );
        $m = M('Comment');
		// $userArr = array(1,6,7,9,10);
		// $NewsList = M('News')->field('id')->select();
		// $newsId = array();
		// foreach ($NewsList as $value) {
		// 	array_push($newsId,$value['id']);
		// }
		dump($m->where("id=15")->find());
        for ($i=1; $i <= 200; $i++) {
            $data['news_id'] = $newsId[rand(0,count($newsId)-1)];
            $data['user_id'] = $userArr[rand(0,4)];
            $data['time'] = date('Y-m-d H:i:s',mktime(rand(1,23),rand(1,59),rand(1,59),rand(2,3),rand(1,29),2016));
            $data['content'] = $content[rand(0,16)];
            $data['delete_tag'] = (bool)0;
			$data['reply'] = 0;
            // dump($m->add($data));
            // dump($m->where('id='.$i)->save($data));
        }
    }

}
