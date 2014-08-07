/*
SQLyog Ultimate v9.51 
MySQL - 5.6.12-log : Database - motiv8_zf
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`motiv8_zf` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `motiv8_zf`;

/*Table structure for table `edm_records` */

DROP TABLE IF EXISTS `edm_records`;

CREATE TABLE `edm_records` (
  `id_licence` int(11) DEFAULT NULL,
  `total` int(10) DEFAULT NULL,
  `launch_date` char(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `edm_records` */

/*Table structure for table `user_login` */

DROP TABLE IF EXISTS `user_login`;

/*!50001 DROP VIEW IF EXISTS `user_login` */;
/*!50001 DROP TABLE IF EXISTS `user_login` */;

/*!50001 CREATE TABLE  `user_login`(
 `id` int(11) ,
 `fullname` varchar(50) ,
 `user` varchar(50) ,
 `password` varchar(150) ,
 `profile_id` varchar(20) ,
 `email` varchar(50) ,
 `position` char(100) ,
 `status` int(11) ,
 `id_client` int(11) 
)*/;

/*Table structure for table `vw_products` */

DROP TABLE IF EXISTS `vw_products`;

/*!50001 DROP VIEW IF EXISTS `vw_products` */;
/*!50001 DROP TABLE IF EXISTS `vw_products` */;

/*!50001 CREATE TABLE  `vw_products`(
 `id` int(11) ,
 `sku` varchar(50) ,
 `image` varchar(255) ,
 `name` varchar(200) ,
 `description` text ,
 `brand` varchar(100) ,
 `rrp` varchar(50) ,
 `id_supplier` int(11) ,
 `status` char(20) ,
 `id_subcategory` int(3) ,
 `price` decimal(8,2) ,
 `price_nogst` decimal(8,2) ,
 `freight_cost` decimal(8,2) ,
 `last_update_csv` datetime ,
 `imgSource` int(1) ,
 `id_category` int(2) 
)*/;

/*Table structure for table `vw_wash` */

DROP TABLE IF EXISTS `vw_wash`;

/*!50001 DROP VIEW IF EXISTS `vw_wash` */;
/*!50001 DROP TABLE IF EXISTS `vw_wash` */;

/*!50001 CREATE TABLE  `vw_wash`(
 `id` int(11) ,
 `sku` varchar(50) ,
 `image` varchar(255) ,
 `name` varchar(200) ,
 `description` text ,
 `brand` varchar(100) ,
 `rrp` varchar(50) ,
 `id_supplier` int(11) ,
 `status` char(20) ,
 `id_subcategory` int(3) ,
 `price` decimal(8,2) ,
 `price_nogst` decimal(8,2) ,
 `freight_cost` decimal(8,2) ,
 `last_update_csv` datetime ,
 `imgSource` int(1) ,
 `id_category` int(2) 
)*/;

/*View structure for view user_login */

/*!50001 DROP TABLE IF EXISTS `user_login` */;
/*!50001 DROP VIEW IF EXISTS `user_login` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `user_login` AS select `user`.`id` AS `id`,`user`.`fullname` AS `fullname`,`user`.`user` AS `user`,`user`.`password` AS `password`,`user`.`profile_id` AS `profile_id`,`user`.`email` AS `email`,`user`.`position` AS `position`,`user`.`status` AS `status`,`user`.`id_client` AS `id_client` from `user` union select `program_participants`.`id_participant` AS `id_participant`,concat(`program_participants`.`first_name`,' ',`program_participants`.`last_name`) AS `concat(``first_name``,' ',``last_name``)`,`program_participants`.`email` AS `email`,`program_participants`.`password` AS `password`,3 AS `3`,`program_participants`.`email` AS `email`,`program_participants`.`position` AS `position`,`program_participants`.`status` AS `status`,`program_participants`.`id_client` AS `id_client` from `program_participants` */;

/*View structure for view vw_products */

/*!50001 DROP TABLE IF EXISTS `vw_products` */;
/*!50001 DROP VIEW IF EXISTS `vw_products` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_products` AS select distinct `a`.`id` AS `id`,`a`.`sku` AS `sku`,`a`.`image` AS `image`,`a`.`name` AS `name`,`a`.`description` AS `description`,`a`.`brand` AS `brand`,max(`a`.`rrp`) AS `rrp`,`a`.`id_supplier` AS `id_supplier`,`a`.`status` AS `status`,`b`.`id_subcategory` AS `id_subcategory`,`a`.`price` AS `price`,`a`.`price_nogst` AS `price_nogst`,`a`.`freight_cost` AS `freight_cost`,`a`.`last_update` AS `last_update_csv`,`a`.`imgSource` AS `imgSource`,`e`.`id_category` AS `id_category` from ((`products_temp` `a` join `csvcategory_m8category` `b` on((`a`.`subcategory` = `b`.`csvsubcategory`))) join `subcategories` `e` on((`e`.`id_subcategory` = `b`.`id_subcategory`))) where (`a`.`subcategory` in (select `b`.`csvsubcategory` from `csvcategory_m8category` `b`) and (`b`.`id_subcategory` <> 0)) group by `a`.`sku` order by `a`.`rrp` desc */;

/*View structure for view vw_wash */

/*!50001 DROP TABLE IF EXISTS `vw_wash` */;
/*!50001 DROP VIEW IF EXISTS `vw_wash` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_wash` AS select `a`.`id` AS `id`,`a`.`sku` AS `sku`,`a`.`image` AS `image`,`a`.`name` AS `name`,`a`.`description` AS `description`,`a`.`brand` AS `brand`,`a`.`rrp` AS `rrp`,`a`.`id_supplier` AS `id_supplier`,`a`.`status` AS `status`,`a`.`id_subcategory` AS `id_subcategory`,`a`.`price` AS `price`,`a`.`price_nogst` AS `price_nogst`,`a`.`freight_cost` AS `freight_cost`,`a`.`last_update_csv` AS `last_update_csv`,`a`.`imgSource` AS `imgSource`,`a`.`id_category` AS `id_category` from (`vw_products` `a` join `csvcategory_m8category` `b` on((`a`.`id_subcategory` = `b`.`id_subcategory`))) where (`a`.`id_subcategory` in (select `b`.`id_subcategory` from `csvcategory_m8category` `b`) and (`b`.`id_subcategory` <> 0) and `a`.`sku` in (select `c`.`sku` from (`vw_products` `c` left join `products` `d` on((`c`.`sku` = `d`.`sku`))) where ((`c`.`last_update_csv` <> `d`.`last_update`) or (not(`c`.`sku` in (select `f`.`sku` from `products` `f`)))))) group by `a`.`sku` order by `a`.`rrp` desc */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
