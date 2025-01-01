-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost:3307
-- Üretim Zamanı: 01 Oca 2025, 11:58:01
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `e-shopping`
--

DELIMITER $$
--
-- Yordamlar
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetRecentSales` (IN `supplierID` INT)   BEGIN
    SELECT 
        o.TotalPrice,
        o.Quantity
    FROM orders o
    WHERE o.Supplier_id = supplierID 
    AND o.OrderDate >= CURDATE() - INTERVAL 10 DAY;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ProcessReturnWithCursor` (IN `p_order_id` INT, IN `p_customer_id` INT, IN `p_return_reason` VARCHAR(255), IN `p_return_date` DATE)   BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE prod_id INT;
    DECLARE qty INT;
    DECLARE cur CURSOR FOR SELECT Product_id, Quantity FROM orders WHERE Order_id = p_order_id;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    -- İlgili sipariş kaydını güncelle
    UPDATE orders
    SET ReturnStatus = 1, ReturnReason = p_return_reason, ReturnDate = p_return_date
    WHERE Order_id = p_order_id AND Customer_id = p_customer_id;

    -- Cursor işlemi başlat
    OPEN cur;

    read_loop: LOOP
        FETCH cur INTO prod_id, qty;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Ürün stok miktarını artır
        UPDATE product
        SET ProductStock = ProductStock + qty
        WHERE Product_id = prod_id;
    END LOOP;

    CLOSE cur;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `campaign`
--

