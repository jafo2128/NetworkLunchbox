-- MySQL dump 10.10
--
-- Host: localhost    Database: test
-- ------------------------------------------------------
-- Server version	5.0.26

-- Network Lunchbox - http://lunchbox.jasonantman.com
-- $LastChangedRevision$
-- $HeadURL$


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `lunchbox_cdp`
--

DROP TABLE IF EXISTS `lunchbox_cdp`;
CREATE TABLE `lunchbox_cdp` (
  `hostname` varchar(100) NOT NULL,
  `ifname` varchar(10) NOT NULL,
  `DeviceID` varchar(100) default NULL,
  `Address` varchar(24) default NULL,
  `PortID` varchar(10) default NULL,
  `Platform` varchar(50) default NULL,
  `VTPManagementDomain` varchar(100) default NULL,
  `NativeVLANID` varchar(6) default NULL,
  `SystemName` varchar(100) default NULL,
  `ManagementAddress` varchar(24) default NULL,
  `PhysicalLocation` varchar(100) default NULL,
  `updated_ts` int(10) unsigned default NULL,
  PRIMARY KEY  (`hostname`,`ifname`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `lunchbox_hosts`
--

DROP TABLE IF EXISTS `lunchbox_hosts`;
CREATE TABLE `lunchbox_hosts` (
  `hostname` varchar(100) NOT NULL,
  `uptime` int(10) unsigned default NULL,
  `updated_ts` int(10) unsigned default NULL,
  PRIMARY KEY  (`hostname`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `lunchbox_interfaces`
--

DROP TABLE IF EXISTS `lunchbox_interfaces`;
CREATE TABLE `lunchbox_interfaces` (
  `hostname` varchar(100) NOT NULL,
  `ifname` varchar(10) NOT NULL,
  `addr` varchar(24) default NULL,
  `mac` varchar(24) default NULL,
  `bcast` varchar(24) default NULL,
  `netmask` varchar(24) default NULL,
  `updated_ts` int(10) unsigned default NULL,
  PRIMARY KEY  (`hostname`,`ifname`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2009-10-13 13:43:18
