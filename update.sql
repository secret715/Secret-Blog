SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

ALTER TABLE `post`
ADD `comment` tinyint(1) NOT NULL AFTER `public`;

ALTER TABLE `post`
ADD `update_time` datetime NOT NULL AFTER `mktime`;

ALTER TABLE `post`
CHANGE `content` `content` longtext COLLATE 'utf8_unicode_ci' NOT NULL AFTER `title`;