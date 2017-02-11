<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// |         lanfengye <zibin_5257@163.com>
// +----------------------------------------------------------------------
namespace Think;
class Page {
    
    //起始页
    public $startPage ;
    //结束页
    public $endPage ;
    // 分页栏每页显示的页数
    public $rollPage = 5;
    // 页数跳转时要带的参数
    public $parameter  ;
    // 分页URL地址
    public $url     =   '';
    // 默认列表每页显示行数
    public $listRows = 20;
    // 起始行数
    public $firstRow    ;
    // 分页总页面数
    protected $totalPages  ;
    // 总行数
    protected $totalRows  ;
    // 当前页数
    protected $nowPage;
    // 分页的栏的总页数
    protected $coolPages   ;
    // 分页显示定制
    protected $config  =    array('header'=>'条记录','prev'=>'<span aria-hidden="true">«</span>','next'=>'<span aria-hidden="true">»</span>','first'=>'首页','last'=>'最后一页','theme'=>' %first%  %upPage% %linkPage% %downPage% %end%');
    // protected $config  =    array('header'=>'条记录','prev'=>'上一页','next'=>'下一页','first'=>'首页','last'=>'最后一页','theme'=>' %first%  %upPage% %linkPage% %downPage% %end% <li><span> %totalRow% %header% %nowPage%/%totalPage% 页</span></li> ');
    // 默认分页变量名
    protected $varPage;

    /**
     * 架构函数
     * @access public
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     */
    public function __construct($totalRows,$listRows='',$parameter='',$url='') {
        $this->totalRows    =   $totalRows;
        $this->parameter    =   $parameter;
        $this->varPage      =   C('VAR_PAGE') ? C('VAR_PAGE') : 'p' ;
        if(!empty($listRows)) {
            $this->listRows =   intval($listRows);
        }
        $this->totalPages   =   ceil($this->totalRows/$this->listRows);     //总页数
        $this->coolPages    =   ceil($this->totalPages/$this->rollPage);
        $this->nowPage      =   !empty($_GET[$this->varPage])?intval($_GET[$this->varPage]):1;
        if($this->nowPage<1){
            $this->nowPage  =   1;
        }elseif(!empty($this->totalPages) && $this->nowPage>$this->totalPages) {
            $this->nowPage  =   $this->totalPages;
        }
        $this->firstRow     =   $this->listRows*($this->nowPage-1);
        if(!empty($url))    $this->url  =   $url; 
    }

    public function setConfig($name,$value) {
        if(isset($this->config[$name])) {
            $this->config[$name]    =   $value;
        }
    }

