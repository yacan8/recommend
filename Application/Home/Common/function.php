<?php
/**
 * [base64_upload base64编码上传图片(话题发表图片上传，缺少自定义路径)]
 * @param  [String] $base64 [base64编码]
 * @return [String]         [成功返回图片路径，失败返回失败原因]
 */
function base64_upload($base64) {
    $base64_image = str_replace(' ', '+', $base64);
    //post的数据里面，加号会被替换为空格，需要重新替换回来，如果不是post的数据，则注释掉这一行
    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image, $result)){
        //匹配成功
        $image_name = uniqid().'.'.$result[2];
        $date = date('Y-m-d',time());
        mkFolder("./Data/topic/$date");
        $image_path = "topic/$date/$image_name";
        $image_file = "./Data/$image_path";//服务器文件存储路径
        if (file_put_contents($image_file, base64_decode(str_replace($result[1], '', $base64_image)))){
            return $image_path;
        }else{
            return 'service error';//上传错误
        }
    }else{
        return 'file error';//base64文件编码不为图片
    }
}


/**
 * [mkFolder 判断文件夹是否存在，不存在则创建]
 * @param  [String] $path [文件夹路径]
 */
function mkFolder($path){  
    if(!is_readable($path)){  
        is_file($path) or mkdir($path,0700);  
    }
} 

/**
 * [is_weixin 判断是否为微信浏览器]
 * @return boolean 
 */
function is_weixin(){ 
    if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
            return true;
    }   
    return false;
}