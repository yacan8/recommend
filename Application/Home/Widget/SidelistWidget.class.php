<?php
namespace Home\Widget;
use Think\Controller;
class SidelistWidget extends Controller{
    public function side(){
        $NewsModel = D('News');

        $List = $NewsModel->getHotTop7();
        foreach ($List as &$result) {
            if($result['image'] == '' || $result['image']== null){
                $img = getNewsImg($result['content']);
                if( $img == '' || $img == null ) {
                    $result['image'] = '';
                }else{
                    $result['image'] = getNewsImg($result['content']);
                }
            }
            if($result['image'] !== '' ){
                $result['image'] = U('Image/img',array('w'=>80,'h'=>80,'image'=>urlencode($result['image']).'!feature'),false,false);
            }else{
                $result['image'] = __ROOT__.'/Public/img/链接.png';
            }
            unset($result['content']);
        }
        $this->assign('List',$List);
        $this->display('Widget:sidelist');
    }
}