<?php
namespace Home\Controller;
use Think\Controller;
class ImageController extends Controller {


    public function img(){
        $img = I('get.image');
        if($img !=''){
            $img = urldecode($img);
            $h = I('get.h',100);
            $w = I('get.w',100);
            $imgNameArray = explode('!',$img);
            $image = new \Think\Image(\Think\Image::IMAGE_GD);
            $image->open('Data/'.$imgNameArray[0]);
            $img = $image->thumb($w, $h,\Think\Image::IMAGE_THUMB_CENTER)->getImg();
            $i = $img->getImg();
            header ('Content-Type:'.$img->mime());
            imagejpeg ( $i );
        }
    }



    
}