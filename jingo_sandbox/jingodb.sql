-- phpMyAdmin SQL Dump
-- version 3.5.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 15, 2013 at 10:35 PM
-- Server version: 5.5.29
-- PHP Version: 5.4.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `jingodb`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `receive`(IN `r` INT(4), IN `st` VARCHAR(40), IN `t` DATETIME, IN `a` VARCHAR(40), IN `ln` DOUBLE, IN `la` DOUBLE, OUT `ni` INT(5))
    READS SQL DATA
begin
declare result int;

SELECT distinct n.nid INTO ni 
FROM view_note n, view_receive r
WHERE checkAuthority(
getUID(
n.poster
), r.uid, n.authority
)
AND (
(
n.area = r.area
AND n.area IS NOT NULL
)
OR getDistance(
n.latitude, n.longitude, r.latitude, r.longitude
) <= n.radius
)
AND (
(
r.f_area = r.area
AND r.f_area IS NOT NULL
)
OR getDistance(
r.f_latitude, r.f_longitude, r.latitude, r.longitude
) <= r.f_radius
)
AND n.tag = r.tag
AND curdatecheck(
n.sch_date, r.curtime
)
AND curdatecheck(
r.sch_date, r.curtime
)
AND timecheck(
r.starttime, r.endtime, n.starttime, n.endtime, r.curtime
) and r.uid=r and r.state=st and r.curtime=t and r.area=a
and r.longitude=ln and r.latitude=la;

end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `receive_insert`(IN `u` INT(4), IN `s` VARCHAR(40), IN `c` DATETIME, IN `a` VARCHAR(40), IN `lng` DOUBLE, IN `lat` DOUBLE)
    MODIFIES SQL DATA
begin
if getLID(lng,lat)= -1 then insert into receive (uid,sid,curtime,aid) values (u,getSID(s),c,getAID(a));
else insert into receive (uid,sid,curtime,aid,curlocation) values (u,getSID(s),c,getAID(a),getLID(lng,lat));
end if;
end$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `checkAuthority`(`poster` INT(4), `receiver` INT(4), `authority` VARCHAR(20)) RETURNS int(11)
    READS SQL DATA
BEGIN
declare result int;
case authority
when 'myself' then set result=0;
when 'friend' then set result=isFriend(poster,receiver);
else set result=1;
end case;
return result;
end$$

CREATE DEFINER=`root`@`localhost` FUNCTION `checkLiked`(`n` INT(5), `li` INT(4)) RETURNS int(11)
    READS SQL DATA
BEGIN
declare ni,result int;
set ni=(select nid from liked where nid=n and liker=li);
if ni is null then set result=1;
else set result=0;
end if;
return result;
end$$

CREATE DEFINER=`root`@`localhost` FUNCTION `checkMarked`(`n` INT(5), `u` INT(4)) RETURNS int(11)
    READS SQL DATA
BEGIN
declare ni,result int;
set ni=(select nid from bookmark where nid=n and uid=u);
if ni is null then set result=1;
else set result=0;
end if;
return result;
end$$

CREATE DEFINER=`root`@`localhost` FUNCTION `curdatecheck`(`d1` VARCHAR(20), `d2` TIMESTAMP) RETURNS int(11)
    READS SQL DATA
BEGIN
declare x1,x2,result int;
set result = 0;
case d1
when 'everyday' then set x1 = 0;
when 'Mondays' then set x1 = 1;
when 'Tuesdays' then set x1 = 2;
when 'Wednesdays' then set x1 = 3;
when 'Thursdays' then set x1 = 4;
when 'Fridays' then set x1 = 5;
when 'Saturdays' then set x1 = 6;
when 'Sundays' then set x1 = 7;
when 'weekday' then set x1 = 8;
when 'weekend' then set x1 = 9;
else set x1 = 10;
end case;
set x2 = weekday(d2) + 1;
if x1 = 0 then set result = 1;
else if x1 = 8 and 1 <= x2 <= 5 then set result = 1;
else if x1 = 9 and 6 <= x2 <= 7 then set result = 1;
else if x1 = x2 then set result = 1;
else set result = 0;
end if;
end if;
end if;
end if;
return result;
end$$

CREATE DEFINER=`root`@`localhost` FUNCTION `datecheck`(`d1` VARCHAR(20), `d2` VARCHAR(20)) RETURNS int(1)
    READS SQL DATA
BEGIN
declare x1,x2,result int;
set result = 0;
case d1
when 'everyday' then set x1 = 0;
when 'Mondays' then set x1 = 1;
when 'Tuesdays' then set x1 = 2;
when 'Wednesdays' then set x1 = 3;
when 'Thursdays' then set x1 = 4;
when 'Fridays' then set x1 = 5;
when 'Saturdays' then set x1 = 6;
when 'Sundays' then set x1 = 7;
when 'weekday' then set x1 = 8;
when 'weekend' then set x1 = 9;
else set x1 = weekday(d1)+1;
end case;

case d2
when 'everyday' then set x2 = 0;
when 'Mondays' then set x2 = 1;
when 'Tuesdays' then set x2 = 2;
when 'Wednesdays' then set x2 = 3;
when 'Thursdays' then set x2 = 4;
when 'Fridays' then set x2 = 5;
when 'Saturdays' then set x2 = 6;
when 'Sundays' then set x2 = 7;
when 'weekday' then set x2 = 8;
when 'weekend' then set x2 = 9;
else set x2 = weekday(d2)+1;
end case;

