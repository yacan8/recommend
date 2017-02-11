<?php
namespace Home\Controller;
use Think\Controller;
class ImageController extends Controller {

    public function img(){
        $image = I('get.image');
        if($image!=''){
            $h = I('get.h',150);
            $w = I('get.w',150);
            $t = I('get.t',0);
            $imgNameArray = explode('!',$image);
            $image = new \Think\Image(\Think\Image::IMAGE_GD); 
            if($t == 0)
                $image->open('Data/news/'.$imgNameArray[0]);
            else
                $image->open('Data/news_thumb/'.$imgNameArray[0]);
            $img = $image->thumb($w, $h,\Think\Image::IMAGE_THUMB_CENTER)->getImg();
            $i = $img->getImg();
            header ('Content-Type:'.$img->mime());
            imagejpeg ( $i );
        }
    }

    public function TImg(){
        $img = I('get.image');
        if($img!=''){
            $img = urldecode($img);
            $imgNameArray = explode('!',$img);
            $image = new \Think\Image(\Think\Image::IMAGE_GD); 
            $image->open('Data/'.$imgNameArray[0]);
            $img1 = $image->thumb(100, 100,\Think\Image::IMAGE_THUMB_CENTER)->getImg();
            $i = $img1->getImg();
            header ('Content-Type:'.$img1->mime());
            imagejpeg ( $i );
        }
    }



    
}