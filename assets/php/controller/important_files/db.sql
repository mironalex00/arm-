-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 18, 2022 at 05:14 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

CREATE OR REPLACE DATABASE `examen_dwse_primero`;

-- --------------------------------------------------------
USE `examen_dwse_primero`;
-- --------------------------------------------------------

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE OR REPLACE TABLE `user` (
  `userId` varchar(25) NOT NULL PRIMARY KEY,
  `userPass` varchar(75) NOT NULL,
  `userFullname` varchar(75) NOT NULL,
  `userSurname` varchar(75) NOT NULL,
  `userMail` varchar(255) NOT NULL,
  `userTerms` tinyint(1) NOT NULL DEFAULT 0,
  `userType` tinyint(1) NOT NULL DEFAULT 0,
  `accountStatus` tinyint(1) NOT NULL DEFAULT 0,
  `registrationUser` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`userId`, `userPass`, `userFullname`, `userSurname`, `userMail`, `userTerms`, `userType`, `accountStatus`, `registrationUser`) VALUES
('admin', '161ebd7d45089b3446ee4e0d86dbcf92', 'Administrador', 'Test', 'admin@admin.com', 1, 1, 1, '2022-11-17 14:39:15');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE OR REPLACE TABLE `categories` (
  `catId` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `catName` varchar(75) NOT NULL,
  `catDesc` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`catId`, `catName`, `catDesc`) VALUES