if x1 = 0 then set result = 1;
else if x1 = 8 and 1 <= x2 <= 5 then set result = 1;
else if x1 = 9 and 6 <= x2 <= 7 then set result = 1;
else if x1 = x2 then set result = 1;
else set result = 0;
end if;
end if;
end if;
end if;
return result;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `getAID`(`a` VARCHAR(40)) RETURNS int(4)
    READS SQL DATA
begin
declare id int;
set id = (select aid from area where area=a);
return id;
end$$

CREATE DEFINER=`root`@`localhost` FUNCTION `getDistance`(`x1` DOUBLE, `y1` DOUBLE, `x2` DOUBLE, `y2` DOUBLE) RETURNS double
    READS SQL DATA
BEGIN 
declare Flattening,er,pix,z1,z2,d1,b1,b2,l1,l2,theta,distance double; 
set Flattening=298.257223563002; 
set er=6378.137; 
set pix=3.1415926535; 
set b1 = (0.5-x1/180)*pix; 
set l1 = (y1/180)*pix; 
set b2 = (0.5-x2/180)*pix; 
set l2 = (y2/180)*pix; 
set x1 = er*COS(l1)*SIN(b1); 
set y1 = er*SIN(l1)*SIN(b1); 
set z1 = er*COS(b1); 
set x2 = er*COS(l2)*SIN(b2); 
set y2 = er*SIN(l2)*SIN(b2); 
set z2 = er*COS(b2); 
set d1 = sqrt((x1-x2)*(x1-x2)+(y1-y2)*(y1-y2)+(z1-z2)*(z1-z2)); 
set theta= acos((er*er+er*er-d1*d1)/(2*er*er)); 
set distance= theta*er; 
RETURN distance; 
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `getFID`(`u` INT(4), `s` VARCHAR(40), `sch` VARCHAR(20), `st` TIME, `end` TIME, `a` VARCHAR(40), `lng` DOUBLE, `lat` DOUBLE, `r` INT(6)) RETURNS int(4)
    MODIFIES SQL DATA
BEGIN
declare id,sid2,sch_id2,aid2,lid2 int;
set sid2=getSID(s);
set sch_id2=getSCHID(sch,st,end);
set aid2=getAID(a);
set lid2=getLID(lng,lat);
set id=(select fid from filters where uid=u and sid=sid2
and sch_id=sch_id2 and aid=aid2 and lid=lid2 and radius=r);
if id is null then insert into filters (uid,sid,sch_id,aid,lid,radius)
values (u,sid2,sch_id2,aid2,lid2,r);
set id=(select fid from filters where uid=u and sid=sid2
and sch_id=sch_id2 and aid=aid2 and lid=lid2 and radius=r);
end if;
return id;
end$$

CREATE DEFINER=`root`@`localhost` FUNCTION `getLID`(`lng` DOUBLE, `lat` DOUBLE) RETURNS int(5)
    MODIFIES SQL DATA
BEGIN
declare id int;
if lng=0 and lat=0 then set id = -1;
else set id=(SELECT lid from location where longitude=lng and latitude=lat);
end if;
if id is null then insert into location (longitude,latitude) values (lng,lat);
set id=(SELECT lid from location where longitude=lng and latitude=lat);
end if;
return id;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `getNID`(`u` INT(4), `t` TEXT, `h` VARCHAR(200), `sch` VARCHAR(20), `st` TIME, `end` TIME, `a` VARCHAR(40), `lng` DOUBLE, `lat` DOUBLE, `r` INT(6), `autho` VARCHAR(20)) RETURNS int(5)
    MODIFIES SQL DATA
BEGIN
declare id,sch_id2,aid2,lid2 int;
set sch_id2=getSCHID(sch,st,end);
set aid2=getAID(a);
set lid2=getLID(lng,lat);

insert into note (uid,text,hyperlink,aid,lid,radius,sch_id,authority)
values (u,t,h,aid2,lid2,r,sch_id2,autho);
set id=(select nid from note where uid=u and 
posttime=(select max(posttime) from note where uid=u));
return id;
end$$

CREATE DEFINER=`root`@`localhost` FUNCTION `getSCHID`(`s` VARCHAR(20), `a` TIME, `b` TIME) RETURNS int(4)
    NO SQL
BEGIN
declare id int;
set id=(SELECT sch_id from schedule where sch_date=s and starttime=a and endtime=b);
if id is null then insert into schedule (sch_date,starttime,endtime) values (s,a,b);
set id=(SELECT sch_id from schedule where sch_date=s and starttime=a and endtime=b);
end if;
return id;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `getSID`(`s` VARCHAR(40)) RETURNS int(3)
    MODIFIES SQL DATA
BEGIN
declare id int;
set id=(SELECT sid from state where state=s);
if id is null then insert into state (state) values (s);
set id=(select sid from state where state=s);
end if;
return id;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `getTID`(`a` VARCHAR(40)) RETURNS int(4)
    MODIFIES SQL DATA
BEGIN
declare id int;
set id=(SELECT tid from tag where tag=a);
if id is null then insert into tag (tag) values (a);
set id=(select tid from tag where tag=a);
end if;
return id;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `getUID`(`name` VARCHAR(20)) RETURNS int(4)
    READS SQL DATA
BEGIN
declare id int;
set id = -1;
set id=(SELECT uid from user where uname=name);
return id;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `isFriend`(`u` INT(4), `f` INT(4)) RETURNS int(11)
    READS SQL DATA
