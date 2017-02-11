<?php

namespace Home\Controller;
use Think\Controller;
//话题控制器
class TopicController extends Controller{

	public function index(){
		$p = I('get.p',1);
		$t = I('get.t',0);
		$showCount = 10;//每页显示个数
		$TypeModel = M('TopicType');
		$TypeList = $TypeModel->select();
		$TopicModel = D('Topic');
		$count = $TopicModel->getAllCount($t);
		if($count%$showCount==0)
			$TotalPage = intval($count/$showCount);
		else
			$TotalPage = intval($count/$showCount)+1;
		$List = $TopicModel ->getList($t,$p,$showCount);

		$TopicCommentModel = D('TopicComment');
		for ($i=0; $i < count($List); $i++) {//添加评论
			$List[$i]['comment_list'] = $TopicCommentModel->getByTopicId($List[$i]['t_id'],0,1,4);
		}


		$this->assign('TotalPage',$TotalPage);
		$this->assign('p',$p);//分页
		$this->assign('t',$t);
		$this->assign('TypeList',$TypeList);
		$this->assign('List',$List);
		$this->display();
	}

	public function detail(){
		$id = I('get.id');
		$p  = I('get.p',1);
		$sort_type = I('sort_type','time_up');
		$showCount = 10;//每页显示个数
		$TopicModel = D('Topic');
		$topic = $TopicModel->getById($id);
		$TopicModel->where(array('id'=>$id))->setInc('browse',1);
		$TopicCommentModel  = D('TopicComment');
		$CommentList = $TopicCommentModel->getByTopicId($id,0,$p,$showCount,$sort_type);
		$count = $TopicCommentModel->getAllCount($id,0);//总个数
		if($count%$showCount==0)
			$TotalPage = intval($count/$showCount);
		else
			$TotalPage = intval($count/$showCount)+1;
		$TopicCommentModel->getReplyByTopicIdAndCommentList($id,$CommentList);
		$TopicTypeModel = D('TopicType');
		$TypeList = $TopicTypeModel->getByTopicId($id);
		$this->assign('TotalPage',$TotalPage);
		$this->assign('p',$p);//分页
		$this->assign('sort_type',$sort_type);//排序类型
		$this->assign('TypeList',$TypeList);
		$this->assign('CommentList',$CommentList);
		$this->assign('topic',$topic);
		$this->display('detail');
	}



	public function addTopic(){
		if(session('?login')){
			$model = M('');
			$TopicModel = D('Topic');
			$result = $TopicModel->create();
			if($result){
				$TypeStr = I('post.topic_type');
				$ImgStr = I('post.ImgStr','');
				$TopicModel->user_id = session('login');
				$TopicModel->time = $TopicModel->update_time = date('Y-m-d H-i-s',time());
				$TopicModel->comment_count = 0;
				$TopicModel->zan_count = 0;
				$TopicModel->browse = 1;

				$model->startTrans();//开启事务
				$TopicResult = $TopicModel->add();
				$topic_id = $TopicModel->getLastInsID();
				$TypeArray = explode(',',$TypeStr);//话题类型ID数组
				$TypeData = array();
				for ($i=0; $i < count($TypeArray); $i++) { 
					$TypeData[] = array('topic_id'=>$topic_id,'type_id'=> $TypeArray[$i]);
				}
				$TopicTypeBelongModel = D('TopicTypeBelong');
				$TypeResult = $TopicTypeBelongModel->addAll($TypeData);
				if($ImgStr != ''){
					$ImgStr = substr($ImgStr,0,-1);
					$TopicPictrueModel = D('TopicPicture');
					$PictrueResult = $TopicPictrueModel->addDataByTypeAndImgStr($topic_id,1,$ImgStr);
				}
				if($TopicResult!=0 && $TypeResult!=0){
					if($ImgStr!= '' ){
						if($PictrueResult!=0){
							$message = '200';
							$model->commit();
						}
						else{
							$message = '发表失败';
							$model->rollback();
						}
					}else{
						$message = '200';
						$model->commit();
					}
				}else{
					$message = '发表失败';
					$model->rollback();
				}
				session('message',$message);
				$this->redirect('Topic/index');
			}else{
				session('message','发表失败');
				$this->redirect('Topic/index');
			}
		}else{
			$this->redirect(U('Login/index'));
			echo '先登录';
		}
		
		
	}

	public function addTopicUpload(){
		if(session('?login')){
			$Base64Img = I('post.img');
			if(strlen($Base64Img)>1400000){
				$json['Code'] = '199';
				$json['Message'] = '图片超出限制大小1M';
			}else{
				$result = base64_upload($Base64Img);
				if($result == 'service error'){
					$json['Code'] = '201';
					$json['Message'] = '上传错误';
				}else if($result == 'file error'){
					$json['Code'] = '202';
					$json['Message'] = '请选择图片';
				}else{
					$json['Code'] = '200';
					$json['ImgName'] = $result;
				}
			}
		}else{
			$json['Code'] = '205';
			$json['Message'] = '请先登录';
		}
		echo json_encode($json);
	}