CREATE TABLE `campaign` (
  `Campaign_id` int(11) NOT NULL,
  `CampaignCategory` varchar(100) DEFAULT NULL,
  `CampaignDate` date DEFAULT NULL,
  `Discount` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `campaign`
--

INSERT INTO `campaign` (`Campaign_id`, `CampaignCategory`, `CampaignDate`, `Discount`) VALUES
(1, 'null', '2024-12-03', 0.00);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `cart`
--

CREATE TABLE `cart` (
  `Cart_id` int(11) NOT NULL,
  `Quantity` int(11) DEFAULT NULL,
  `Product_id` int(11) DEFAULT NULL,
  `Customer_id` int(11) DEFAULT NULL,
  `Campaign_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `cart`
--

INSERT INTO `cart` (`Cart_id`, `Quantity`, `Product_id`, `Customer_id`, `Campaign_id`) VALUES
(28, 6, 8, 6, NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `cashondelivery`
--

CREATE TABLE `cashondelivery` (
  `CoD_id` int(11) NOT NULL,
  `Payment_id` int(11) DEFAULT NULL,
  `PaymentCode` varchar(50) DEFAULT NULL,
  `Amount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `cashondelivery`
--

INSERT INTO `cashondelivery` (`CoD_id`, `Payment_id`, `PaymentCode`, `Amount`) VALUES
(10, 22, '277460', 9000.00),
(11, 24, '772191', 36000.00),
(12, 25, '781043', 27000.00),
(13, 26, '747840', 54000.00),
(14, 27, '252347', 54000.00);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `category`
--

CREATE TABLE `category` (
  `Category_id` int(11) NOT NULL,
  `CategoryName` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `category`
--

INSERT INTO `category` (`Category_id`, `CategoryName`) VALUES
(1, 'ELEKTRONİK'),
(2, 'GİYİM');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `customer`
--

CREATE TABLE `customer` (
  `Customer_id` int(11) NOT NULL,
  `User_id` int(11) DEFAULT NULL,
  `FirstName` varchar(100) DEFAULT NULL,
  `LastName` varchar(100) DEFAULT NULL,
  `Phone` varchar(15) DEFAULT NULL,
  `Address` text DEFAULT NULL,
  `BirthDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `customer`
--

INSERT INTO `customer` (`Customer_id`, `User_id`, `FirstName`, `LastName`, `Phone`, `Address`, `BirthDate`) VALUES
(6, 1, 'Ahmet emre ', 'Akın', '05465657109', 'Adıyaman Besni ', '2002-06-21'),
(7, 3, 'volkan', 'yalvarıcı', '05892362178', 'izmir', '2001-09-15'),
(8, 7, 'Ali', 'İntaş', '5555555554', 'Ordu altınordu ', '2024-12-01');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `favorites`
--

CREATE TABLE `favorites` (
  `Favorite_id` int(11) NOT NULL,
  `Product_id` int(11) DEFAULT NULL,
  `AddedDate` date DEFAULT NULL,
  `Notes` text DEFAULT NULL,
  `Customer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `favorites`
--

INSERT INTO `favorites` (`Favorite_id`, `Product_id`, `AddedDate`, `Notes`, `Customer_id`) VALUES
(1, 8, '2024-12-29', NULL, 6),
(3, 8, '2024-12-31', NULL, 8);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `notifications`
--

CREATE TABLE `notifications` (
  `Notification_id` int(11) NOT NULL,
  `Message` varchar(255) NOT NULL,
  `Product_id` int(11) NOT NULL,
  `Created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `notifications`
--

INSERT INTO `notifications` (`Notification_id`, `Message`, `Product_id`, `Created_at`) VALUES
(8, 'Stok kritik seviyede!', 21, '2024-12-31 10:14:46');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `onlinepayment`
--

CREATE TABLE `onlinepayment` (
  `Op_id` int(11) NOT NULL,
  `Payment_id` int(11) DEFAULT NULL,
  `CardNumber` varchar(16) DEFAULT NULL,
  `ExpiryDate` varchar(5) DEFAULT NULL,
  `CVV` varchar(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `onlinepayment`
--

INSERT INTO `onlinepayment` (`Op_id`, `Payment_id`, `CardNumber`, `ExpiryDate`, `CVV`) VALUES
(12, 23, '1111111111111111', '12/24', '111');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `orders`
--

CREATE TABLE `orders` (
  `Order_id` int(11) NOT NULL,
  `Supplier_id` int(11) DEFAULT NULL,
  `OrderDate` date DEFAULT NULL,
  `TotalPrice` decimal(10,2) DEFAULT NULL,
  `ReturnStatus` tinyint(1) DEFAULT NULL,
  `ReturnReason` text DEFAULT NULL,
  `ReturnDate` date DEFAULT NULL,
  `Payment_id` int(11) DEFAULT NULL,
  `Product_id` int(11) DEFAULT NULL,
  `Quantity` int(11) DEFAULT NULL,
  `Customer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `orders`
--

INSERT INTO `orders` (`Order_id`, `Supplier_id`, `OrderDate`, `TotalPrice`, `ReturnStatus`, `ReturnReason`, `ReturnDate`, `Payment_id`, `Product_id`, `Quantity`, `Customer_id`) VALUES
(8, 3, '2024-12-31', 9000.00, 1, 'yanlış aldım.', '2024-12-31', 22, 8, 1, 8),
(9, 1, '2024-12-31', 132123.00, 1, 'almadım.', '2024-12-31', 23, 21, 1, 8),
(10, 3, '2024-12-31', 36000.00, NULL, NULL, NULL, 24, 8, 4, 8),
(11, 3, '2025-01-01', 27000.00, 1, 'asda', '2025-01-01', 25, 8, 3, 6),
(12, 3, '2025-01-01', 54000.00, 1, 'sevmedim', '2025-01-01', 26, 8, 6, 6),
(13, 3, '2025-01-01', 54000.00, 1, 'sasdaksldşa', '2025-01-01', 27, 8, 6, 6);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `payment`
--

CREATE TABLE `payment` (
  `Payment_id` int(11) NOT NULL,
  `Cart_id` int(11) DEFAULT NULL,
  `PaymentStatus` varchar(50) DEFAULT NULL,
  `Customer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `payment`
--

INSERT INTO `payment` (`Payment_id`, `Cart_id`, `PaymentStatus`, `Customer_id`) VALUES
(22, NULL, '1', 8),
(23, NULL, '1', 8),
(24, NULL, '1', 8),
(25, NULL, '1', 6),
(26, NULL, '1', 6),
(27, NULL, '1', 6);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `product`
--

CREATE TABLE `product` (
  `Product_id` int(11) NOT NULL,
  `ProductName` varchar(255) DEFAULT NULL,
  `ProductDescription` text DEFAULT NULL,
  `ProductPrice` decimal(10,2) DEFAULT NULL,
  `ProductStock` int(11) DEFAULT NULL,
  `Category_id` int(11) DEFAULT NULL,
  `Supplier_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `product`
--

INSERT INTO `product` (`Product_id`, `ProductName`, `ProductDescription`, `ProductPrice`, `ProductStock`, `Category_id`, `Supplier_id`) VALUES
(8, 'victus', '512gb', 9000.00, 46, 1, 3),
(21, 'PC', '2232323', 132123.00, 5, 1, 1);

--
-- Tetikleyiciler `product`
--
DELIMITER $$
CREATE TRIGGER `StockTrigger` AFTER UPDATE ON `product` FOR EACH ROW BEGIN
    -- Stok 10'un altına düştüğünde bildirim ekle
    IF NEW.ProductStock < 10 AND OLD.ProductStock >= 10 THEN
        INSERT INTO Notifications (Message, Product_id)
        VALUES ('Stok kritik seviyede!', NEW.Product_id);
    -- Stok 10'un üzerine çıktığında bildirim sil
    ELSEIF NEW.ProductStock >= 10 AND OLD.ProductStock < 10 THEN
        DELETE FROM Notifications WHERE Product_id = NEW.Product_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Görünüm yapısı durumu `product_view`
-- (Asıl görünüm için aşağıya bakın)
--
CREATE TABLE `product_view` (
`Product_id` int(11)
,`ProductName` varchar(255)
,`ProductPrice` decimal(10,2)
);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `services`
--

CREATE TABLE `services` (
  `Service_id` int(11) NOT NULL,
  `ServiceName` varchar(100) DEFAULT NULL,
  `ServicePrice` decimal(10,2) DEFAULT NULL,
  `ServiceDescription` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `shipment`
--

CREATE TABLE `shipment` (
  `Shipment_id` int(11) NOT NULL,
  `Order_id` int(11) DEFAULT NULL,
  `TrackingNumber` varchar(50) DEFAULT NULL,
  `Supplier_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `shipment`
--

INSERT INTO `shipment` (`Shipment_id`, `Order_id`, `TrackingNumber`, `Supplier_id`) VALUES
(12, 12, '465465', 3),
(13, 10, '46546565', 3),
(14, 13, '54654654', 3);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `supplier`
--

CREATE TABLE `supplier` (
  `Supplier_id` int(11) NOT NULL,
  `User_id` int(11) DEFAULT NULL,
  `CompanyName` varchar(255) DEFAULT NULL,
  `CompanyPhone` varchar(15) DEFAULT NULL,
  `CompanyAddress` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `supplier`
--

INSERT INTO `supplier` (`Supplier_id`, `User_id`, `CompanyName`, `CompanyPhone`, `CompanyAddress`) VALUES
(1, 2, 'turkcell', '05464654701', 'erzurum'),
(3, 5, 'hp', '05465657109', 'erzurum'),
(4, 6, 'hp1', '12345', '12345');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `user`
--

CREATE TABLE `user` (
  `User_id` int(11) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `user`
--

INSERT INTO `user` (`User_id`, `Email`, `Password`) VALUES
(1, 'emreakin@gmail.com', '12345'),
(2, 'turkcell@gmail.com', '12345'),
(3, 'volkanyalvarici@gmail.com', '12345'),
(5, 'hp@gmail.com', '12345'),
(6, 'hp1@gmail.com', '12345'),
(7, 'ali@intas.com', '123');

-- --------------------------------------------------------

--
-- Görünüm yapısı `product_view`
--
DROP TABLE IF EXISTS `product_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `product_view`  AS SELECT `product`.`Product_id` AS `Product_id`, `product`.`ProductName` AS `ProductName`, `product`.`ProductPrice` AS `ProductPrice` FROM `product` ;

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `campaign`
--
ALTER TABLE `campaign`
  ADD PRIMARY KEY (`Campaign_id`);

--
-- Tablo için indeksler `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`Cart_id`),
  ADD KEY `Product_id` (`Product_id`),
  ADD KEY `CC_Customer_id` (`Customer_id`),
  ADD KEY `CC_Campaign_id` (`Campaign_id`);

--
-- Tablo için indeksler `cashondelivery`
--
ALTER TABLE `cashondelivery`
  ADD PRIMARY KEY (`CoD_id`),
  ADD KEY `Payment_id` (`Payment_id`);

--
-- Tablo için indeksler `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`Category_id`);

--
-- Tablo için indeksler `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`Customer_id`),
  ADD KEY `User_id` (`User_id`);

--
-- Tablo için indeksler `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`Favorite_id`),
  ADD KEY `Product_id` (`Product_id`),
  ADD KEY `FC_Customer_id` (`Customer_id`);

--
-- Tablo için indeksler `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`Notification_id`),
  ADD KEY `notifications_ibfk_1` (`Product_id`);

--
-- Tablo için indeksler `onlinepayment`
--
ALTER TABLE `onlinepayment`
  ADD PRIMARY KEY (`Op_id`),
  ADD KEY `Payment_id` (`Payment_id`);

--
-- Tablo için indeksler `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`Order_id`),
  ADD KEY `Supplier_id` (`Supplier_id`),
  ADD KEY `OP_Payment_id` (`Payment_id`),
  ADD KEY `OC_Customer_id` (`Customer_id`),
  ADD KEY `OP_Product_Order` (`Product_id`);

--
-- Tablo için indeksler `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`Payment_id`),
  ADD KEY `Cart_id` (`Cart_id`),
  ADD KEY `PC_Customer_id` (`Customer_id`);

--
-- Tablo için indeksler `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`Product_id`),
  ADD KEY `PS_Suplier_id` (`Supplier_id`);

--
-- Tablo için indeksler `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`Service_id`);

--
-- Tablo için indeksler `shipment`
--
ALTER TABLE `shipment`
  ADD PRIMARY KEY (`Shipment_id`),
  ADD KEY `Supplier_id` (`Supplier_id`),
  ADD KEY `shipment_ibfk_1` (`Order_id`);

--
-- Tablo için indeksler `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`Supplier_id`),
  ADD KEY `User_id` (`User_id`);

--
-- Tablo için indeksler `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`User_id`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `campaign`
--
ALTER TABLE `campaign`
  MODIFY `Campaign_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `cart`
--
ALTER TABLE `cart`
  MODIFY `Cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Tablo için AUTO_INCREMENT değeri `cashondelivery`
--
ALTER TABLE `cashondelivery`
  MODIFY `CoD_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Tablo için AUTO_INCREMENT değeri `category`
--
ALTER TABLE `category`
  MODIFY `Category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `customer`
--
ALTER TABLE `customer`
  MODIFY `Customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Tablo için AUTO_INCREMENT değeri `favorites`
--
ALTER TABLE `favorites`
  MODIFY `Favorite_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `notifications`
--
ALTER TABLE `notifications`
  MODIFY `Notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Tablo için AUTO_INCREMENT değeri `onlinepayment`
--
ALTER TABLE `onlinepayment`
  MODIFY `Op_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Tablo için AUTO_INCREMENT değeri `orders`
--
ALTER TABLE `orders`
  MODIFY `Order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Tablo için AUTO_INCREMENT değeri `payment`
--
ALTER TABLE `payment`
  MODIFY `Payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Tablo için AUTO_INCREMENT değeri `product`
--
ALTER TABLE `product`
  MODIFY `Product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Tablo için AUTO_INCREMENT değeri `services`
--
ALTER TABLE `services`
  MODIFY `Service_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `shipment`
--
ALTER TABLE `shipment`
  MODIFY `Shipment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Tablo için AUTO_INCREMENT değeri `supplier`
--
ALTER TABLE `supplier`
  MODIFY `Supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `user`
--
ALTER TABLE `user`
  MODIFY `User_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `CC_Campaign_id` FOREIGN KEY (`Campaign_id`) REFERENCES `campaign` (`Campaign_id`),
  ADD CONSTRAINT `CC_Customer_id` FOREIGN KEY (`Customer_id`) REFERENCES `customer` (`Customer_id`),
  ADD CONSTRAINT `Product_id` FOREIGN KEY (`Product_id`) REFERENCES `product` (`Product_id`);

--
-- Tablo kısıtlamaları `cashondelivery`
--
ALTER TABLE `cashondelivery`
  ADD CONSTRAINT `cashondelivery_ibfk_1` FOREIGN KEY (`Payment_id`) REFERENCES `payment` (`Payment_id`);

--
-- Tablo kısıtlamaları `customer`
--
ALTER TABLE `customer`
  ADD CONSTRAINT `customer_ibfk_1` FOREIGN KEY (`User_id`) REFERENCES `user` (`User_id`);

--
-- Tablo kısıtlamaları `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `FC_Customer_id` FOREIGN KEY (`Customer_id`) REFERENCES `customer` (`Customer_id`),
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`Product_id`) REFERENCES `product` (`Product_id`);

--
-- Tablo kısıtlamaları `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`Product_id`) REFERENCES `product` (`Product_id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `onlinepayment`
--
ALTER TABLE `onlinepayment`
  ADD CONSTRAINT `onlinepayment_ibfk_1` FOREIGN KEY (`Payment_id`) REFERENCES `payment` (`Payment_id`);

--
-- Tablo kısıtlamaları `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `OC_Customer_id` FOREIGN KEY (`Customer_id`) REFERENCES `customer` (`Customer_id`),
  ADD CONSTRAINT `OP_Payment_id` FOREIGN KEY (`Payment_id`) REFERENCES `payment` (`Payment_id`),
  ADD CONSTRAINT `OP_Product_Order` FOREIGN KEY (`Product_id`) REFERENCES `product` (`Product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`Supplier_id`) REFERENCES `supplier` (`Supplier_id`);

--
-- Tablo kısıtlamaları `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `PC_Customer_id` FOREIGN KEY (`Customer_id`) REFERENCES `customer` (`Customer_id`),
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`Cart_id`) REFERENCES `cart` (`Cart_id`);

--
-- Tablo kısıtlamaları `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `PS_Suplier_id` FOREIGN KEY (`Supplier_id`) REFERENCES `supplier` (`Supplier_id`);

--
-- Tablo kısıtlamaları `shipment`
--
ALTER TABLE `shipment`
  ADD CONSTRAINT `Supplier_id` FOREIGN KEY (`Supplier_id`) REFERENCES `supplier` (`Supplier_id`),
  ADD CONSTRAINT `shipment_ibfk_1` FOREIGN KEY (`Order_id`) REFERENCES `orders` (`Order_id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `supplier`
--
ALTER TABLE `supplier`
  ADD CONSTRAINT `supplier_ibfk_1` FOREIGN KEY (`User_id`) REFERENCES `user` (`User_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
