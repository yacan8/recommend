# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: localhost (MySQL 5.6.35)
# Database: recommend
# Generation Time: 2017-05-13 01:28:18 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table re_address_areas
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_address_areas`;

CREATE TABLE `re_address_areas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `areaid` varchar(20) NOT NULL,
  `area` varchar(50) NOT NULL,
  `cityid` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='行政区域县区信息表';



# Dump of table re_address_cities
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_address_cities`;

CREATE TABLE `re_address_cities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cityid` varchar(20) NOT NULL,
  `city` varchar(50) NOT NULL,
  `provinceid` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='行政区域地州市信息表';



# Dump of table re_address_provinces
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_address_provinces`;

CREATE TABLE `re_address_provinces` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provinceid` varchar(20) NOT NULL,
  `province` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='省份信息表';



# Dump of table re_apply
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_apply`;

CREATE TABLE `re_apply` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `id_number` varchar(30) COLLATE utf8_bin NOT NULL DEFAULT '',
  `file` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `state` int(11) NOT NULL DEFAULT '0',
  `time` datetime DEFAULT NULL,
  `email` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table re_audio
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_audio`;

CREATE TABLE `re_audio` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL DEFAULT '' COMMENT '标题',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '名',
  `duration` varchar(11) DEFAULT NULL COMMENT '时长',
  `upload_time` datetime DEFAULT NULL COMMENT '上传时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='音频表';



# Dump of table re_browse
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_browse`;

CREATE TABLE `re_browse` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `news_id` int(11) DEFAULT NULL,
  `ip` varchar(20) DEFAULT NULL,
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_id` varchar(13) DEFAULT NULL,
  `area` varchar(100) DEFAULT NULL COMMENT '地区',
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '地域判断 1中国 2中国不知省份 3 国外',
  PRIMARY KEY (`id`),
  KEY `newId_HASH` (`news_id`) USING HASH,
  KEY `time` (`time`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table re_cancel_follow
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_cancel_follow`;

CREATE TABLE `re_cancel_follow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `follow_id` int(11) NOT NULL DEFAULT '0',
  `time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table re_city_info
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_city_info`;

CREATE TABLE `re_city_info` (
  `ci_id` int(10) NOT NULL COMMENT '城市ID值',
  `ci_province` int(10) NOT NULL COMMENT '省份外键',
  `ci_city` varchar(32) NOT NULL COMMENT '城市名称',
  PRIMARY KEY (`ci_id`),
  KEY `ci_province` (`ci_province`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='学校城市选择静态信息表';



# Dump of table re_collection
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_collection`;

CREATE TABLE `re_collection` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `collection_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table re_comment
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_comment`;

CREATE TABLE `re_comment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `content` text COLLATE utf8_bin NOT NULL COMMENT '内容',
  `time` datetime NOT NULL COMMENT '时间',
  `delete_tag` bit(1) NOT NULL DEFAULT b'0' COMMENT '删除标识 0为未删除 1为删除',
  `news_id` int(11) NOT NULL COMMENT '新闻id',
  `reply` int(11) DEFAULT '0' COMMENT '自关联：评论回复ID',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `zan_count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table re_crawler
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_crawler`;

CREATE TABLE `re_crawler` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `from` int(11) DEFAULT NULL COMMENT '来源：1、新浪。2、网易。3、腾讯。',
  `state` int(11) NOT NULL DEFAULT '0' COMMENT '是否录入 1、有重复。2、已录入',
  `title` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '标题',
  `time` datetime DEFAULT NULL COMMENT '时间戳',
  `url` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '地址',
  `from_id` varchar(50) COLLATE utf8_bin DEFAULT NULL COMMENT '来源ID',
  PRIMARY KEY (`id`),
  UNIQUE KEY `from_id` (`from_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table re_crawler_from
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_crawler_from`;

CREATE TABLE `re_crawler_from` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `display_name` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `last_time` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table re_crawler_struct
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_crawler_struct`;