	public function addData(){

		$title = array(
			'10个为撩妹节省金钱/时间的技巧 ',
			'获得看电影最优惠价的秘密 ',
			'如何在网上找到最好的赚钱生意 ',
			'撩汉的最好小工具 ',
			'你的价值观值那么多钱吗？',
			'获得更便宜的喝奶茶全攻略 ',
			'方便逃课的最好十个理由',
			'10块钱以下最好的吃饱方法',
			'不同寻常但可以实现的逃课方法',
			'在不增加消费的情况下推广你的产品5个方法',
			'21种最有用交流技巧',
			'发现你最好的一面',
			'计划一个完美的约会',
			'这是撩妹真正需要的 ',
			'你可以搞基的7种迹象 ',
			'现在开始我要减肥',
			'基佬是一个濒临灭绝的品种吗',
			'如何克服对宋仲基的恐惧',
			'10种常见诈骗以及如何避免它们',
			'你的长相有多安全？ ',
			'7 个最令人害怕的密集',
			'最吓人的10个恐怖电影',
			'蛮横的基佬以及它们如何影响你 ',
			'永远摆脱你的烦恼',
			'你的室友可能成为同性恋吗？ ',
			'你的表面并没有告诉你的气质',
			'提防有直男癌的人以及如何识别他们 ',
		);
		$content = array(
			'希望我的回答能帮到你哈，有问题可以追问，觉得好就点个赞，有用就采纳！',
			'写得很好',
			'这个评论写的好，平淡中显示出不凡的文学功底，可谓是字字珠玑，句句经典，是我辈应当学习之典范。',
			'头一次评论啊，好紧张啊，该怎么说啊，打多少字才显的有文采啊，这样说好不好啊，会不会成热门啊，我写的这么好会不会太招遥，写的这么深奥别人会不会看不懂啊，怎样才能写出飘逸潇洒的水平呢，半小时写了这么多会不会太快啊，好激动啊！',
			'我来了，既然来了我就得说几句！只说几句而已！如果我不说几句！',
			'看完您的评论后,我的心久久不能平静!这条评论构思新颖,题材独具匠心,段落清晰,情节诡异,跌宕起伏,主线分明,引人入胜,平淡中显示出不凡的文学功底,可谓是字字珠玑,句句经典,是我辈应学习之典范.就小说艺术的角度而言,可能不算太成功,但它的实验意义却远大于成功本身',
			'从文学的角度来讲，选材很是新颖，角度清晰可见，语言平实而不失风采，简洁而富有寓意，堪称现代评论之典范！',
			' 好文采，客亦知夫水与月呼？',
			'英语四六级能过么？生活规划有了么？大学是让你每天发上网么？',
			'你又在这里上网，工头到处找你，叫你赶紧回工地去，还有20吨水泥要卸，工头说再不回来你那10天300块钱的工钱一分都别想拿到',
			'看完了你发的评论，我关掉了手机，语重心长的吸了一根烟',
			'不花钱与女人发生关系，是一夜情。',
			'第一次回复，好紧张啊！有没有潜规则？用不用脱啊？该怎么说啊？打多少字才显的有文采啊？',
			'继续努力哈！',
			'加油加油！',
			'自己一个人照顾好自己，晚上盖好被子，早晨醒来不要猛的坐起来会头疼，不要吃凉的辣的对胃不好，过马路的时候左右看，好好吃饭，不要就穿那么点衣服，感冒了一定要吃药，不要熬太晚，睡觉时记得把手机的亮度调低些，不然有辐射，记得你是我的特别关心！',
			'都让开，我要秒赞， 老夫纵横动态圈多年，自以为再也不会有任何评论能打动我，没想到今天看到了如此精妙绝伦的这样一段评论。',
			'他魔剑在手。他权倾一方，他点草全服。他强打红字，他挥金如土。他装备牛逼，他瞎人狗眼。',
			'最喜欢的事就是拜访你的空间/玫瑰/拥抱/握手/爱心/可爱最开心的事就是看到你的留言/玫瑰/拥抱/握手/爱心/可爱最高兴的事就是给你送来祝福/玫瑰/拥抱/握手/爱心/可爱最愉快的事就是有你这位朋友/玫瑰/拥抱/握手/爱心无论现在还是以后祝福永远。',
			'我来了，既然来了我就得说几句！只说几句而已！如果我不说几句！就对不起人了，既然我要说几句！那么肯定是要说话的~所以我决定几句话也许能改变一切！也许我说的这几句话什么也不能改变！',
			'本评论由蓝瓶的钙就是好喝的钙；正宗好凉茶正宗好声音；娃哈哈启力喝、启力添动力；求完美、威锋剃须刀；你的益达是你的益达呦；绿茶配青梅还是头一回；风驰天下大运摩托。'
		);
		$m = M('TopicTypeBelong');
  		// $List = $m->select();
		$stringtime = date("Y-m-d H:i:s",time());  
		  
		echo $stringtime."<br/>";  
		  
		echo strtotime($stringtime)."<br/>";  
		  
		echo date("Y-m-d H:i:s",strtotime($stringtime));  
		for ($i=1; $i <= 30; $i++) { 
			
			$data['topic_id'] = rand(1,52);
			$data['type_id'] = rand(1,7);
			$count = $m->where($data)->count();
			// if($count==0)
				// $m->add($data);



			// $data['other_id'] = rand(1,52);
			// $day = rand(0,9);
			// $pic = rand(1,7);
			// $data['image'] = 'topic/2016-04-1'.$day.'/'.$pic.'jpg';
			// $data['type'] = 2; 
			// $data['sender'] = rand(1,15);
			// $month = rand(1,4);
			// if($month==4)
			// 	$day = rand(1,19);
			// else
			// 	$day = rand(1,29);
			// $data['time'] = date('Y-m-d H:i:s',mktime(rand(1,23),rand(1,59),rand(1,59),$month,$day,2016));
			// $data['content'] = $content[rand(0,20)];
			// $data['title'] = $title[rand(0,25)];
			// $data['comment_count'] = rand(0,20);
			// $data['zan_count'] = rand(0,30);
			// $data['browse'] = rand(1,300);
			// $data['update_time'] = $data['time'];
			// $m->where('id='.$i)->save($data);
		}
	}
}



