-- phpMyAdmin SQL Dump
-- version 4.0.10.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 03, 2021 at 09:36 PM
-- Server version: 10.3.17-MariaDB
-- PHP Version: 7.3.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Table structure for table `tbl_password_users`
--

DROP TABLE IF EXISTS `tbl_password_users`;
CREATE TABLE IF NOT EXISTS `tbl_password_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `who` char(64) DEFAULT NULL,
  `pass` char(64) NOT NULL DEFAULT '-',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Table structure for table `tbl_password`
--

DROP TABLE IF EXISTS `tbl_password`;
CREATE TABLE IF NOT EXISTS `tbl_password` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site` text NOT NULL,
  `user` text NOT NULL,
  `pass` text NOT NULL,
  `notes` text NOT NULL,
  `visible` enum('Y','N') NOT NULL DEFAULT 'Y',
  `owner` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
