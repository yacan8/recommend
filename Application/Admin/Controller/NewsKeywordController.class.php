<?php
namespace Admin\Controller;
use Think\Controller;
class NewsKeywordController extends Controller {
    public function loading(){
        $word = I('get.word');
        $result = M('NewsKeyword')->where(array('keyword'=>array('like','%'.$word.'%')))->field('id as id,keyword as name')->limit(10)->select();
        echo json_encode($result);
    }
}
