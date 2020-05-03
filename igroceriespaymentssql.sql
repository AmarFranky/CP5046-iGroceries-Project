-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 27, 2020 at 09:56 AM
-- Server version: 10.4.6-MariaDB
-- PHP Version: 7.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `igroceries`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `fname` varchar(30) NOT NULL,
  `lname` varchar(30) NOT NULL,
  `email` varchar(30) NOT NULL,
  `password` varchar(30) NOT NULL,
  `admin_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`fname`, `lname`, `email`, `password`, `admin_id`) VALUES
('John', 'Jacobs', 'admin@igroceries.com', 'johnadmin', 1);

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `contact_id` int(10) NOT NULL,
  `uname` varchar(20) NOT NULL,
  `umessage` varchar(400) NOT NULL,
  `uemail` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(10) NOT NULL,
  `fname` varchar(30) NOT NULL,
  `lname` varchar(30) NOT NULL,
  `address` varchar(100) NOT NULL,
  `gender` varchar(1) NOT NULL,
  `contact` varchar(12) NOT NULL,
  `email` varchar(30) NOT NULL,
  `password` varchar(20) NOT NULL,
  `image` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `fname`, `lname`, `address`, `gender`, `contact`, `email`, `password`, `image`) VALUES
(2, 'dsf', 'sdf', 'sdf', 'M', '3434343434', 'abc@gmail.com', 'YWJjMTIz', 'sugar.jpg'),
(3, 'fdgd', 'gfdg', 'dfg', 'M', '3434343434', 'abc@gmail.com', 'YWJjMTIz', 'sugar.jpg'),
(4, 'dfg', 'fdg', 'fdg', 'M', '4545454545', 'abc@gmail.com', 'YWJjMTIz', 'sugar.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `inventory_id` int(10) NOT NULL,
  `item_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `item_id` int(10) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `item_category` varchar(30) NOT NULL,
  `item_price` double NOT NULL,
  `item_description` varchar(500) NOT NULL,
  `item_image` varchar(100) NOT NULL,
  `item_stock` int(20) NOT NULL,
  `item_arrive` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `item_name`, `item_category`, `item_price`, `item_description`, `item_image`, `item_stock`, `item_arrive`) VALUES
(7, 'sugar1', 'Others', 30, 'sugar low fat', 'sugar.jpg', 5, '0000-00-00'),
(8, 'sugar2', 'Others', 10, 'high quality sugar', 'sugar.jpg', 0, '2020-04-03'),
(9, 'sugar3', 'Others', 20, 'high quality sugar', 'sugar.jpg', 0, '2020-04-01'),
(10, 'sugar4', 'Others', 30, 'high quality sugar', 'sugar.jpg', 0, '0000-00-00'),
(11, 'sugar5', 'Others', 40, 'high quality sugar', 'sugar.jpg', 0, '2020-04-04'),
(12, 'sugar6', 'Others', 50, 'high quality sugar', 'sugar.jpg', 0, '2020-04-05'),
(13, 'sugar7', 'Others', 60, 'high quality sugar', 'sugar.jpg', 0, '2020-04-06'),
(14, 'Red Bull Sugarfree Energy Drin', 'Beverages', 45, ' Red Bull Sugarfree - No Sugars, just Wings\r\nRed Bull Sugarfree is Red Bull Energy Drink, but sugar free\r\nOnly 5 calories per each Red Bull Sugarfree can of 250 ml\r\nRed Bull Sugarfree\'s formula contains high quality ingredients: Caffeine, Taurine, B-Group Vitamins, Aspartame & Acesulfame K, Alpine water ', 'redbull.png', 2, '2020-04-11'),
(15, 'Sunkist Orange Soft Drink', 'Beverages', 30, '\r\n    Sunkist is Australia\'s favourite orange soft drink\r\n    Sunkist has a fresh and tangy taste that consumers love\r\n    Contains no artificial colours and flavours\r\n', 'sunkist.png', 10, '2020-04-11'),
(16, 'bragg', 'Beverages', 6.5, '\r\n    Convenient refreshing drink with the added health benefits\r\n    Provides extra power to immune system\r\n    With added benefits of apple cider vinegar, acai berries and grapes\r\n    Filled with power and energy\r\n    Sweetened with natural stevia\r\n', 'bragg.png', 2, '2020-04-11'),
(17, 'Ocean Spray Pink Low Sugar Cra', 'Beverages', 5.17, '\r\n    Some cranberries, depending on the amount of sun exposure and air temperature remain a beautiful pink color.\r\n    Less than 1 gram of sugar and only 9 calories per serve.\r\n    Enjoy the surprisingly light & refreshing taste of pink cranberries over ice or with your favourite meal.\r\n    Free from artificial flavours, colours and preservatives.\r\n    Explore the collection of Ocean Spray Recipes\r\n', 'ocean.png', 10, '2020-04-11'),
(18, ' Australian Beef Bone Broth Concentrate - Neutral Flavour', 'Beverages', 25, ' TASTING IDEAS: Our beef broth is naturally flavoured so it is ideal for creating your own herb or spiced flavoured broth. BEEF is perfect to stir through your favourite dishes to add a natural and nutritious flavour boost to your meal. Easy to disguise in the children meals, giving them a boost of all important protein, vitamins and minerals. Our easy-to-digest tasty broth is easy to make, just add 1 teaspoon to a cup of 100 ml hot water for a beverage drink.\r\nGOOD FOR EVERYBODY: High quality n', 'bone.png', 20, '2020-04-11');

