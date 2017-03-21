<?php
namespace Admin\Model;
use Think\Model;
class RecommonedConfigModel extends Model{

	protected $_validate = array(
	    array('display_name','require','请输入配置名字！'),
	);
}