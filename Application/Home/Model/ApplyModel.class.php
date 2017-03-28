<?php
namespace Home\Model;
use Think\Model;

class ApplyModel extends Model{
	protected $_validate = array(
		array('name','require','姓名不能为空！'),
		array('phone','require','手机号码不能为空！'),
		array('email','require','邮箱不能为空！'),
		array('phone','/^1[34578]\d{9}$/','请输入正确手机号码'),
		array('email','/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/','请输入正确邮箱'),
		array('id_number','require','请输入证件号码'),
		array('phone','','帐号名称已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
	);




}