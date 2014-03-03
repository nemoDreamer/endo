-- phpMyAdmin SQL Dump
-- version 3.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 02, 2014 at 08:11 PM
-- Server version: 5.1.44
-- PHP Version: 5.3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `endo_liveit`
--

-- --------------------------------------------------------

--
-- Table structure for table `blog`
--

CREATE TABLE `blog` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` text,
  `description` text,
  `author` varchar(128) DEFAULT NULL,
  `is_published` char(1) NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `subject_class` varchar(32) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `object_class` text,
  `object_id` int(11) DEFAULT NULL,
  `action` varchar(32) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`subject_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `faq`
--

CREATE TABLE `faq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` text NOT NULL,
  `answer` text COLLATE utf8_unicode_ci,
  `email` varchar(128) DEFAULT NULL,
  `is_published` binary(1) NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `faq_tag`
--

CREATE TABLE `faq_tag` (
  `faq_id` int(11) DEFAULT NULL,
  `tag_id` int(11) DEFAULT NULL,
  KEY `faq_id` (`faq_id`,`tag_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE `post` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `blog_id` int(11) DEFAULT NULL,
  `title` text,
  `body` text,
  `is_published` char(1) NOT NULL DEFAULT '0',
  `published` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `blog_id` (`blog_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE `setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variable` varchar(128) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  `group` varchar(64) DEFAULT NULL,
  `label` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `tag`
--

CREATE TABLE `tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL DEFAULT '',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned DEFAULT NULL,
  `first_name` varchar(128) DEFAULT NULL,
  `last_name` varchar(128) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `crypted_password` text,
  `salt` text,
  `class` varchar(16) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
