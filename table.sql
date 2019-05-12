SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `class`;
CREATE TABLE `class` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `classname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mktime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `member`;
CREATE TABLE `member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `nickname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `level` tinyint(4) NOT NULL,
  `joined` datetime NOT NULL,
  `last_login` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `post`;
CREATE TABLE `post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci NOT NULL,
  `type` tinyint(1) NOT NULL,
  `public` tinyint(1) NOT NULL,
  `comment` tinyint(1) NOT NULL,
  `class` int(11) NOT NULL,
  `keyword` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mktime` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  `author` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



INSERT INTO `class` (`id`, `classname`, `mktime`) VALUES
(1,	'預設分類',	now());

INSERT INTO `post` (`id`, `title`, `content`, `type`, `public` ,`comment`, `class`, `keyword`, `mktime`,`update_time`, `author`) VALUES
(1,	'歡迎使用Secret Blog！',	'<p>Secret Blog已經成功安裝囉！</p>',	0,	1,	1,	1,	'Secret Blog',now(),now(),	1);

INSERT INTO `post` (`id`, `title`, `content`, `type`, `public`, `comment`, `class`, `keyword`, `mktime`,`update_time`, `author`) VALUES
(2,	'預設頁面',	'<p>這是你的第一個頁面</p>',	1,	1,	0,	0,	'Secret Blog',now(),now(),	1);