    /**
     * 分页显示输出
     * @access public
     */
    public function show() {
        if(0 == $this->totalRows) return '';
        $p              =   $this->varPage;
        $nowCoolPage    =   ceil($this->nowPage/$this->rollPage);

        // 分析分页参数
        if($this->url){
            $depr       =   C('URL_PATHINFO_DEPR');
            $url        =   rtrim(U('/'.$this->url,'',false),$depr).$depr.'__PAGE__';
        }else{
            if($this->parameter && is_string($this->parameter)) {
                parse_str($this->parameter,$parameter);
            }elseif(is_array($this->parameter)){
                $parameter      =   $this->parameter;
            }elseif(empty($this->parameter)){
                unset($_GET[C('VAR_URL_PARAMS')]);
                $var =  !empty($_POST)?$_POST:$_GET;
                if(empty($var)) {
                    $parameter  =   array();
                }else{
                    $parameter  =   $var;
                }
            }
            $parameter[$p]  =   '__PAGE__';
            $url            =   U('',$parameter);
        }
        //上下翻页字符串
        $upRow          =   $this->nowPage-1;
        $downRow        =   $this->nowPage+1;
        if ($this->nowPage>1){
            $upPage     =   "<li><a href='".str_replace('__PAGE__',$upRow,$url)."'>".$this->config['prev']."</a></li>";
        }else{
            $upPage     =   '';
        }

        if ($downRow <= $this->totalPages){
            $downPage   =   "<li><a href='".str_replace('__PAGE__',$downRow,$url)."'>".$this->config['next']."</a></li>";
        }else{
            $downPage   =   '';
        }
        // << < > >>


        if($this->nowPage > 2){     //当当前页大于1 输出首页与上一页 
             $preRow     =   $this->nowPage-$this->rollPage;
            // $prePage    =   "<li><a href='".str_replace('__PAGE__',$preRow,$url)."' >上".$this->rollPage."页</a></li>";
            $theFirst   =   "<li><a href='".str_replace('__PAGE__',1,$url)."' >".$this->config['first']."</a></li>";
        }else{            //当当前页等于1
            $theFirst   =   '';
            $prePage    =   '';
        }
        // if($nowCoolPage == 1){
        //     $theFirst   =   '';
        //     $prePage    =   '';
        // }else{
        //     $preRow     =   $this->nowPage-$this->rollPage;
        //     $prePage    =   "<li><a href='".str_replace('__PAGE__',$preRow,$url)."' >上".$this->rollPage."页</a></li>";
        //     $theFirst   =   "<li><a href='".str_replace('__PAGE__',1,$url)."' >".$this->config['first']."</a></li>";
        // }
        // if($nowCoolPage == $this->coolPages){
        //     $nextPage   =   '';
        //     $theEnd     =   '';
        // }else{
        //     $nextRow    =   $this->nowPage+$this->rollPage;
        //     $theEndRow  =   $this->totalPages;
        //     $nextPage   =   "<li><a href='".str_replace('__PAGE__',$nextRow,$url)."' >下".$this->rollPage."页</a></li>";
        //     $theEnd     =   "<li><a href='".str_replace('__PAGE__',$theEndRow,$url)."' >".$this->config['last']."</a></li>";
        // }

        if($this->nowPage < $this->totalPages-1){
            $nextRow    =   $this->nowPage+$this->rollPage;
            $theEndRow  =   $this->totalPages;
            $nextPage   =   "<li><a href='".str_replace('__PAGE__',$nextRow,$url)."' >下".$this->rollPage."页</a></li>";
            $theEnd     =   "<li><a href='".str_replace('__PAGE__',$theEndRow,$url)."' >".$this->config['last']."</a></li>";
        }else{
            $nextPage   =   '';
            $theEnd     =   '';
        }


        // 1 2 3 4 5
        $linkPage = "";

        if($this->totalPages<=$this->rollPage)                  //当总页数小于分页栏每页显示的页数时
        {
            $this->startPage =1; //设置起始页为1
            $this->endPage =$this->totalPages; //设置结束页为总页数
        }
        else if($this->nowPage <= floor($this->rollPage/2)+1)   //当当前页数小于分页栏每页显示的页数时
        {
            $this->startPage =1; //设置起始页为1
            $this->endPage =$this->rollPage; //设置结束页位分页栏每页显示的页数
        }
        else if($this->nowPage <= $this->totalPages-floor($this->rollPage/2)&&$this->nowPage > floor($this->rollPage/2)+1  )        //当当前页数大于没分页兰每页显示的页数的一半且小于总页数减去分页栏的一半时
        {
            if($this->nowPage<$this->rollPage)
                $up = 1;
            else
                $up = $this->nowPage-$this->rollPage;
            $linkPage .= "<li><a href='".str_replace('__PAGE__',$up,$url)."'>...</a></li>";     //显示前省略号
            $this->startPage =$this->nowPage - floor($this->rollPage/2); //设置起始页当前页减去分页栏页数的一半
            $this->endPage =$this->nowPage + floor($this->rollPage/2); //设置结束页当前页加上分页栏页数的一半
        }

        else if($this->nowPage > $this->totalPages-floor($this->rollPage/2))//当当前页大于总页数减去分页栏页数时
        {
            if($this->nowPage<$this->rollPage)              
                $up = 1;
            else
                $up = $this->nowPage-$this->rollPage;
            $linkPage .= "<li><a href='".str_replace('__PAGE__',$up,$url)."'>...</a></li>";//显示前省略号
            $this->startPage = $this->totalPages - $this->rollPage; //设置起始页总页数减去分页栏页数
            $this->endPage = $this->totalPages; //设置结束页总页数
        }

         for($i=$this->startPage;$i<=$this->endPage;$i++){                  //输出页数中间连接
            if($i==$this->nowPage){
                $linkPage .= "<li class='active'><a>".$i."</a></li>";
            }else{
                $linkPage .= "<li><a href='".str_replace('__PAGE__',$i,$url)."'>".$i."</a></li>";
            }
        }
        if($this->nowPage < $this->totalPages-floor($this->rollPage/2) && $this->totalPages > $this->rollPage)                //当当前页小于总页数减去分页栏页数的一半
        {
            if($this->nowPage<$this->totalPages-$this->rollPage)
                $down = $this->nowPage+$this->rollPage;
            else
                $down = $this->totalPages;
            $linkPage .= "<li><a href='".str_replace('__PAGE__',$down,$url)."'>...</a></li>";//输出后省略号
        }




        // for($i=1;$i<=$this->rollPage;$i++){
        // for($i=$this->startPage;$i<=$this->endPage;$i++){
        //     $page       =   ($nowCoolPage-1)*$this->rollPage+$i;
        //     if($page!=$this->nowPage){
        //         if($page<=$this->totalPages){
        //             $linkPage .= "<li><a href='".str_replace('__PAGE__',$page,$url)."'>".$page."</a></li>";
        //         }else{
        //             break;
        //         }
        //     }else{
        //         if($this->totalPages != 1){
        //             $linkPage .= "<li class='disabled'><span>".$page."</span></li>";
        //         }
        //     }
        // }
        $pageStr     =   str_replace(
            array('%header%','%nowPage%','%totalRow%','%totalPage%','%upPage%','%downPage%','%first%','%prePage%','%linkPage%','%nextPage%','%end%'),
            array($this->config['header'],$this->nowPage,$this->totalRows,$this->totalPages,$upPage,$downPage,$theFirst,$prePage,$linkPage,$nextPage,$theEnd),$this->config['theme']);
        return $pageStr;
    }

}
