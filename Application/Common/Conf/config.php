<?php
return array(
	//'配置项'=>'配置值'
	'DB_TYPE' => 'mysql',
	'DB_NAME' => 'recommend',
	'DB_HOST' => 'localhost',
	'DB_USER' => 'root',
	'DB_PWD' => 'root',
	'DB_PREFIX' => 're_',
	'DB_PORT' => '3306',
	'URL_MODEL'=>'1',
	'ERROR_PAGE'=>'/Home/view/Common/404.html',
	'COOKIE_CONFIG' => array(
		'path'=> __ROOT__,
		'domain'=> $_SERVER['SERVER_NAME']
	),
	//调试
	'URL_PATHINFO_DEPR'     =>  '/',
	'__DATA__' => __ROOT__.'/Data',
	'SHOW_PAGE_TRACE' =>false,
	'MODULE_ALLOW_LIST' => array('Home','Admin'), // 配置你原来的分组列表
	'DEFAULT_MODULE' => 'Home', // 配置你原来的默认分组
	// 'HTML_CACHE_ON'=>true,
	// 'TMPL_CACHE_ON' => true,//禁止模板编译缓存
	// 表单令牌
	'TOKEN_ON'=>true,  // 是否开启令牌验证
	'TOKEN_NAME'=>'__hash__',    // 令牌验证的表单隐藏字段名称
	'TOKEN_TYPE'=>'md5',  //令牌哈希验证规则 默认为MD5
	'TOKEN_RESET'=>true,  //令牌验证出错后是否重置令牌 默认为true

	'URL_ROUTER_ON'   => true,
	'URL_ROUTE_RULES'=>array(
		't/:id'  => array('Topic/detail','',array('ext'=>'html')),
		'n/:id'  => array('News/detail','',array('ext'=>'html')),
		'u/:id'  =>array('User/index','',array('ext'=>'')),
	),
);
?>
