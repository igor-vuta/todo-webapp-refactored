-- MySQL dump 10.13  Distrib 9.2.0, for macos15.2 (arm64)
--
-- Host: localhost    Database: todo_app
-- ------------------------------------------------------
-- Server version	9.2.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `group_users`
--

LOCK TABLES `group_users` WRITE;
/*!40000 ALTER TABLE `group_users` DISABLE KEYS */;
INSERT INTO `group_users` VALUES (1,1,'2025-03-31 20:09:40'),(1,5,'2025-03-31 20:09:40'),(2,1,'2025-03-31 20:09:40'),(2,4,'2025-03-31 20:09:40'),(3,1,'2025-03-31 20:09:40'),(3,2,'2025-03-31 20:09:40'),(3,3,'2025-03-31 20:09:40'),(3,4,'2025-03-31 20:09:40'),(3,5,'2025-03-31 20:09:40'),(11,1,'2025-03-31 20:13:09'),(11,10,'2025-03-31 20:13:09');
/*!40000 ALTER TABLE `group_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `groups`
--

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
INSERT INTO `groups` VALUES (1,'Baldwin Group',4,'2025-03-31 20:09:40'),(2,'Perry, Curtis and Smith',5,'2025-03-31 20:09:40'),(3,'Hale and Sons',3,'2025-03-31 20:09:40'),(11,'test',10,'2025-03-31 20:13:09');
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `lists`
--

LOCK TABLES `lists` WRITE;
/*!40000 ALTER TABLE `lists` DISABLE KEYS */;
INSERT INTO `lists` VALUES (1,'Education List',2,1,'2025-03-31 20:09:40'),(2,'Dog List',2,1,'2025-03-31 20:09:40'),(3,'Public List',5,3,'2025-03-31 20:09:40'),(4,'Line List',1,NULL,'2025-03-31 20:09:40'),(5,'Human List',3,3,'2025-03-31 20:09:40'),(6,'See List',1,2,'2025-03-31 20:09:40'),(7,'Type List',5,2,'2025-03-31 20:09:40'),(8,'Report List',1,2,'2025-03-31 20:09:40'),(9,'Out List',5,1,'2025-03-31 20:09:40'),(10,'Two List',5,2,'2025-03-31 20:09:40'),(18,'test',10,NULL,'2025-03-31 20:12:56');
/*!40000 ALTER TABLE `lists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `tasks`
--

LOCK TABLES `tasks` WRITE;
/*!40000 ALTER TABLE `tasks` DISABLE KEYS */;
INSERT INTO `tasks` VALUES (1,'All the nothing sign.','2025-04-27','in_progress',1,0,5,7,'2025-03-31 20:09:40','2025-03-31 20:09:40','17:10:15','20:58:11',2),(2,'Ask reduce land those.','2025-04-20','completed',1,0,3,3,'2025-03-31 20:09:40','2025-03-31 20:09:40','22:32:40','16:13:19',1),(3,'This manager.','2025-04-08','pending',1,0,5,5,'2025-03-31 20:09:40','2025-03-31 20:09:40','16:04:02','04:51:18',3),(4,'Could yourself plan base.','2025-04-28','pending',1,0,2,3,'2025-03-31 20:09:40','2025-03-31 20:09:40','03:15:13','02:21:44',NULL),(5,'Wear individual about add.','2025-04-29','pending',3,0,5,7,'2025-03-31 20:09:40','2025-03-31 20:09:40','14:20:29','03:40:21',2),(6,'Partner couple part cup few.','2025-04-26','completed',1,0,2,10,'2025-03-31 20:09:40','2025-03-31 20:09:40','03:09:00','00:06:37',3),(7,'Take however ball ever.','2025-04-14','completed',2,0,4,8,'2025-03-31 20:09:40','2025-03-31 20:09:40','17:55:52','18:08:10',2),(8,'Technology card.','2025-04-18','pending',2,0,5,2,'2025-03-31 20:09:40','2025-03-31 20:09:40','07:19:12','02:05:45',3),(9,'Boy child surface amount.','2025-04-02','completed',3,0,3,4,'2025-03-31 20:09:40','2025-03-31 20:09:40','06:23:59','02:49:10',1),(10,'Fire happen nothing support suffer.','2025-04-27','pending',3,0,3,2,'2025-03-31 20:09:40','2025-03-31 20:09:40','21:25:36','02:34:43',1),(11,'Republican total policy head.','2025-04-19','in_progress',1,0,3,7,'2025-03-31 20:09:40','2025-03-31 20:09:40','09:07:28','23:50:20',NULL),(12,'Onto across character four.','2025-04-10','pending',1,0,2,1,'2025-03-31 20:09:40','2025-03-31 20:09:40','09:36:14','02:42:35',NULL),(13,'Single along especially change.','2025-04-21','pending',1,0,2,10,'2025-03-31 20:09:40','2025-03-31 20:09:40','07:52:59','09:10:51',NULL),(14,'Writer can boy room.','2025-03-26','in_progress',1,0,3,2,'2025-03-31 20:09:40','2025-03-31 20:09:40','09:26:01','15:43:31',NULL),(15,'Rock few structure federal board.','2025-04-20','completed',1,0,2,3,'2025-03-31 20:09:40','2025-03-31 20:09:40','14:27:13','10:47:16',NULL),(16,'Subject wish gas look.','2025-04-26','in_progress',1,0,1,1,'2025-03-31 20:09:40','2025-03-31 20:09:40','06:12:25','14:13:57',3),(17,'Few same.','2025-03-31','completed',1,0,3,2,'2025-03-31 20:09:40','2025-03-31 20:09:40','18:37:47','18:11:28',1),(18,'Heart window police assume be.','2025-04-29','pending',3,0,3,6,'2025-03-31 20:09:40','2025-03-31 20:09:40','10:39:54','01:04:02',3),(19,'Account hour million large.','2025-04-16','pending',1,0,5,8,'2025-03-31 20:09:40','2025-03-31 20:09:40','00:34:04','11:41:58',NULL),(20,'Institution happy write.','2025-04-05','completed',1,0,4,4,'2025-03-31 20:09:40','2025-03-31 20:09:40','22:05:28','22:18:01',2),(21,'Issue grow ask.','2025-04-27','in_progress',3,0,4,10,'2025-03-31 20:09:40','2025-03-31 20:09:40','11:20:40','05:17:29',1),(22,'Sell cut market either.','2025-04-24','completed',3,0,2,1,'2025-03-31 20:09:40','2025-03-31 20:09:40','10:28:42','07:26:36',1),(23,'Perhaps bit learn.','2025-04-10','pending',2,0,5,5,'2025-03-31 20:09:40','2025-03-31 20:09:40','15:05:03','10:28:10',NULL),(24,'Writer work.','2025-03-31','completed',2,0,2,1,'2025-03-31 20:09:40','2025-03-31 20:09:40','21:54:37','20:53:05',3),(25,'There many true follow.','2025-04-17','completed',2,0,5,9,'2025-03-31 20:09:40','2025-03-31 20:09:40','22:16:37','14:54:21',2),(26,'Myself use act.','2025-04-27','completed',2,0,4,5,'2025-03-31 20:09:40','2025-03-31 20:09:40','18:03:09','09:08:56',1),(27,'Along chance either six.','2025-04-11','completed',3,0,1,8,'2025-03-31 20:09:40','2025-03-31 20:09:40','06:08:07','21:54:35',NULL),(28,'At be than always.','2025-04-03','in_progress',3,0,1,9,'2025-03-31 20:09:40','2025-03-31 20:09:40','10:47:28','21:56:55',2),(29,'Address such former.','2025-03-31','pending',1,0,4,6,'2025-03-31 20:09:40','2025-03-31 20:09:40','03:36:53','22:17:00',2),(30,'Why measure too maybe.','2025-04-20','completed',2,0,5,10,'2025-03-31 20:09:40','2025-03-31 20:09:40','00:58:30','23:34:50',1),(31,'Serious wrong section town deal.','2025-04-18','completed',2,0,4,7,'2025-03-31 20:09:40','2025-03-31 20:09:40','15:18:59','11:39:21',NULL),(32,'Suddenly win parent do.','2025-03-24','pending',3,1,2,6,'2025-03-31 20:09:40','2025-03-31 20:39:56','10:32:53','23:34:54',1),(33,'Few now four.','2025-04-16','pending',1,0,4,7,'2025-03-31 20:09:40','2025-03-31 20:09:40','05:34:41','06:08:34',3),(34,'His himself clearly very.','2025-04-04','pending',2,0,5,7,'2025-03-31 20:09:40','2025-03-31 20:09:40','14:51:57','14:16:02',NULL),(35,'Area along individual man.','2025-04-22','pending',2,0,1,5,'2025-03-31 20:09:40','2025-03-31 20:09:40','19:45:11','16:00:38',1),(36,'Special good along.','2025-04-15','in_progress',3,0,4,9,'2025-03-31 20:09:40','2025-03-31 20:09:40','01:23:54','13:58:37',NULL),(37,'Big sing.','2025-03-24','pending',2,0,3,5,'2025-03-31 20:09:40','2025-03-31 20:09:40','22:24:12','20:00:16',3),(38,'Car food record power crime.','2025-04-16','pending',2,0,2,9,'2025-03-31 20:09:40','2025-03-31 20:09:40','20:14:39','07:19:50',NULL),(39,'Red pass value practice wide.','2025-04-27','completed',1,0,1,7,'2025-03-31 20:09:40','2025-03-31 20:09:40','19:47:22','17:22:06',3),(40,'When hold family.','2025-04-29','in_progress',1,0,2,1,'2025-03-31 20:09:40','2025-03-31 20:09:40','12:09:57','21:27:37',NULL),(41,'test','3212-03-12','pending',1,0,10,NULL,'2025-03-31 20:13:22','2025-03-31 20:13:22',NULL,NULL,11),(42,'test2','2027-03-12','pending',1,0,10,NULL,'2025-03-31 20:13:54','2025-03-31 20:13:54',NULL,NULL,11),(43,'test3','2028-03-01','in_progress',1,0,10,18,'2025-03-31 20:14:16','2025-03-31 21:18:02','00:00:00','00:00:00',NULL);
/*!40000 ALTER TABLE `tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'huffchad','michael20@mcdonald.biz','$2y$12$D.Q0dAqBYYgZK6d/bqD/c.EtrBztBkIg7Ft8qrBA7XSDFfpWmeKE6','2025-03-31 20:09:40'),(2,'jorgestrong','heidiharris@gmail.com','$2y$10$examplehashedpassword2','2025-03-31 20:09:40'),(3,'meganpeterson','carlsonscott@gmail.com','$2y$10$examplehashedpassword3','2025-03-31 20:09:40'),(4,'ruizrobert','paul42@hotmail.com','$2y$10$examplehashedpassword4','2025-03-31 20:09:40'),(5,'ellischristian','dennis75@roberts.org','$2y$10$examplehashedpassword5','2025-03-31 20:09:40'),(10,'test','test@test.com','$2y$12$YhExbfrdjAqx1MknjSh5iu8jB2vSnU5EfBxNtedQewe6psS.0BcM2','2025-03-31 20:12:42'),(11,'test2','test2@test.com','$2y$12$2zDh7F5v8foo/0dkutClme5lqQ7j4mWu1mEM1x9vcCt0Xx7H.Wfnm','2025-03-31 20:24:58');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-04-01  2:20:55
