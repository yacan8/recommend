<?php
namespace Admin\Controller;
use Think\Controller;

class SimilarityController extends Controller{


    public function newsSimilarity(){
        $this->assign('title','新闻相似度可视化计算');
        $this->display();
    }


    public function userSimilarity(){
        $portrayalModel = D('Portrayal');
        $key = I('get.key','');

        if( $key ) {
            $list = $portrayalModel->search($key);
        } else {
            $p = I('get.p',1);
            $showCount = $count = 10;
            $list = $portrayalModel->getList($p,$count);
            $Page       = new \Think\Page($count,$showCount);// 实例化分页类 传入总记录数和每页显示的记录数
            $show       = $Page->show();// 分页显示输出
            $this->assign('page',$show);
        }
        $this->assign('list',$list);
        $this->assign('title','用户相似度可视化查询');
        $this->display();
    }
    public function userSimilarityDetail(){
        $id = I('get.id',0);

        if( $id != 0  && is_numeric($id)) {



            $this->assign('user_id',$id);
            $this->assign('title','用户相似度可视化展示');
            $this->display();
        } else {
            exit('参数错误');
        }

    }
    public function userSimilarityLoad(){
        $id = I('get.id',0);
        header('Content-type:text/html;charset=utf-8');
        if( $id != 0  && is_numeric($id)) {
            $similarityModel = D('Similarity');
            $similarityController = A('Home/Recommend');
            $portrayalModel = D('Portrayal');
            $userPortrayalInfo = $portrayalModel->getByUserId($id);
            $recommendConfigModel = M('RecommendConfig');
            $recommendConfig = $recommendConfigModel->where(array('state' => array('neq', '0')))->find();
            $keywordArray = array();
            $typeArray = array();
            $similarityList = $similarityModel->getList($id,3);
            $similarityList = array_merge(array($userPortrayalInfo),$similarityList);
            foreach($similarityList as &$item){
                $item['portrayal'] = $similarityController->getRecommendWeight($similarityController -> portrayal(json_decode($item['portrayal'],true)),$recommendConfig,null);
                foreach ( $item['portrayal'] as &$_item ) {
                    if($_item['type'] == 1){
                        if(!in_array($_item['id'],$keywordArray)){
                            array_push($keywordArray,$_item['id']);
                        }
                    } else {
                        if(!in_array($_item['id'],$typeArray)){
                            array_push($typeArray,$_item['id']);
                        }
                    }
                }
            }
            $keywordModel = M('NewsKeyword');
            $keywordList = $keywordModel -> where(array('id'=>array('in',join(',',$keywordArray))))->select();
            $typeModel = M('Type');
            $typeList = $typeModel->where(array('id'=>array('in',join(',',$typeArray))))->select();
            $keywordMap = array();
            $typeMap = array();
            foreach ($keywordList as $keyword) {
                $keywordMap[$keyword['id']] = $keyword['keyword'];
            }
            foreach ($typeList as $type) {
                $typeMap[$type['id']] = $type['type'];
            }

            foreach($similarityList as &$item){
                foreach ( $item['portrayal'] as &$_item ) {
                    if($_item['type'] == 1){
                        $_item['keyword'] = $keywordMap[$_item['id']];
                    } else {
                        $_item['type_text'] = $typeMap[$_item['id']];
                    }
                }
            }
            $json = array(
                'success' => true,
                'code' => 200,
                'message' => '请求成功',
                'attr' => $similarityList
            );



        } else {
            $json = array(
                'success' => false,
                'code' => 400,
                'message' => '参数错误'
            );
        }
        $this->ajaxReturn($json);
    }
}