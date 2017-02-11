<?php
namespace Home\Widget;
use Think\Controller;
class SidelistWidget extends Controller{
    public function side(){
        $NewsModel = D('News');
        $List = $NewsModel->getHotTop7();
        $this->assign('List',$List);
        $this->display('Widget:sidelist');
    }
}