(1, 'Telefonía', 'Productos de informática relacionados con la telefonía'),
(2, 'Servicios', 'Servicios ofrecidos por nosotros'),
(3, 'Bicicletas', 'Productos relacionados con el ciclismo');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE OR REPLACE TABLE `orders` (
  `orderId` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `orderDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `orderStatus` tinyint(1) NOT NULL DEFAULT 0,
  `userId` varchar(50) NOT NULL,
  INDEX fk_order_userid (userId),
  CONSTRAINT `fk_order_userid` 
    FOREIGN KEY (`userId`) REFERENCES `user`(`userId`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`orderId`, `orderDate`, `orderStatus`, `userId`) VALUES
(1, '2022-11-17 17:36:51', 0, 'admin'),
(2, '2022-11-17 17:59:05', 1, 'admin'),
(3, '2022-11-18 22:28:03', 1, 'admin'),
(4, '2022-11-18 03:15:40', 1, 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE OR REPLACE TABLE `products` (
  `prodId` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `prodName` varchar(50) NOT NULL,
  `prodDesc` varchar(150) NOT NULL,
  `prodPrice` float(8,2) NOT NULL,
  `prodStock` smallint(6) NOT NULL,
  `catId` int(11) NOT NULL,
  CONSTRAINT `fk_products_catid` 
    FOREIGN KEY (`catId`) REFERENCES `categories` (`catId`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`prodId`, `prodName`, `prodDesc`, `prodPrice`, `prodStock`, `catId`) VALUES
(1, 'Samsung A5', 'Smartphone Samsung A5', 99.00, 200, 1),
(2, 'Reparación de Cristal + LCD Smartphone', 'Reparamos su dispositivo smartphone roto', 19.99, 999, 2),
(3, 'Samsung Galaxy S22 Ultra', 'Smartphone Samsung Galaxy S22 Ultra', 1011.45, 100, 1),
(4, 'Bicicleta eléctrica de trekking', 'Bicicleta eléctrica de trekking aluminio monoplato 8V Riverside 500 E gris', 999.99, 130, 3);

-- --------------------------------------------------------

--
-- Table structure for table `ordersproducts`
--

CREATE OR REPLACE TABLE `ordersproducts` (
  `orderProductId` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `orderId` int(11) NOT NULL,
  `prodId` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  CONSTRAINT `fk_ordersproducts_prodid` 
    FOREIGN KEY (`prodId`) REFERENCES `products` (`prodId`) 
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ordersproducts_ordeid` 
    FOREIGN KEY (`orderId`) REFERENCES `orders` (`orderId`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ordersproducts`
--

INSERT INTO `ordersproducts` (`orderProductId`, `orderId`, `prodId`, `quantity`) VALUES
(1, 1, 1, 5),
(2, 2, 2, 10),
(3, 3, 3, 62),
(4, 4, 4, 100);

-- --------------------------------------------------------

--
-- View structure for table `user_categories`
--

-- CREATE VIEW `user_categories` AS SELECT `c`.`catName` AS `category`, `c`.`catDesc` AS `description` FROM `categories` AS `c`;

-- --------------------------------------------------------

--
-- View structure for table `user_categories`
--

CREATE VIEW `user_orders` AS SELECT `u`.`userId` AS `user`, cast(`o`.`orderDate` as date) AS `date`, `p`.`prodName` AS `product`, `op`.`quantity` AS `quantity`,  `p`.`prodPrice` AS `price`, `o`.`orderStatus` AS `status` FROM (((`user` `u` join `products` `p`) join `orders` `o`) join `ordersproducts` `op`) WHERE `o`.`orderId` = `op`.`orderId` AND `p`.`prodId` = `op`.`prodId` AND `u`.`userId` = `o`.`userId`;
-- --------------------------------------------------------

--
-- View structure for table `user_products`
--

CREATE VIEW `user_products` AS SELECT `p`.`prodName` AS `product`, `p`.`prodDesc` AS `description`, `p`.`prodPrice` AS `price`, `p`.`prodStock` AS `stock`, `c`.`catName` AS `category` FROM (`products` `p` join `categories` `c`) WHERE `p`.`catId` = `c`.`catId`;
-- --------------------------------------------------------

--
-- View structure for table `user_categories`
--

CREATE VIEW `user_categories` AS select `c`.`catId` AS `id`, `c`.`catName` AS `category`, `c`.`catDesc` AS `desc` from `examen_dwse_primero`.`categories` `c` where `c`.`catName` in (select distinct `user_products`.`category` from `examen_dwse_primero`.`user_products` where `c`.`catName` = `user_products`.`category`);

-- --------------------------------------------------------

--
-- Procedure `reset_autoincrement`
--

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_reset_table`(IN tablename varchar(200))
BEGIN
	SET @ALT_TBL = CONCAT('ALTER TABLE ',tablename,' AUTO_INCREMENT=1, ALGORITHM=INPLACE;');
	PREPARE stmt FROM @ALT_TBL;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
END $$
DELIMITER ;

-- --------------------------------------------------------

--
-- Trigger `user_order`
--

DELIMITER $$
CREATE DEFINER=`root`@`localhost` TRIGGER `user_order` BEFORE INSERT ON `ordersproducts` FOR EACH ROW 
	BEGIN
    DECLARE quantity integer;
		DECLARE error_msg VARCHAR(255);
    SELECT p.prodStock - NEW.quantity INTO quantity FROM products p WHERE p.prodId = NEW.prodId;
    SET @ordersize = (
      SELECT COUNT(*) FROM ordersproducts WHERE orderId = (
        SELECT orderId FROM orders AS o WHERE 
          DATE_FORMAT(o.orderDate, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d') 
          AND o.orderId = NEW.orderId
      )
    );
    IF quantity < 0 THEN  
      set error_msg = 'La cantidad del stock solicitado supera la cantidad actual';
      IF @ORDERSIZE = 0 THEN
        set error_msg = CONCAT(error_msg, ', ', 'pedido cancelado');
      END IF;
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = error_msg;
    ELSE
      UPDATE `products` SET `prodStock`= `prodStock` - NEW.quantity WHERE products.prodId = NEW.prodId;        
    END IF;
	END$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Events
--

DELIMITER $$
CREATE DEFINER=`root`@`localhost` EVENT `fix_app` ON SCHEDULE EVERY 1 MINUTE STARTS CURRENT_TIMESTAMP() ON COMPLETION NOT PRESERVE ENABLE 
  DO BEGIN
    SIGNAL SQLSTATE '01000' SET MESSAGE_TEXT = 'fix_app Ha iniciado rutina';
     DELETE FROM `orders` WHERE orderId NOT IN (SELECT DISTINCT orderId FROM ordersproducts);
    SIGNAL SQLSTATE '01000' SET MESSAGE_TEXT = 'fix_app Ha eliminado entradas innecesarias de "orders"';
     CALL sp_reset_table('categories');
    SIGNAL SQLSTATE '01000' SET MESSAGE_TEXT = 'fix_app Ha restaurado AUTO_INCREMENT de "categories"';
     CALL sp_reset_table('orders');
    SIGNAL SQLSTATE '01000' SET MESSAGE_TEXT = 'fix_app Ha restaurado AUTO_INCREMENT de "orders"';
     CALL sp_reset_table('products');
    SIGNAL SQLSTATE '01000' SET MESSAGE_TEXT = 'fix_app Ha restaurado AUTO_INCREMENT de "products"';
    SIGNAL SQLSTATE '01000' SET MESSAGE_TEXT = 'fix_app Ha terminado rutina';
  END$$
DELIMITER ;

-- --------------------------------------------------------
--
--	Saving changes
--
SET GLOBAL event_scheduler="ON";
COMMIT;