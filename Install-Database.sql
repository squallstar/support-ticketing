/*
 Squallstar Support Ticketing

 Target Server Type    : MySQL
 Target Server Version : 40123
 File Encoding         : utf-8

 Date: 09/20/2011 10:24:00 AM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `support_users`
-- ----------------------------
DROP TABLE IF EXISTS `support_users`;
CREATE TABLE `support_users` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(20) default NULL,
  `password` varchar(20) default NULL,
  `realname` varchar(32) default NULL,
  `isadmin` smallint(1) default NULL,
  `email` varchar(128) NOT NULL default '',
  `notifications` smallint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- ----------------------------
--  Records of `support_users`
-- ----------------------------
INSERT INTO `support_users` VALUES ('1', 'admin', 'admin', 'Admin', '1', 'mail@example.com', '1');

-- ----------------------------
--  Table structure for `support_tickets`
-- ----------------------------
DROP TABLE IF EXISTS `support_tickets`;
CREATE TABLE `support_tickets` (
  `id` int(11) NOT NULL auto_increment,
  `owner` int(11) default NULL,
  `project` int(11) default NULL,
  `priority` int(11) default NULL,
  `data` datetime default NULL,
  `title` varchar(128) default NULL,
  `description` text character set utf8,
  `attach` varchar(255) default NULL,
  `status` varchar(12) default NULL,
  `worker` varchar(36) NOT NULL default '0',
  `last_update` datetime NOT NULL default '0000-00-00 00:00:00',
  `hidden` int(1) default NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- ----------------------------
--  Records of `support_tickets`
-- ----------------------------
INSERT INTO `support_tickets` VALUES ('1', '1', '1', null, '2011-09-20 09:29:02', 'Primo ticket', 'Ciao mondo! Grazie per aver installato Support ticketing!', null, 'inserted', '', '2011-06-17 00:47:38', '0');

-- ----------------------------
--  Table structure for `support_projects`
-- ----------------------------
DROP TABLE IF EXISTS `support_projects`;
CREATE TABLE `support_projects` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(64) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- ----------------------------
--  Records of `support_projects`
-- ----------------------------
INSERT INTO `support_projects` VALUES ('1', 'Progetto di prova');

-- ----------------------------
--  Table structure for `support_projects_relations`
-- ----------------------------
DROP TABLE IF EXISTS `support_projects_relations`;
CREATE TABLE `support_projects_relations` (
  `id` int(11) NOT NULL auto_increment,
  `project` int(11) NOT NULL default '0',
  `owner` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- ----------------------------
--  Records of `support_projects_relations`
-- ----------------------------
INSERT INTO `support_projects_relations` VALUES ('1', '1', '1');

-- ----------------------------
--  Table structure for `support_replies`
-- ----------------------------
DROP TABLE IF EXISTS `support_replies`;
CREATE TABLE `support_replies` (
  `id` int(11) NOT NULL auto_increment,
  `ticket` int(11) default NULL,
  `owner` int(11) default NULL,
  `data` datetime default NULL,
  `description` text character set utf8,
  `attach` varchar(255) default NULL,
  `quotetime` int(11) default NULL,
  `completedtime` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