BEGIN
declare id, result int;
set id=(select uid from friendrequest where status='Confirmed'
and ((uid=u and friend_id=f)or(uid=f and friend_id=u)));
if id is null then set result=0;
else set result=1;
end if;
return result;
end$$

CREATE DEFINER=`root`@`localhost` FUNCTION `timecheck`(`r1` TIME, `r2` TIME, `n1` TIME, `n2` TIME, `curtime` TIMESTAMP) RETURNS int(1)
    READS SQL DATA
begin
declare result int;
if r1 >= n1 and r2 <= n2 and r1 <= time(curtime) <= r2 then set result = 1;
else if r1 <= n1 and n1 <= r1 <= n2 and n1 <= time(curtime) <= r2 then set result = 1;
else if n1 <= r1 <= n2 <= r2 and r1 <= time(curtime) <= n2 then set result = 1;
else if r1 <= n1 and n2 <= r2 and n1 <= time(curtimer) <= n2 then set result = 1;
else set result = 0;
end if;
end if;
end if;
end if;
return result;
end$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `area`
--

CREATE TABLE `area` (
  `aid` int(4) NOT NULL AUTO_INCREMENT,
  `area` varchar(40) NOT NULL,
  PRIMARY KEY (`aid`),
  UNIQUE KEY `aid` (`aid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `area`
--

INSERT INTO `area` (`aid`, `area`) VALUES
(1, 'NYU-POLY'),
(2, 'SOHO'),
(3, 'NYU Bobst'),
(4, 'NYU gym'),
(5, 'Times Square'),
(6, 'Central Park');

-- --------------------------------------------------------

--
-- Table structure for table `bookmark`
--

CREATE TABLE `bookmark` (
  `nid` int(5) NOT NULL,
  `uid` int(4) NOT NULL,
  PRIMARY KEY (`nid`,`uid`),
  KEY `nid` (`nid`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bookmark`
--

INSERT INTO `bookmark` (`nid`, `uid`) VALUES
(1, 2),
(12, 2),
(13, 2),
(17, 2),
(18, 2);

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `cid` int(4) NOT NULL AUTO_INCREMENT,
  `nid` int(5) DEFAULT NULL,
  `uid` int(4) DEFAULT NULL,
  `commenttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `context` text,
  PRIMARY KEY (`cid`),
  KEY `nid` (`nid`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `comment`
--

INSERT INTO `comment` (`cid`, `nid`, `uid`, `commenttime`, `context`) VALUES
(1, 6, 3, '2013-05-06 01:46:38', 'good point!!'),
(2, 6, 5, '2013-05-06 01:46:44', 'good!!'),
(3, 7, 4, '2013-05-06 01:46:50', 'very good!'),
(4, 6, 2, '2013-05-06 14:23:33', 'social time'),
(5, 1, 2, '2013-05-06 14:26:47', 'good!'),
(7, 8, 2, '2013-05-07 22:13:41', 'sporting time!'),
(8, 1, 13, '2013-05-15 05:24:59', 'very good!'),
(9, 1, 14, '2013-05-15 05:25:12', 'good point'),
(10, 1, 11, '2013-05-15 05:25:35', 'enjoy it'),
(11, 1, 2, '2013-05-15 19:40:43', 'good one!');

-- --------------------------------------------------------

--
-- Table structure for table `filters`
--

CREATE TABLE `filters` (
  `fid` int(4) NOT NULL AUTO_INCREMENT,
  `uid` int(4) DEFAULT NULL,
  `sid` int(3) DEFAULT NULL,
  `sch_id` int(4) DEFAULT NULL,
  `aid` int(4) DEFAULT NULL,
  `lid` int(5) DEFAULT NULL,
  `radius` int(6) DEFAULT NULL,
  PRIMARY KEY (`fid`),
  KEY `uid` (`uid`),
  KEY `sid` (`sid`),
  KEY `sch_id` (`sch_id`),
  KEY `lid` (`lid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `filters`
--

INSERT INTO `filters` (`fid`, `uid`, `sid`, `sch_id`, `aid`, `lid`, `radius`) VALUES
(1, 3, 2, 6, 3, 3, 4),
(2, 2, 5, 7, 2, 2, 5),
(3, 2, 3, 3, 1, 1, 3),
(4, 3, 2, 6, 2, 2, 5),
(5, 2, 2, 6, 4, 4, 6),
(6, 4, 6, 6, 4, 4, 3),
(7, 5, 4, 7, NULL, 5, 5),
(8, 2, 2, 19, 4, 16, 6),
(9, 2, 2, 21, 6, 19, 10),
(10, 1, 2, 31, NULL, 19, 8),
(11, 2, 2, 46, 1, 15, 5),
(12, 1, 2, 47, 1, 15, 5),
(13, 1, 2, 48, 1, 15, 5),
(14, 2, 6, 50, 1, 15, 5);

-- --------------------------------------------------------

--
-- Table structure for table `filters_tag`
--

CREATE TABLE `filters_tag` (
  `fid` int(4) NOT NULL DEFAULT '0',
  `tid` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`fid`,`tid`),
  KEY `tid` (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `filters_tag`
--

INSERT INTO `filters_tag` (`fid`, `tid`) VALUES
(1, 1),
(2, 1),
(5, 1),
(6, 1),
(1, 2),
(4, 2),
(5, 2),
(8, 2),
(9, 2),
(11, 2),
(12, 2),
(14, 2),
(3, 3),
(7, 4),
(9, 4),
(13, 5),
(14, 5),
(4, 6),
(6, 6),
(7, 6),
(8, 7),
(11, 8),
(12, 8),
(13, 8),
(14, 8);

-- --------------------------------------------------------

--
-- Stand-in structure for view `filters_view`
--
CREATE TABLE `filters_view` (
`uid` int(4)
,`fid` int(4)
,`state` varchar(40)
,`sch_date` varchar(20)
,`starttime` time
,`endtime` time
,`area` varchar(40)
,`longitude` double(12,8)
,`latitude` double(12,8)
,`radius` int(6)
);
-- --------------------------------------------------------

--
-- Table structure for table `friendrequest`
--

CREATE TABLE `friendrequest` (
  `uid` int(4) NOT NULL DEFAULT '0',
  `friend_id` int(4) NOT NULL DEFAULT '0',
  `senttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `responsetime` datetime DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`uid`,`friend_id`,`senttime`),
  KEY `friend_id` (`friend_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `friendrequest`
--

INSERT INTO `friendrequest` (`uid`, `friend_id`, `senttime`, `responsetime`, `status`) VALUES
(1, 2, '2013-04-20 01:33:25', '2013-04-20 18:00:00', 'Confirmed'),
(2, 13, '2013-05-15 19:28:41', NULL, NULL),
(3, 2, '2013-04-30 19:22:05', '2013-05-15 15:27:40', 'Confirmed'),
(4, 2, '2013-04-30 19:22:15', '2013-05-15 15:28:07', 'Confirmed'),
(11, 1, '2013-05-15 02:28:31', NULL, NULL),
(11, 2, '2013-05-15 02:28:27', '2013-05-14 22:36:10', 'Confirmed'),
(12, 1, '2013-05-15 02:31:01', NULL, NULL),
(12, 2, '2013-05-15 02:31:04', NULL, NULL),
(13, 2, '2013-05-15 04:48:11', NULL, NULL),
(14, 1, '2013-05-15 04:48:44', NULL, NULL),
(14, 2, '2013-05-15 04:48:42', NULL, NULL),
(14, 2, '2013-05-15 05:50:04', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `liked`
--

CREATE TABLE `liked` (
  `nid` int(5) NOT NULL,
  `liker` int(4) NOT NULL,
  PRIMARY KEY (`nid`,`liker`),
  KEY `liker` (`liker`),
  KEY `liker_2` (`liker`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `liked`
--

INSERT INTO `liked` (`nid`, `liker`) VALUES
(15, 1),
(16, 1),
(1, 2),
(8, 2),
(15, 2),
(18, 2),
(19, 2),
(1, 3),
(8, 3),
(9, 3),
(1, 4),
(1, 5),
(7, 5),
(14, 5),
(1, 11),
(8, 11),
(9, 11),
(13, 11),
(16, 11),
(1, 12),
(8, 12),
(14, 12),
(16, 12),
(35, 13);

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `lid` int(5) NOT NULL AUTO_INCREMENT,
  `longitude` double(12,8) DEFAULT NULL,
  `latitude` double(12,8) DEFAULT NULL,
  PRIMARY KEY (`lid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`lid`, `longitude`, `latitude`) VALUES
(1, -73.98701400, 40.69403000),
(2, -73.99708800, 40.72961700),
(3, -73.99743400, 40.72437300),
(4, -73.99752300, 40.72665900),
(5, -74.01232300, 40.62137200),
(14, -73.99710400, 40.72972300),
(15, -73.98693250, 40.69407410),
(16, -73.99753540, 40.72645140),
(17, -74.00170400, 40.72338400),
(18, -73.98513080, 40.75889540),
(19, -73.97110570, 40.77361540);

-- --------------------------------------------------------

--
-- Table structure for table `note`
--

CREATE TABLE `note` (
  `nid` int(5) NOT NULL AUTO_INCREMENT,
  `uid` int(4) DEFAULT NULL,
  `posttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `text` text,
  `hyperlink` varchar(200) DEFAULT NULL,
  `aid` int(4) DEFAULT NULL,
  `lid` int(5) DEFAULT NULL,
  `radius` int(6) DEFAULT NULL,
  `sch_id` int(3) DEFAULT NULL,
  `authority` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`nid`),
  KEY `uid` (`uid`),
  KEY `lid` (`lid`),
  KEY `sch_id` (`sch_id`),
  KEY `aid` (`aid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=40 ;

--
-- Dumping data for table `note`
--

INSERT INTO `note` (`nid`, `uid`, `posttime`, `text`, `hyperlink`, `aid`, `lid`, `radius`, `sch_id`, `authority`) VALUES
(1, 1, '2013-05-04 18:40:32', 'I love shopping!', 'www.shopping.com', 2, 2, 5, 4, 'everybody'),
(2, 1, '2013-05-04 18:40:43', 'lunch time!', 'www.lunch.com', 1, 1, 5, 3, 'friend'),
(6, 2, '2013-05-04 18:50:49', 'social time!', 'www.social.com', 3, 3, 8, 2, 'everybody'),
(7, 2, '2013-05-04 18:51:01', 'dinner time!', 'www.dinnertime.com', 1, 1, 10, 6, 'friend'),
(8, 4, '2013-05-07 15:43:11', 'sports time!', 'www.sports.com', 3, 3, 8, 4, 'everybody'),
(9, 5, '2013-05-04 18:51:33', 'social time!', 'www.social.com', 2, 2, 10, 6, 'everybody'),
(11, 2, '2013-05-15 02:27:07', 'lunch time!', 'www.lunch.com', 1, 15, 3, 16, 'friend'),
(12, 11, '2013-05-15 02:29:56', 'shopping time!', 'shopping.com', 2, 17, 5, 17, 'everybody'),
(13, 12, '2013-05-15 02:31:56', 'shopping', 'shopping.com', 2, 17, 5, 18, 'everybody'),
(14, 12, '2013-05-15 02:34:50', 'sporting time', 'sporting.com', 4, 16, 5, 19, 'everybody'),
(15, 11, '2013-05-15 02:35:55', 'sporting', 'sporting.com', 4, 14, 6, 19, 'everybody'),
(16, 2, '2013-05-15 02:40:06', 'playing time!', 'playing.com', 5, 18, 6, 20, 'everybody'),
(17, 11, '2013-05-15 02:47:38', 'take a relax', 'playing.com', 6, 19, 5, 22, 'everybody'),
(18, 12, '2013-05-15 02:48:32', 'playing!', 'playing.com', 6, 19, 5, 23, 'everybody'),
(19, 1, '2013-05-15 02:51:08', 'take a relax', 'relax.com', 6, 19, 6, 25, 'everybody'),
(21, 1, '2013-05-15 02:53:15', 'playing', 'playing.com', NULL, 19, 6, 28, 'everybody'),
(24, 1, '2013-05-15 02:59:08', 'playing', 'playing.com', 6, 19, 5, 32, 'everybody'),
(25, 1, '2013-05-15 02:59:41', 'playing', 'playing.com', NULL, 18, 10, 33, 'everybody'),
(35, 14, '2013-05-15 05:49:57', 'playing', 'playing.com', NULL, 19, 6, 42, 'everybody'),
(36, 2, '2013-05-15 13:59:35', 'sport time', 'sporting.com', 4, 16, 5, 43, 'everybody'),
(37, 2, '2013-05-15 14:02:05', 'social', 'social.com', 5, 18, 5, 44, 'everybody'),
(38, 2, '2013-05-15 19:31:15', 'this is testing ', 'test.com', 1, 15, 5, 45, 'everybody'),
(39, 1, '2013-05-15 19:39:03', 'this is test!!!', 'test.com', 1, 15, 5, 49, 'everybody');

-- --------------------------------------------------------

--
-- Table structure for table `note_tag`
--

CREATE TABLE `note_tag` (
  `nid` int(5) NOT NULL DEFAULT '0',
  `tid` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`nid`,`tid`),
  KEY `tid` (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `note_tag`
--

INSERT INTO `note_tag` (`nid`, `tid`) VALUES
(1, 1),
(8, 1),
(12, 1),
(13, 1),
(1, 2),
(12, 2),
(13, 2),
(14, 2),
(15, 2),
(19, 2),
(35, 2),
(39, 2),
(2, 3),
(9, 3),
(11, 3),
(39, 3),
(8, 4),
(16, 4),
(17, 4),
(18, 4),
(21, 4),
(24, 4),
(25, 4),
(35, 4),
(6, 5),
(7, 5),
(8, 5),
(9, 5),
(12, 5),
(37, 5),
(38, 5),
(6, 6),
(7, 6),
(9, 6),
(14, 7),
(15, 7),
(36, 7),
(38, 8);

-- --------------------------------------------------------

--
-- Table structure for table `receive`
--

CREATE TABLE `receive` (
  `uid` int(4) NOT NULL,
  `sid` int(3) NOT NULL DEFAULT '0',
  `curtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `aid` int(4) DEFAULT NULL,
  `curlocation` int(6) DEFAULT NULL,
  PRIMARY KEY (`uid`,`sid`,`curtime`),
  KEY `sid` (`sid`),
  KEY `curlocation` (`curlocation`),
  KEY `aid` (`aid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `receive`
--

INSERT INTO `receive` (`uid`, `sid`, `curtime`, `aid`, `curlocation`) VALUES
(1, 2, '2013-05-18 13:00:00', 1, 15),
(1, 2, '2013-05-18 14:00:00', 1, 15),
(2, 1, '2013-05-18 13:00:00', 1, 15),
(2, 2, '2013-05-04 14:08:00', 2, 14),
(2, 2, '2013-05-04 14:56:50', 4, 4),
(2, 2, '2013-05-04 15:00:00', 6, 19),
(2, 2, '2013-05-04 17:37:00', 2, 17),
(2, 2, '2013-05-11 19:00:00', 2, 14),
(2, 2, '2013-05-11 20:00:00', 4, 16),
(2, 5, '2013-05-04 14:56:59', 1, 1),
(2, 6, '2013-05-16 13:10:00', 1, 15),
(3, 2, '2013-05-04 14:57:06', 3, 3),
(3, 2, '2013-05-04 16:17:01', NULL, 4),
(4, 6, '2013-05-04 14:57:15', 4, 3),
(5, 4, '2013-05-04 14:57:30', 2, 2);

-- --------------------------------------------------------

--
-- Stand-in structure for view `received`
--
CREATE TABLE `received` (
`nid` int(5)
,`poster` varchar(20)
,`posttime` timestamp
,`text` text
,`hyperlink` varchar(200)
,`uid` int(4)
,`state` varchar(40)
,`curtime` datetime
,`area` varchar(40)
,`longitude` double(12,8)
,`latitude` double(12,8)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `receive_view`
--
CREATE TABLE `receive_view` (
`uid` int(4)
,`state` varchar(40)
,`curtime` datetime
,`area` varchar(40)
,`longitude` double(12,8)
,`latitude` double(12,8)
,`sch_date` varchar(20)
,`starttime` time
,`endtime` time
,`f_area` varchar(40)
,`f_longitude` double(12,8)
,`f_latitude` double(12,8)
,`f_radius` int(6)
);
-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE `schedule` (
  `sch_id` int(4) NOT NULL AUTO_INCREMENT,
  `sch_date` varchar(20) NOT NULL,
  `starttime` time DEFAULT NULL,
  `endtime` time DEFAULT NULL,
  PRIMARY KEY (`sch_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=51 ;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`sch_id`, `sch_date`, `starttime`, `endtime`) VALUES
(1, 'everyday', '12:00:00', '18:00:00'),
(2, 'Fridays', '18:00:00', '22:00:00'),
(3, 'weekday', '11:30:00', '13:00:00'),
(4, 'weekend', '10:00:00', '22:00:00'),
(5, 'Mondays', '12:00:00', '15:00:00'),
(6, 'Saturdays', '16:00:00', '20:00:00'),
(7, 'Sundays', '13:00:00', '19:00:00'),
(11, 'everyday', '08:00:00', '15:53:40'),
(14, 'Sundays', '11:00:00', '13:00:00'),
(15, 'everyday', '20:00:00', '21:00:00'),
(16, 'everyday', '12:00:00', '13:00:00'),
(17, 'weekend', '13:00:00', '22:00:00'),
(18, 'weekend', '14:00:00', '22:00:00'),
(19, 'everyday', '19:00:00', '21:00:00'),
(20, 'Fridays', '19:00:00', '22:00:00'),
(21, 'Saturdays', '12:00:00', '22:00:00'),
(22, 'Saturdays', '14:00:00', '17:00:00'),
(23, 'Saturdays', '10:00:00', '16:00:00'),
(25, 'everyday', '16:00:00', '18:00:00'),
(28, 'Saturdays', '12:00:00', '18:00:00'),
(31, 'weekend', '15:00:00', '20:00:00'),
(32, 'Saturdays', '14:00:00', '18:00:00'),
(33, 'Sundays', '14:00:00', '19:00:00'),
(42, 'weekend', '12:00:00', '18:00:00'),
(43, 'everyday', '16:00:00', '17:30:00'),
(44, 'Sundays', '18:00:00', '21:00:00'),
(45, '2013-05-18', '08:00:00', '15:29:57'),
(46, '2013-05-18', '11:00:00', '15:32:34'),
(47, 'everyday', '09:00:00', '14:00:00'),
(48, 'everyday', '13:00:00', '15:00:00'),
(49, 'everyday', '10:00:00', '15:00:00'),
(50, 'everyday', '13:00:00', '14:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `state`
--

CREATE TABLE `state` (
  `sid` int(3) NOT NULL AUTO_INCREMENT,
  `state` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`sid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `state`
--

INSERT INTO `state` (`sid`, `state`) VALUES
(1, 'work'),
(2, 'relax'),
(3, 'lunch break'),
(4, 'dinner'),
(5, 'shopping'),
(6, 'social');

-- --------------------------------------------------------

--
-- Table structure for table `tag`
--

CREATE TABLE `tag` (
  `tid` int(3) NOT NULL AUTO_INCREMENT,
  `tag` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`tid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `tag`
--

INSERT INTO `tag` (`tid`, `tag`) VALUES
(1, 'shopping'),
(2, 'relax'),
(3, 'lunch'),
(4, 'playing'),
(5, 'me'),
(6, 'dinner'),
(7, 'sport'),
(8, 'test');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `uid` int(4) NOT NULL AUTO_INCREMENT,
  `email` varchar(40) DEFAULT NULL,
  `uname` varchar(20) DEFAULT NULL,
  `pw` varchar(20) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` varchar(40) DEFAULT NULL,
  `age` int(3) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `email_2` (`email`),
  UNIQUE KEY `uname` (`uname`),
  UNIQUE KEY `email_3` (`email`,`uname`),
  KEY `date` (`date`),
  KEY `date_2` (`date`),
  KEY `date_3` (`date`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`uid`, `email`, `uname`, `pw`, `date`, `name`, `age`, `phone`) VALUES
(1, 'vivienne@gmail.com', 'vivienne', '123456', '2013-04-29 23:47:51', 'Vivi', 22, '12345678'),
(2, 'vivi@gmail.com', 'vivian', '123456', '2013-04-19 05:25:55', 'Viv', 23, '987654321'),
(3, 'robin@gmail.com', 'robin', '123456', '2013-04-19 21:28:26', 'Zhaoran', 40, '9175156250'),
(4, 'aj@students.poly.edu', 'Micheal', 'Miller', '2013-04-21 18:28:16', 'Micheal Miller', 50, '9175136250'),
(5, 'kerr@students.poly.edu', 'kerr', 'smith', '2013-04-21 18:30:49', 'Miranda', 34, '9177777777'),
(11, 'wong@students.poly.edu', 'wong', '123456', '2013-05-15 02:27:48', 'wong', 23, '9175126520'),
(12, 'zhang@students.poly.edu', 'zhang', '123456', '2013-05-15 02:30:45', NULL, NULL, NULL),
(13, 'li@students.poly.edu', 'li', '123456', '2013-05-15 04:47:53', NULL, NULL, NULL),
(14, 'fang@students.poly.edu', 'fang', '123456', '2013-05-15 04:48:30', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_note`
--
CREATE TABLE `view_note` (
`nid` int(5)
,`poster` varchar(20)
,`posttime` timestamp
,`text` text
,`hyperlink` varchar(200)
,`tag` varchar(20)
,`area` varchar(40)
,`longitude` double(12,8)
,`latitude` double(12,8)
,`radius` int(6)
,`sch_date` varchar(20)
,`starttime` time
,`endtime` time
,`authority` varchar(20)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_receive`
--
CREATE TABLE `view_receive` (
`uid` int(4)
,`state` varchar(40)
,`tag` varchar(20)
,`curtime` datetime
,`area` varchar(40)
,`longitude` double(12,8)
,`latitude` double(12,8)
,`sch_date` varchar(20)
,`starttime` time
,`endtime` time
,`f_area` varchar(40)
,`f_longitude` double(12,8)
,`f_latitude` double(12,8)
,`f_radius` int(6)
);
-- --------------------------------------------------------

--
-- Structure for view `filters_view`
--
DROP TABLE IF EXISTS `filters_view`;

CREATE ALGORITHM=MERGE DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `filters_view` AS select `f`.`uid` AS `uid`,`f`.`fid` AS `fid`,`s`.`state` AS `state`,`sch`.`sch_date` AS `sch_date`,`sch`.`starttime` AS `starttime`,`sch`.`endtime` AS `endtime`,`a`.`area` AS `area`,`l`.`longitude` AS `longitude`,`l`.`latitude` AS `latitude`,`f`.`radius` AS `radius` from (((((`filters` `f` left join `location` `l` on((`f`.`lid` = `l`.`lid`))) left join `user` `u` on((`f`.`uid` = `u`.`uid`))) left join `state` `s` on((`f`.`sid` = `s`.`sid`))) left join `schedule` `sch` on((`f`.`sch_id` = `sch`.`sch_id`))) left join `area` `a` on((`f`.`aid` = `a`.`aid`)));

-- --------------------------------------------------------

--
-- Structure for view `received`
--
DROP TABLE IF EXISTS `received`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `received` AS select `n`.`nid` AS `nid`,`n`.`poster` AS `poster`,`n`.`posttime` AS `posttime`,`n`.`text` AS `text`,`n`.`hyperlink` AS `hyperlink`,`r`.`uid` AS `uid`,`r`.`state` AS `state`,`r`.`curtime` AS `curtime`,`r`.`area` AS `area`,`r`.`longitude` AS `longitude`,`r`.`latitude` AS `latitude` from (`view_note` `n` join `view_receive` `r`) where (`checkAuthority`(`getUID`(`n`.`poster`),`r`.`uid`,`n`.`authority`) and (((`n`.`area` = `r`.`area`) and (`n`.`area` is not null)) or (`getDistance`(`n`.`latitude`,`n`.`longitude`,`r`.`latitude`,`r`.`longitude`) <= `n`.`radius`)) and (((`r`.`f_area` = `r`.`area`) and (`r`.`f_area` is not null)) or (`getDistance`(`r`.`f_latitude`,`r`.`f_longitude`,`r`.`latitude`,`r`.`longitude`) <= `r`.`f_radius`)) and (`n`.`tag` = `r`.`tag`) and `curdatecheck`(`n`.`sch_date`,`r`.`curtime`) and `curdatecheck`(`r`.`sch_date`,`r`.`curtime`) and `timecheck`(`r`.`starttime`,`r`.`endtime`,`n`.`starttime`,`n`.`endtime`,`r`.`curtime`)) group by `n`.`nid`,`r`.`uid`,`r`.`curtime` order by `n`.`nid`;

-- --------------------------------------------------------

--
-- Structure for view `receive_view`
--
DROP TABLE IF EXISTS `receive_view`;

CREATE ALGORITHM=MERGE DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `receive_view` AS select `r`.`uid` AS `uid`,`state`.`state` AS `state`,`r`.`curtime` AS `curtime`,`a`.`area` AS `area`,`l`.`longitude` AS `longitude`,`l`.`latitude` AS `latitude`,`sch`.`sch_date` AS `sch_date`,`sch`.`starttime` AS `starttime`,`sch`.`endtime` AS `endtime`,`b`.`area` AS `f_area`,`m`.`longitude` AS `f_longitude`,`m`.`latitude` AS `f_latitude`,`f`.`radius` AS `f_radius` from (((((((`receive` `r` join `state` on((`r`.`sid` = `state`.`sid`))) join `filters` `f` on(((`r`.`uid` = `f`.`uid`) and (`r`.`sid` = `f`.`sid`)))) left join `area` `a` on((`r`.`aid` = `a`.`aid`))) left join `location` `l` on((`r`.`curlocation` = `l`.`lid`))) left join `schedule` `sch` on((`f`.`sch_id` = `sch`.`sch_id`))) left join `area` `b` on((`f`.`aid` = `b`.`aid`))) left join `location` `m` on((`f`.`lid` = `m`.`lid`)));

-- --------------------------------------------------------

--
-- Structure for view `view_note`
--
DROP TABLE IF EXISTS `view_note`;

CREATE ALGORITHM=MERGE DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_note` AS select `note`.`nid` AS `nid`,`user`.`uname` AS `poster`,`note`.`posttime` AS `posttime`,`note`.`text` AS `text`,`note`.`hyperlink` AS `hyperlink`,`tag`.`tag` AS `tag`,`area`.`area` AS `area`,`location`.`longitude` AS `longitude`,`location`.`latitude` AS `latitude`,`note`.`radius` AS `radius`,`schedule`.`sch_date` AS `sch_date`,`schedule`.`starttime` AS `starttime`,`schedule`.`endtime` AS `endtime`,`note`.`authority` AS `authority` from ((((((`note` left join `note_tag` on((`note`.`nid` = `note_tag`.`nid`))) left join `tag` on((`note_tag`.`tid` = `tag`.`tid`))) left join `user` on((`note`.`uid` = `user`.`uid`))) left join `location` on((`note`.`lid` = `location`.`lid`))) left join `schedule` on((`note`.`sch_id` = `schedule`.`sch_id`))) left join `area` on((`note`.`aid` = `area`.`aid`)));

-- --------------------------------------------------------

--
-- Structure for view `view_receive`
--
DROP TABLE IF EXISTS `view_receive`;

CREATE ALGORITHM=MERGE DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_receive` AS select `r`.`uid` AS `uid`,`state`.`state` AS `state`,`t`.`tag` AS `tag`,`r`.`curtime` AS `curtime`,`a`.`area` AS `area`,`l`.`longitude` AS `longitude`,`l`.`latitude` AS `latitude`,`sch`.`sch_date` AS `sch_date`,`sch`.`starttime` AS `starttime`,`sch`.`endtime` AS `endtime`,`b`.`area` AS `f_area`,`m`.`longitude` AS `f_longitude`,`m`.`latitude` AS `f_latitude`,`f`.`radius` AS `f_radius` from (((((`receive` `r` join `state` on((`r`.`sid` = `state`.`sid`))) left join `area` `a` on((`r`.`aid` = `a`.`aid`))) left join `location` `l` on((`r`.`curlocation` = `l`.`lid`))) join (((`filters` `f` left join `schedule` `sch` on((`f`.`sch_id` = `sch`.`sch_id`))) left join `area` `b` on((`f`.`aid` = `b`.`aid`))) left join `location` `m` on((`f`.`lid` = `m`.`lid`)))) join (`filters_tag` `ft` join `tag` `t` on((`ft`.`tid` = `t`.`tid`)))) where ((`r`.`uid` = `f`.`uid`) and (`r`.`sid` = `f`.`sid`) and (`f`.`fid` = `ft`.`fid`));

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookmark`
--
ALTER TABLE `bookmark`
  ADD CONSTRAINT `bookmark_ibfk_1` FOREIGN KEY (`nid`) REFERENCES `note` (`nid`),
  ADD CONSTRAINT `bookmark_ibfk_2` FOREIGN KEY (`uid`) REFERENCES `user` (`uid`);

--
-- Constraints for table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`nid`) REFERENCES `note` (`nid`),
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`uid`) REFERENCES `user` (`uid`);

--
-- Constraints for table `filters`
--
ALTER TABLE `filters`
  ADD CONSTRAINT `filters_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `user` (`uid`),
  ADD CONSTRAINT `filters_ibfk_2` FOREIGN KEY (`sid`) REFERENCES `state` (`sid`),
  ADD CONSTRAINT `filters_ibfk_3` FOREIGN KEY (`sch_id`) REFERENCES `schedule` (`sch_id`),
  ADD CONSTRAINT `filters_ibfk_4` FOREIGN KEY (`lid`) REFERENCES `location` (`lid`);

--
-- Constraints for table `filters_tag`
--
ALTER TABLE `filters_tag`
  ADD CONSTRAINT `filters_tag_ibfk_1` FOREIGN KEY (`fid`) REFERENCES `filters` (`fid`),
  ADD CONSTRAINT `filters_tag_ibfk_2` FOREIGN KEY (`tid`) REFERENCES `tag` (`tid`);

--
-- Constraints for table `friendrequest`
--
ALTER TABLE `friendrequest`
  ADD CONSTRAINT `friendrequest_ibfk_3` FOREIGN KEY (`uid`) REFERENCES `user` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `friendrequest_ibfk_4` FOREIGN KEY (`friend_id`) REFERENCES `user` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `liked`
--
ALTER TABLE `liked`
  ADD CONSTRAINT `liked_ibfk_1` FOREIGN KEY (`nid`) REFERENCES `note` (`nid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `liked_ibfk_2` FOREIGN KEY (`liker`) REFERENCES `user` (`uid`);

--
-- Constraints for table `note`
--
ALTER TABLE `note`
  ADD CONSTRAINT `note_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `user` (`uid`),
  ADD CONSTRAINT `note_ibfk_2` FOREIGN KEY (`lid`) REFERENCES `location` (`lid`),
  ADD CONSTRAINT `note_ibfk_3` FOREIGN KEY (`sch_id`) REFERENCES `schedule` (`sch_id`),
  ADD CONSTRAINT `note_ibfk_4` FOREIGN KEY (`aid`) REFERENCES `area` (`aid`);

--
-- Constraints for table `note_tag`
--
ALTER TABLE `note_tag`
  ADD CONSTRAINT `note_tag_ibfk_4` FOREIGN KEY (`nid`) REFERENCES `note` (`nid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `note_tag_ibfk_5` FOREIGN KEY (`tid`) REFERENCES `tag` (`tid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `receive`
--
ALTER TABLE `receive`
  ADD CONSTRAINT `receive_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `user` (`uid`),
  ADD CONSTRAINT `receive_ibfk_2` FOREIGN KEY (`sid`) REFERENCES `state` (`sid`),
  ADD CONSTRAINT `receive_ibfk_3` FOREIGN KEY (`curlocation`) REFERENCES `location` (`lid`),
  ADD CONSTRAINT `receive_ibfk_4` FOREIGN KEY (`aid`) REFERENCES `area` (`aid`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
