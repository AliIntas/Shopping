-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost:3307
-- Üretim Zamanı: 19 Ara 2024, 20:14:34
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
-- Tablo için tablo yapısı `customer`
--

CREATE TABLE `customer` (
  `user_ID` int(11) DEFAULT NULL,
  `name` varchar(20) DEFAULT NULL,
  `surname` varchar(20) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone_number` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `customer`
--

INSERT INTO `customer` (`user_ID`, `name`, `surname`, `birth_date`, `address`, `phone_number`) VALUES
(6, 'Ahmet emre akın', 'Akın', '2002-06-21', 'Adıyaman Besni ', '05464654701'),
(14, 'volkan ', 'yalvarıcı', '2001-09-11', 'izmir', '07894561236'),
(16, 'muhammet', 'aksu', '2004-10-16', 'istanbul', '05465879022');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `supplier`
--

CREATE TABLE `supplier` (
  `user_ID` int(11) DEFAULT NULL,
  `company_name` varchar(100) DEFAULT NULL,
  `company_number` varchar(11) DEFAULT NULL,
  `company_adress` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `supplier`
--

INSERT INTO `supplier` (`user_ID`, `company_name`, `company_number`, `company_adress`) VALUES
(7, 'defactp', '2147483647', 'erzurum'),
(8, 'vodafone', '2147483647', 'erzurum cadde'),
(9, 'mavi', '2147483647', 'erzurum'),
(10, 'hp', '2147483647', 'ankara'),
(11, 'apple', '2147483647', 'adana'),
(12, 'desni', '05465657109', 'desni'),
(13, 'turk telekom', '05467891265', 'erzurum cadde'),
(15, 'deneme', 'deneme', 'deneme');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `user`
--

CREATE TABLE `user` (
  `user_ID` int(10) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `authorization` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `user`
--

INSERT INTO `user` (`user_ID`, `email`, `password`, `authorization`) VALUES
(1, 'ali.kaya@example.com', 'Ali123!', 1),
(2, 'ayse.yilmaz@example.', 'Ayse456!', 0),
(3, 'mehmet.celik@example', 'Mehmet789!', 0),
(4, 'zeynep.demir@example', 'Zeynep987!', 0),
(5, 'fatma.sahin@example.', 'Fatma654!', 0),
(6, 'emreakin@gmail.com', '56452asd.', 0),
(7, 'defacto@gmail.com', '123456789', 0),
(8, 'voda@gmail.com', '123456789', 0),
(9, 'mavi@gmail.com', '123456789', 0),
(10, 'hp@gmail.com', '12345679', 0),
(11, 'apple@gmail.com', '123456', 0),
(12, 'desni@gmail.com', '123456', 0),
(13, 'turktellekom@gmail.c', '123456', 0),
(14, 'volkanyalvarici@gmai', '123456', 1),
(15, 'deneme', 'deneme', 2),
(16, 'maksu3162@gmail.com', '123456', 1);

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `customer`
--
ALTER TABLE `customer`
  ADD KEY `user_ID` (`user_ID`);

--
-- Tablo için indeksler `supplier`
--
ALTER TABLE `supplier`
  ADD KEY `user_ID` (`user_ID`);

--
-- Tablo için indeksler `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_ID`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `user`
--
ALTER TABLE `user`
  MODIFY `user_ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `customer`
--
ALTER TABLE `customer`
  ADD CONSTRAINT `customer_ibfk_1` FOREIGN KEY (`user_ID`) REFERENCES `user` (`user_ID`);

--
-- Tablo kısıtlamaları `supplier`
--
ALTER TABLE `supplier`
  ADD CONSTRAINT `supplier_ibfk_1` FOREIGN KEY (`user_ID`) REFERENCES `user` (`user_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
