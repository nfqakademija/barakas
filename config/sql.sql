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
-- Table structure for table `academy`
--

DROP TABLE IF EXISTS `academy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `academy_type` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academy`
--

LOCK TABLES `academy` WRITE;
/*!40000 ALTER TABLE `academy` DISABLE KEYS */;
INSERT INTO `academy` VALUES (1,'Alytaus kolegija',1),(2,'Kauno kolegija',1),(3,'Kauno miškų ir aplinkos inžinerijos kolegija',1),(4,'Kauno technikos kolegija',1),(5,'Klaipėdos valstybinė kolegija',1),(6,'Lietuvos aukštoji jūreivystės mokykla',1),(7,'Marijampolės kolegija',1),(8,'Panevėžio kolegija',1),(9,'Šiaulių valstybinė kolegija',1),(10,'Utenos kolegija',1),(11,'Vilniaus kolegija',1),(12,'Vilniaus technologijų ir dizaino kolegija',1),(13,'V. A. Graičiūno aukštoji vadybos mokykla',1),(14,'Socialinių mokslų kolegija',1),(15,'Klaipėdos verslo kolegija',1),(16,'Kolpingo kolegija',1),(17,'Šiaurės Lietuvos kolegija',1),(18,'Šv. Ignaco Lojolos kolegija',1),(19,'Tarptautinė teisės ir verslo aukštoji mokykla',1),(20,'Vakarų Lietuvos verslo kolegija',1),(21,'Vilniaus verslo kolegija',1),(22,'Vilniaus dizaino kolegija',1),(23,'Vilniaus kooperacijos kolegija',1),(24,'Vilniaus Universitetas',0),(25,'Vilniaus Gedimino technikos universitetas',0),(26,'Vilniaus dailės akademija',0),(27,'Generolo Jono Žemaičio Lietuvos karo akademija',0),(28,'Lietuvos muzikos ir teatro akademija',0),(29,'Lietuvos sveikatos mokslų universitetas',0),(30,'Kauno technologijos universitetas',0),(31,'Lietuvos sporto universitetas',0),(32,'Mykolo Romerio universitetas',0),(33,'Vytauto Didžiojo universitetas',0),(34,'Šiaulių universitetas',0),(35,'Klaipėdos universitetas',0),(36,'ISM Vadybos ir ekonomikos universitetas',0),(37,'LCC tarptautinis universitetas',0),(38,'Kazimiero Simonavičiaus universitetas',0),(39,'Telšių Vyskupo Vincento Borisevičiaus kunigų seminarija',0),(40,'Europos Humanitarinis Universitetas',0),(41,'Vilniaus Šv. Juozapo kunigų seminarija',0);
/*!40000 ALTER TABLE `academy` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-11-08 18:53:58