CREATE TABLE `re_crawler_struct` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `title_dom` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `content_dom` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `title_class` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `content_class` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table re_dynamics
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_dynamics`;

CREATE TABLE `re_dynamics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `content_id` bigint(1) NOT NULL DEFAULT '0' COMMENT '内容编号、1-关注ID、2-评论ID、3-点赞ID、4-新闻ID',
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '动态类型、1-关注了*。2-评论了新闻*。3-点赞了评论*。4-发表了新闻。',
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户动态表';



# Dump of table re_follow
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_follow`;

CREATE TABLE `re_follow` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '关注的人的id',
  `follow_id` int(11) NOT NULL DEFAULT '0' COMMENT '被关注用户ID',
  `delete_tag` bit(1) NOT NULL DEFAULT b'0' COMMENT '取消关注标识 0为未取消 1为取消关注',
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='关注表';



# Dump of table re_interest
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_interest`;

CREATE TABLE `re_interest` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table re_login
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_login`;

CREATE TABLE `re_login` (
  `id` bigint(21) NOT NULL AUTO_INCREMENT,
  `tel` varchar(11) NOT NULL DEFAULT '0',
  `password` varchar(255) NOT NULL DEFAULT '',
  `icon` varchar(255) NOT NULL DEFAULT '',
  `nickname` varchar(10) DEFAULT '',
  `email` varchar(40) DEFAULT NULL,
  `power` int(2) DEFAULT '0',
  `reg_time` datetime DEFAULT NULL,
  `userId` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户信息编号',
  `last_fans_read_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_message_read_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_dynamics_read_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tel_unique` (`tel`),
  UNIQUE KEY `nickname_unique` (`nickname`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



# Dump of table re_message
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_message`;

CREATE TABLE `re_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT '用户ID',
  `type` int(11) DEFAULT NULL COMMENT '消息类型 1-点赞。2-评论。3-评论ID。4-关注ID。5-新闻被评论了。',
  `content_id` int(11) NOT NULL DEFAULT '0' COMMENT '内容ID类型 消息类型 1-评论被点赞了。2-评论被回复了。3-新闻被评论了。4-被关注了。',
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `delete_tag` bit(11) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='消息表';



# Dump of table re_news
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_news`;

