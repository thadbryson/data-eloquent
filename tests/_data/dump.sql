--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) AUTO_INCREMENT NOT NULL UNIQUE ,
  `name` varchar(100) DEFAULT NULL,
  `age` smallint(5) unsigned DEFAULT '13'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;

INSERT INTO `users` (`NAME`, `age`) VALUES ('Jim',22),
('Timmy',10),
('Jen',73),
('Chad',40),
('Zeke',34),
('Bob',21),
('Joe',64),
('Judy',12);

UNLOCK TABLES;
