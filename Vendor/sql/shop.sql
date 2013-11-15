-- phpMyAdmin SQL Dump
-- version 4.0.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 14, 2013 at 09:40 PM
-- Server version: 5.0.96-community-log
-- PHP Version: 5.3.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `shop`
--

-- --------------------------------------------------------

--
-- Table structure for table `catalog_items`
--

CREATE TABLE IF NOT EXISTS `catalog_items` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `short_description` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` float(10,2) NOT NULL,
  `sale` float(10,2) default NULL,
  `cost` float(10,2) default NULL,
  `min_quantity` int(4) default '1',
  `quantity_per_pack` int(11) default '1',
  `stock` int(6) default NULL,
  `filename` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL default '1',
  `hidden` tinyint(1) NOT NULL default '0',
  `unlimited` tinyint(1) NOT NULL default '0',
  `order` int(3) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=143 ;

-- --------------------------------------------------------

--
-- Table structure for table `catalog_item_categories`
--

CREATE TABLE IF NOT EXISTS `catalog_item_categories` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) default NULL,
  `lft` int(11) NOT NULL,
  `rght` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `catalog_item_count` int(4) default NULL,
  `active_catalog_item_count` int(4) default NULL,
  PRIMARY KEY  (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

-- --------------------------------------------------------

--
-- Table structure for table `catalog_item_categories_catalog_items`
--

CREATE TABLE IF NOT EXISTS `catalog_item_categories_catalog_items` (
  `id` int(11) NOT NULL auto_increment,
  `catalog_item_id` int(11) NOT NULL,
  `catalog_item_category_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `product_id` (`catalog_item_id`,`catalog_item_category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=119 ;

-- --------------------------------------------------------

--
-- Table structure for table `catalog_item_category_links`
--

CREATE TABLE IF NOT EXISTS `catalog_item_category_links` (
  `id` int(11) NOT NULL auto_increment,
  `model` varchar(25) NOT NULL,
  `model_id` int(11) NOT NULL,
  `catalog_item_category_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `model` (`model`,`model_id`,`catalog_item_category_id`),
  KEY `model_2` (`model`,`model_id`),
  KEY `catalog_item_category_id` (`catalog_item_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `catalog_item_images`
--

CREATE TABLE IF NOT EXISTS `catalog_item_images` (
  `id` int(11) NOT NULL auto_increment,
  `catalog_item_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `thumb` tinyint(1) NOT NULL default '0',
  `order` int(4) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `product_id` (`catalog_item_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1677 ;

-- --------------------------------------------------------

--
-- Table structure for table `catalog_item_options`
--

CREATE TABLE IF NOT EXISTS `catalog_item_options` (
  `id` int(11) NOT NULL auto_increment,
  `catalog_item_id` int(11) NOT NULL,
  `index` int(11) default NULL,
  `title` varchar(128) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `product_id` (`catalog_item_id`,`index`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Table structure for table `catalog_item_packages`
--

CREATE TABLE IF NOT EXISTS `catalog_item_packages` (
  `id` int(11) NOT NULL auto_increment,
  `catalog_item_parent_id` int(11) NOT NULL,
  `catalog_item_child_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `product_parent_id` (`catalog_item_parent_id`,`catalog_item_child_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=158 ;

-- --------------------------------------------------------

--
-- Table structure for table `handling_methods`
--

CREATE TABLE IF NOT EXISTS `handling_methods` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `pct` float(10,2) NOT NULL,
  `amt` float(10,2) NOT NULL,
  `active` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE IF NOT EXISTS `invoices` (
  `id` int(8) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `item_name` varchar(28) NOT NULL,
  `item_number` int(11) NOT NULL,
  `model` varchar(64) default NULL,
  `model_id` int(11) default NULL,
  `model_title` varchar(64) default NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `invoice_payment_method_id` int(11) default NULL,
  `first_name` varchar(128) default NULL,
  `last_name` varchar(128) default NULL,
  `addline1` varchar(255) default NULL,
  `addline2` varchar(255) default NULL,
  `city` varchar(128) default NULL,
  `state` varchar(2) default NULL,
  `zip` varchar(15) default NULL,
  `country` varchar(2) default NULL,
  `email` varchar(128) default NULL,
  `phone` varchar(25) default NULL,
  `amt` float(10,2) NOT NULL,
  `recur` int(4) NOT NULL default '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `paid` datetime default NULL,
  `paypal_payment_id` int(11) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `paypal_payment_id` (`paypal_payment_id`),
  KEY `invoice_payment_method_id` (`invoice_payment_method_id`),
  KEY `model` (`model`,`model_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=137347 ;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_payment_methods`
--

CREATE TABLE IF NOT EXISTS `invoice_payment_methods` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(127) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `invoice_id` int(11) default NULL,
  `paid` datetime default NULL,
  `sub_total` float(10,2) NOT NULL,
  `shipping` float(10,2) NOT NULL,
  `handling` float(10,2) NOT NULL,
  `auto_price` tinyint(1) NOT NULL default '1',
  `auto_shipping` tinyint(1) NOT NULL default '1',
  `auto_handling` tinyint(1) NOT NULL default '1',
  `promo_discount` float(10,2) NOT NULL,
  `total` float(10,2) NOT NULL,
  `shop_order_product_count` int(11) NOT NULL,
  `shipped` datetime default NULL,
  `shop_order_shipping_id` int(11) default NULL,
  `shipping_method_id` int(11) NOT NULL,
  `shipping_cost` float(10,2) NOT NULL,
  `tracking` varchar(255) default NULL,
  `first_name` varchar(128) NOT NULL,
  `last_name` varchar(128) NOT NULL,
  `location_name` varchar(128) NOT NULL,
  `addline1` varchar(128) NOT NULL,
  `addline2` varchar(128) NOT NULL,
  `city` varchar(128) NOT NULL,
  `state` varchar(2) NOT NULL,
  `zip` varchar(25) NOT NULL,
  `country` varchar(2) NOT NULL,
  `email` varchar(128) default NULL,
  `phone` varchar(128) default NULL,
  `same_billing` tinyint(1) NOT NULL default '1',
  `shipped_email` datetime default NULL,
  `paid_email` datetime default NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `canceled` tinyint(1) NOT NULL,
  `archived` tinyint(1) NOT NULL default '0',
  `note` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `invoice_id` (`invoice_id`),
  KEY `user_id` (`user_id`),
  KEY `shop_order_shipping_method_id` (`shipping_method_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4218 ;

-- --------------------------------------------------------

--
-- Table structure for table `orders_handling_methods`
--

CREATE TABLE IF NOT EXISTS `orders_handling_methods` (
  `id` int(11) NOT NULL auto_increment,
  `handling_method_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `title` varchar(128) default NULL,
  `amt` float(10,2) default NULL,
  `pct` float(10,2) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `handling_method_id` (`handling_method_id`,`order_id`),
  KEY `handling_method_id_2` (`handling_method_id`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4731 ;

-- --------------------------------------------------------

--
-- Table structure for table `orders_promo_codes`
--

CREATE TABLE IF NOT EXISTS `orders_promo_codes` (
  `id` int(11) NOT NULL auto_increment,
  `promo_code_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `title` varchar(128) default NULL,
  `code` varchar(25) NOT NULL,
  `amt` float(10,2) default NULL,
  `pct` float(10,2) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `product_promo_id` (`promo_code_id`,`order_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=85 ;

-- --------------------------------------------------------

--
-- Table structure for table `order_products`
--

CREATE TABLE IF NOT EXISTS `order_products` (
  `id` int(11) NOT NULL auto_increment,
  `order_id` int(11) NOT NULL,
  `parent_id` int(11) default NULL,
  `product_id` int(11) NOT NULL,
  `catalog_item_id` int(11) default NULL,
  `parent_product_id` int(11) default NULL,
  `product_inventory_id` int(11) default NULL,
  `title` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `package_quantity` int(4) default NULL,
  `price` float(10,2) default NULL,
  `shipping` float(10,2) default NULL,
  `sub_total` float(10,2) default NULL,
  `total` float(10,2) NOT NULL,
  `cost` float(10,2) default NULL,
  `archived` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `parent_product_id` (`parent_product_id`),
  KEY `shop_order_id` (`order_id`),
  KEY `product_id` (`catalog_item_id`),
  KEY `parent_shop_order_product_id` (`parent_id`),
  KEY `product_inventory_id` (`product_inventory_id`),
  KEY `product_id_2` (`product_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9943 ;

-- --------------------------------------------------------

--
-- Table structure for table `order_products_shipping_rules`
--

CREATE TABLE IF NOT EXISTS `order_products_shipping_rules` (
  `id` int(11) NOT NULL auto_increment,
  `shipping_rule_id` int(11) default NULL,
  `order_product_id` int(11) NOT NULL,
  `title` varchar(128) default NULL,
  `amt` float(10,2) default NULL,
  `pct` float(10,2) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `product_shipping_rule_id` (`shipping_rule_id`,`order_product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `paypal_payments`
--

CREATE TABLE IF NOT EXISTS `paypal_payments` (
  `id` int(11) NOT NULL auto_increment,
  `invoice` int(11) default NULL,
  `item_name` varchar(255) default NULL,
  `item_number` varchar(255) default NULL,
  `ptype` varchar(255) default NULL,
  `payment_status` varchar(255) default NULL,
  `pending_reason` varchar(28) default NULL,
  `reason_code` varchar(127) default NULL,
  `payment_date` varchar(28) default NULL,
  `payment_fee` float(10,2) default NULL,
  `payment_type` varchar(8) default NULL,
  `mc_gross` varchar(255) default NULL,
  `mc_currency` varchar(255) default NULL,
  `mc_fee` float(10,2) default NULL,
  `mc_handling` float(10,2) default NULL,
  `mc_shipping` float(10,2) default NULL,
  `memo` varchar(255) default NULL,
  `txn_id` varchar(255) default NULL,
  `receiver_email` varchar(255) default NULL,
  `first_name` varchar(64) default NULL,
  `last_name` varchar(64) default NULL,
  `business` varchar(127) NOT NULL,
  `address_street` varchar(255) default NULL,
  `address_city` varchar(255) default NULL,
  `address_state` varchar(64) default NULL,
  `address_zip` varchar(64) default NULL,
  `address_country` varchar(64) default NULL,
  `payer_id` varchar(13) default NULL,
  `payer_email` varchar(127) default NULL,
  `payer_business_name` varchar(127) default NULL,
  `auth_amount` varchar(28) default NULL,
  `auth_exp` varchar(28) default NULL,
  `auth_id` int(19) default NULL,
  `auth_status` varchar(28) default NULL,
  `amount` varchar(28) default NULL,
  `amount_per_cycle` varchar(28) default NULL,
  `initial_payment_amount` varchar(28) default NULL,
  `next_payment_date` varchar(28) default NULL,
  `outstanding_balance` varchar(28) default NULL,
  `payment_cycle` varchar(28) default NULL,
  `period_type` varchar(28) default NULL,
  `product_name` varchar(28) default NULL,
  `product_type` varchar(28) default NULL,
  `profile_status` varchar(28) default NULL,
  `recurring_payment_id` varchar(28) default NULL,
  `rp_invoice_id` varchar(127) default NULL,
  `time_created` varchar(28) default NULL,
  `modified` datetime NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `txn_id` (`txn_id`),
  KEY `invoice` (`invoice`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=115554 ;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL auto_increment,
  `catalog_item_id` int(11) NOT NULL,
  `title` varchar(256) NOT NULL,
  `sub_title` varchar(256) NOT NULL,
  `product_option_choice_id_1` int(11) default NULL,
  `product_option_choice_id_2` int(11) default NULL,
  `product_option_choice_id_3` int(11) default NULL,
  `product_option_choice_id_4` int(11) default NULL,
  `active` tinyint(1) NOT NULL default '1',
  `stock` int(11) NOT NULL,
  `price` float(10,2) NOT NULL,
  `sale` float(10,2) default NULL,
  `cost` float(10,2) default NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `catalog_product_id` (`catalog_item_id`,`product_option_choice_id_1`,`product_option_choice_id_2`,`product_option_choice_id_3`,`product_option_choice_id_4`),
  KEY `catalog_product_id_2` (`catalog_item_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=51 ;

-- --------------------------------------------------------

--
-- Table structure for table `product_inventories`
--

CREATE TABLE IF NOT EXISTS `product_inventories` (
  `id` int(11) NOT NULL auto_increment,
  `product_id` int(11) NOT NULL,
  `title` varchar(64) NOT NULL,
  `quantity` int(6) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `product_id_2` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `product_inventory_adjustments`
--

CREATE TABLE IF NOT EXISTS `product_inventory_adjustments` (
  `id` int(11) NOT NULL auto_increment,
  `product_id` int(11) default NULL,
  `title` varchar(255) NOT NULL,
  `quantity` int(6) NOT NULL,
  `available` datetime default NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=294 ;

-- --------------------------------------------------------

--
-- Table structure for table `product_option_choices`
--

CREATE TABLE IF NOT EXISTS `product_option_choices` (
  `id` int(11) NOT NULL auto_increment,
  `catalog_item_option_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `product_option_id` (`catalog_item_option_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=45 ;

-- --------------------------------------------------------

--
-- Table structure for table `promo_codes`
--

CREATE TABLE IF NOT EXISTS `promo_codes` (
  `id` int(11) NOT NULL auto_increment,
  `code` varchar(128) NOT NULL,
  `title` varchar(255) NOT NULL,
  `amt` float(10,2) NOT NULL,
  `pct` float(2,2) NOT NULL,
  `active` tinyint(1) NOT NULL default '1',
  `started` date default NULL,
  `stopped` date default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `shipping_classes`
--

CREATE TABLE IF NOT EXISTS `shipping_classes` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `shipping_methods`
--

CREATE TABLE IF NOT EXISTS `shipping_methods` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(128) NOT NULL,
  `url` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `shipping_rules`
--

CREATE TABLE IF NOT EXISTS `shipping_rules` (
  `id` int(11) NOT NULL auto_increment,
  `catalog_item_id` int(11) default NULL,
  `order_class_id` int(11) default NULL,
  `min_quantity` int(11) default NULL,
  `max_quantity` int(11) default NULL,
  `pct` float(10,2) default '0.00',
  `per_item` float(10,2) default '0.00',
  `amt` float(10,2) default '0.00',
  `active` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `product_id` (`catalog_item_id`,`order_class_id`,`active`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=22 ;

-- --------------------------------------------------------

--
-- Table structure for table `shop_settings`
--

CREATE TABLE IF NOT EXISTS `shop_settings` (
  `name` varchar(36) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY  (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
         