-- --------------------------------------------------------

--
-- Table structure for table `item_categories`
--

CREATE TABLE `item_categories` (
  `category_id` int(10) NOT NULL,
  `category_name` varchar(50) NOT NULL,
  `category_desc` varchar(50) NOT NULL,
  `category_image` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `item_categories`
--

INSERT INTO `item_categories` (`category_id`, `category_name`, `category_desc`, `category_image`) VALUES
(1, 'Beverages', 'null', 'null'),
(2, 'Bread/Bakery', 'null', 'null'),
(3, 'Frozen Foods', 'null', 'null'),
(4, 'Cleaners', 'null', 'null'),
(5, 'Pet Food', 'null', 'null'),
(6, 'Canned/Jarred Goods', 'null', 'null'),
(7, 'Dry/Baking Goods', 'null', 'null'),
(8, 'Dairy', 'null', 'null'),
(9, 'Personal Care', 'null', 'null'),
(10, 'Others', 'null', 'null');

-- --------------------------------------------------------

--
-- Table structure for table `item_gallery`
--

CREATE TABLE `item_gallery` (
  `image_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `card_num` bigint(20) NOT NULL,
  `card_cvc` int(5) NOT NULL,
  `card_exp_month` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `card_exp_year` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `item_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `item_number` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `item_price` float(10,2) NOT NULL,
  `item_price_currency` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `paid_amount` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `paid_amount_currency` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `txn_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `payment_status` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `full_address` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `pincode` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `city` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `state` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `country` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `name`, `email`, `card_num`, `card_cvc`, `card_exp_month`, `card_exp_year`, `item_name`, `item_number`, `item_price`, `item_price_currency`, `paid_amount`, `paid_amount_currency`, `txn_id`, `payment_status`, `created`, `modified`, `full_address`, `pincode`, `city`, `state`, `country`) VALUES
(1, 'happy', 'happy.assignus@gmail.com', 4242424242424242, 123, '12', '2024', 'Red Bull Sugarfree Energy Drin', '14', 45.00, 'inr', '45', 'inr', 'txn_1GcRjYL0XfkwR4mJafXboCsy', 'succeeded', '2020-04-27 09:50:32', '2020-04-27 09:50:32', 'khaiswadi', '388454', 'borsad', '', 'india');

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `transaction_id` int(10) NOT NULL,
  `item_id` int(10) NOT NULL,
  `customer_id` int(10) NOT NULL,
  `amount` varchar(10) NOT NULL,
  `method` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`contact_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`inventory_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `item_categories`
--
ALTER TABLE `item_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `item_gallery`
--
ALTER TABLE `item_gallery`
  ADD PRIMARY KEY (`image_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`transaction_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `contact_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `inventory_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `item_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `item_categories`
--
ALTER TABLE `item_categories`
  MODIFY `category_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `item_gallery`
--
ALTER TABLE `item_gallery`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `transaction_id` int(10) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
