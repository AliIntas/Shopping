-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost:3307
-- Üretim Zamanı: 01 Oca 2025, 20:30:24
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
  `Supplier_id` int(11) DEFAULT NULL,
  `Campaign_id` int(11) DEFAULT NULL
) ;

--
-- Tablo döküm verisi `product`
--

INSERT INTO `product` (`Product_id`, `ProductName`, `ProductDescription`, `ProductPrice`, `ProductStock`, `Category_id`, `Supplier_id`, `Campaign_id`) VALUES
(8, 'victus', '512gb', 9000.00, 45, 1, 3, 3),
(21, 'PC -3060ti', '3060ti PC', 14000.00, 15, 1, 1, 3);

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

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`Product_id`),
  ADD KEY `PS_Suplier_id` (`Supplier_id`),
  ADD KEY `CC_Campaign_id` (`Campaign_id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `product`
--
ALTER TABLE `product`
  MODIFY `Product_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `CC_Campaign_id` FOREIGN KEY (`Campaign_id`) REFERENCES `campaign` (`Campaign_id`),
  ADD CONSTRAINT `PS_Suplier_id` FOREIGN KEY (`Supplier_id`) REFERENCES `supplier` (`Supplier_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