CREATE TABLE `re_news` (
  `id` bigint(1) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `publish_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `browse` int(11) NOT NULL DEFAULT '0',
  `contributor` varchar(13) NOT NULL DEFAULT '',
  `type` int(2) NOT NULL DEFAULT '0',
  `sections` int(3) NOT NULL DEFAULT '1',
  `state` int(11) NOT NULL DEFAULT '0',
  `image` varchar(255) DEFAULT NULL,
  `image_thumb` varchar(255) DEFAULT NULL,
  `comment_count` int(11) NOT NULL DEFAULT '0',
  `delete_tag` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`id`),
  KEY `一级分类` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table re_news_keyword
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_news_keyword`;

CREATE TABLE `re_news_keyword` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `keyword` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table re_news_keyword_belong
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_news_keyword_belong`;

CREATE TABLE `re_news_keyword_belong` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `news_id` int(11) NOT NULL,
  `keyword_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table re_news_similarity
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_news_similarity`;

CREATE TABLE `re_news_similarity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `news_id1` int(11) NOT NULL,
  `news_id2` int(11) NOT NULL,
  `similarity` float DEFAULT NULL,
  `last_modify_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index` (`news_id1`,`news_id2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table re_news_similarity_time
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_news_similarity_time`;

CREATE TABLE `re_news_similarity_time` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `news_id` int(11) NOT NULL,
  `last_calculate_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table re_portrayal
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_portrayal`;

CREATE TABLE `re_portrayal` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `portrayal` text,
  `last_modify_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table re_province_info
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_province_info`;

CREATE TABLE `re_province_info` (
  `pr_id` int(10) NOT NULL COMMENT '省份ID值',
  `pr_province` varchar(32) NOT NULL COMMENT '省份名称',
  PRIMARY KEY (`pr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='学校省份选择静态信息表';



# Dump of table re_recommend
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_recommend`;

CREATE TABLE `re_recommend` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `news_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table re_recommend_config
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_recommend_config`;

CREATE TABLE `re_recommend_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `browse_type` int(11) NOT NULL DEFAULT '1',
  `browse_keyword` int(11) NOT NULL DEFAULT '1',
  `comment_type` int(11) NOT NULL DEFAULT '1',
  `comment_keyword` int(11) NOT NULL DEFAULT '1',
  `zan_type` int(11) NOT NULL DEFAULT '1',
  `zan_keyword` int(11) NOT NULL DEFAULT '1',
  `follow_keyword` int(11) NOT NULL DEFAULT '1' COMMENT '关注的人喜欢权重',
  `allow_recommend_time` int(11) NOT NULL DEFAULT '7' COMMENT '可推荐时间跨度 单位/天',
  `calculate_time_span` int(11) NOT NULL DEFAULT '7' COMMENT '计算时间跨度，单位/天',
  `display_name` varchar(50) COLLATE utf8_bin DEFAULT NULL COMMENT '配置显示名',
  `description` text COLLATE utf8_bin COMMENT '描述',
  `state` bit(11) NOT NULL DEFAULT b'0' COMMENT '1为启用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table re_school_info
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_school_info`;

CREATE TABLE `re_school_info` (
  `sh_id` int(10) NOT NULL COMMENT '学校ID值',
  `sh_city` int(10) NOT NULL COMMENT '城市外键',
  `sh_shool` varchar(32) NOT NULL COMMENT '学校名称',
  PRIMARY KEY (`sh_id`),
  KEY `sh_city` (`sh_city`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='学校信息选择静态表';



# Dump of table re_sections
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_sections`;

CREATE TABLE `re_sections` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `type_id` bigint(11) NOT NULL DEFAULT '0' COMMENT '归属类型ID',
  `sections` varchar(255) NOT NULL DEFAULT '' COMMENT '二级标签',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='二级分类（标签）';



# Dump of table re_similarity
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_similarity`;

CREATE TABLE `re_similarity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id1` int(11) NOT NULL DEFAULT '0',
  `user_id2` int(11) NOT NULL DEFAULT '0',
  `similarity` float NOT NULL,
  `last_modify_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table re_type
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_type`;

CREATE TABLE `re_type` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(6) NOT NULL DEFAULT '',
  `color` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '#fff',
  `state` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table re_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_user`;

CREATE TABLE `re_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  `birthDate` date DEFAULT NULL,
  `sex` int(1) DEFAULT '3' COMMENT '0表示女，1表示男，相当于false和true  3未知',
  `schoolName` varchar(255) DEFAULT NULL COMMENT '学校',
  `profession` varchar(20) DEFAULT NULL COMMENT '专业',
  `province` varchar(255) DEFAULT NULL COMMENT '省份',
  `city` varchar(255) DEFAULT NULL COMMENT '城市',
  `area` varchar(255) DEFAULT NULL COMMENT '所在地区',
  `shelfIntroduction` varchar(255) DEFAULT NULL COMMENT '自我简介',
  `createTime` datetime DEFAULT NULL COMMENT '创建时间',
  `modifyTime` datetime DEFAULT NULL COMMENT '最后修改时间',
  `deleteTime` datetime DEFAULT NULL COMMENT '删除时间',
  `deleteTag` bit(1) DEFAULT b'0' COMMENT '删除标志：0表示未删除，1表示已删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户信息表';



# Dump of table re_visitor
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_visitor`;

CREATE TABLE `re_visitor` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(20) NOT NULL DEFAULT '',
  `area` varchar(255) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `read` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='访客表';



# Dump of table re_visitor_news
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_visitor_news`;

CREATE TABLE `re_visitor_news` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(20) DEFAULT NULL,
  `area` varchar(100) DEFAULT NULL,
  `news_id` bigint(255) DEFAULT NULL,
  `date` datetime NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文章访客表';



# Dump of table re_word_library
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_word_library`;

CREATE TABLE `re_word_library` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `word` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '词语',
  `num` int(11) NOT NULL DEFAULT '0' COMMENT '出现该词的文章个数',
  PRIMARY KEY (`id`),
  UNIQUE KEY `唯一` (`word`),
  KEY `索引` (`word`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='语料库';



# Dump of table re_zan
# ------------------------------------------------------------

DROP TABLE IF EXISTS `re_zan`;

CREATE TABLE `re_zan` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
