USE tv;

-- MySQL dump 10.13  Distrib 5.5.31, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: tv
-- ------------------------------------------------------
-- Server version	5.5.31-0ubuntu0.12.04.2

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
-- Table structure for table `clip_files`
--

DROP TABLE IF EXISTS `clip_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clip_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clip_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `file_ord` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clips`
--

DROP TABLE IF EXISTS `clips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clips` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `file` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `duration` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=230 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `restreamer`
--

DROP TABLE IF EXISTS `restreamer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `restreamer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `command` longtext COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `restreamer`
--

LOCK TABLES `restreamer` WRITE;
/*!40000 ALTER TABLE `restreamer` DISABLE KEYS */;
INSERT INTO `restreamer` VALUES (7,'web','{streamer_path}/bin/ffmpeg -report -loglevel verbose -i udp://{broadcast_ip}:{broadcast_port} -tune film -preset fast -s 512x288 -async 1 -acodec libmp3lame -ab 64k -ar 44100 -b:v 468k -r 20 -qscale 5 -threads 0 -f flv rtmp://localhost/live/sd',0),(8,'web2','{streamer_path}/bin/ffmpeg -report -loglevel verbose -i udp://{broadcast_ip}:{broadcast_port} -tune film -preset fast -s 512x288 -async 1 -acodec libmp3lame -ab 64k -ar 44100 -b:v 468k -r 20 -qscale 5 -threads 0 -f flv -metadata streamName=\"sd\" tcp://127.0.0.1:6666?pkt_size=1316',0),(9,'web3','{streamer_path}/bin/ffmpeg -report -loglevel verbose -i udp://{broadcast_ip}:{broadcast_port} -tune film -preset fast -s 1280x720 -async 1 -acodec libmp3lame -ab 96k -ar 44100 -b:v 4096k -r 20 -qscale 5 -threads 0 -f flv rtmp://localhost/live/sd',0),(10,'web4','{streamer_path}/bin/ffmpeg -report -loglevel verbose -i udp://{broadcast_ip}:{broadcast_port} -tune film -preset fast -s 1280x720 -async 1 -acodec libmp3lame -ab 96k -ar 44100 -b:v 4096k -r 20 -qscale 5 -threads 0 -f flv -metadata streamName=\"sd\" tcp://127.0.0.1:6666?pkt_size=1316',1),(11,'web5','{streamer_path}/bin/ffmpeg -report -loglevel verbose -i udp://{broadcast_ip}:{broadcast_port} -tune film -preset fast -s 1920x1080 -async 1 -acodec libmp3lame -ab 96k -ar 44100 -b:v 8192k -r 20 -qscale 5 -threads 0 -f flv -metadata streamName=\"sd\" tcp://127.0.0.1:6666?pkt_size=1316',0),(12,'iPhone','cd /home/tv/www/mobile/segments && {streamer_path}/bin/ffmpeg -re -i rtmp://87.120.9.38/live/sd -strict experimental -vcodec libx264 -s 320x240 -vf scale=320:240 -b:v 512k -minrate 256k -maxrate 768k -bufsize 2000k -c:a copy -flags -global_header -map 0 -map -0:d -f segment -segment_time 5 -segment_list iphone.m3u8 -segment_list_size 10 -segment_format mpegts stream%05d.ts',1),(13,'Android','/usr/bin/vlc-wrapper --ignore-config --daemon udp://@239.255.1.1:1234 --sout \'#transcode{width=320,height=240,vcodec=h264,vb=0,scale=0,acodec=mpga,ab=128,channels=2,samplerate=44100}:rtp{sdp=rtsp://87.120.9.38:8554/weekendtv.sdp}\' --sout-keep',1);
/*!40000 ALTER TABLE `restreamer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schedule`
--

DROP TABLE IF EXISTS `schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `file_id` int(11) NOT NULL,
  `ord` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2873 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `salt` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-09-13 18:22:17
