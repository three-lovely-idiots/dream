-- phpMyAdmin SQL Dump
-- version phpStudy 2014
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2017 �?09 �?01 �?12:33
-- 服务器版本: 5.5.53
-- PHP 版本: 5.6.27

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `zerg`
--

-- --------------------------------------------------------

--
-- 表的结构 `banner`
--

CREATE TABLE IF NOT EXISTS `banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL COMMENT 'Banner名称，通常作为标识',
  `description` varchar(255) DEFAULT NULL COMMENT 'Banner描述',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COMMENT='banner管理表' AUTO_INCREMENT=6 ;

--
-- 转存表中的数据 `banner`
--

INSERT INTO `banner` (`id`, `name`, `description`, `delete_time`, `update_time`) VALUES
(1, '首页置顶', '首页轮播图', NULL, NULL),
(2, 'newtest', '这是我得newtest\r\n                                        ', NULL, 1503462490),
(5, 'newtest3', 'newtest3\r\n                                        ', NULL, 1503481295);

-- --------------------------------------------------------

--
-- 表的结构 `banner_item`
--

CREATE TABLE IF NOT EXISTS `banner_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `img_id` int(11) NOT NULL COMMENT '外键，关联image表',
  `key_word` varchar(100) NOT NULL COMMENT '执行关键字，根据不同的type含义不同',
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '跳转类型，可能导向商品，可能导向专题，可能导向其他。0，无导向；1：导向商品;2:导向专题',
  `delete_time` int(11) DEFAULT NULL,
  `banner_id` int(11) NOT NULL COMMENT '外键，关联banner表',
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COMMENT='banner子项表' AUTO_INCREMENT=20 ;

--
-- 转存表中的数据 `banner_item`
--

INSERT INTO `banner_item` (`id`, `img_id`, `key_word`, `type`, `delete_time`, `banner_id`, `update_time`) VALUES
(1, 65, '6', 1, NULL, 1, NULL),
(2, 2, '25', 1, NULL, 1, NULL),
(3, 3, '11', 1, NULL, 1, NULL),
(5, 1, '10', 1, NULL, 1, NULL),
(6, 70, '1', 0, NULL, 2, NULL),
(7, 71, '1', 1, NULL, 2, NULL),
(8, 72, '2', 0, NULL, 2, NULL),
(19, 99, '32', 0, NULL, 5, NULL),
(18, 98, '4', 0, NULL, 5, NULL),
(17, 77, '2', 0, NULL, 5, NULL),
(16, 76, '1', 0, NULL, 5, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '分类名称',
  `topic_img_id` int(11) DEFAULT NULL COMMENT '外键，关联image表',
  `delete_time` int(11) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL COMMENT '描述',
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COMMENT='商品类目' AUTO_INCREMENT=8 ;

--
-- 转存表中的数据 `category`
--

INSERT INTO `category` (`id`, `name`, `topic_img_id`, `delete_time`, `description`, `update_time`) VALUES
(2, '果味', 6, NULL, NULL, NULL),
(3, '蔬菜', 5, NULL, NULL, NULL),
(4, '炒货', 7, NULL, NULL, NULL),
(5, '点心', 4, NULL, NULL, NULL),
(6, '粗茶', 8, NULL, NULL, NULL),
(7, '淡饭', 9, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `image`
--

CREATE TABLE IF NOT EXISTS `image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL COMMENT '图片路径',
  `from` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1 来自本地，2 来自公网',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COMMENT='图片总表' AUTO_INCREMENT=162 ;

--
-- 转存表中的数据 `image`
--

INSERT INTO `image` (`id`, `url`, `from`, `delete_time`, `update_time`) VALUES
(1, '/banner-1a.png', 1, NULL, NULL),
(2, '/banner-2a.png', 1, NULL, NULL),
(3, '/banner-3a.png', 1, NULL, NULL),
(4, '/category-cake.png', 1, NULL, NULL),
(5, '/category-vg.png', 1, NULL, NULL),
(6, '/category-dryfruit.png', 1, NULL, NULL),
(7, '/category-fry-a.png', 1, NULL, NULL),
(8, '/category-tea.png', 1, NULL, NULL),
(9, '/category-rice.png', 1, NULL, NULL),
(10, '/product-dryfruit@1.png', 1, NULL, NULL),
(13, '/product-vg@1.png', 1, NULL, NULL),
(14, '/product-rice@6.png', 1, NULL, NULL),
(16, '/1@theme.png', 1, NULL, NULL),
(17, '/2@theme.png', 1, NULL, NULL),
(18, '/3@theme.png', 1, NULL, NULL),
(19, '/detail-1@1-dryfruit.png', 1, NULL, NULL),
(20, '/detail-2@1-dryfruit.png', 1, NULL, NULL),
(21, '/detail-3@1-dryfruit.png', 1, NULL, NULL),
(22, '/detail-4@1-dryfruit.png', 1, NULL, NULL),
(23, '/detail-5@1-dryfruit.png', 1, NULL, NULL),
(24, '/detail-6@1-dryfruit.png', 1, NULL, NULL),
(25, '/detail-7@1-dryfruit.png', 1, NULL, NULL),
(26, '/detail-8@1-dryfruit.png', 1, NULL, NULL),
(27, '/detail-9@1-dryfruit.png', 1, NULL, NULL),
(28, '/detail-11@1-dryfruit.png', 1, NULL, NULL),
(29, '/detail-10@1-dryfruit.png', 1, NULL, NULL),
(31, '/product-rice@1.png', 1, NULL, NULL),
(32, '/product-tea@1.png', 1, NULL, NULL),
(33, '/product-dryfruit@2.png', 1, NULL, NULL),
(36, '/product-dryfruit@3.png', 1, NULL, NULL),
(37, '/product-dryfruit@4.png', 1, NULL, NULL),
(38, '/product-dryfruit@5.png', 1, NULL, NULL),
(39, '/product-dryfruit-a@6.png', 1, NULL, NULL),
(40, '/product-dryfruit@7.png', 1, NULL, NULL),
(41, '/product-rice@2.png', 1, NULL, NULL),
(42, '/product-rice@3.png', 1, NULL, NULL),
(43, '/product-rice@4.png', 1, NULL, NULL),
(44, '/product-fry@1.png', 1, NULL, NULL),
(45, '/product-fry@2.png', 1, NULL, NULL),
(46, '/product-fry@3.png', 1, NULL, NULL),
(47, '/product-tea@2.png', 1, NULL, NULL),
(48, '/product-tea@3.png', 1, NULL, NULL),
(49, '/1@theme-head.png', 1, NULL, NULL),
(50, '/2@theme-head.png', 1, NULL, NULL),
(51, '/3@theme-head.png', 1, NULL, NULL),
(52, '/product-cake@1.png', 1, NULL, NULL),
(53, '/product-cake@2.png', 1, NULL, NULL),
(54, '/product-cake-a@3.png', 1, NULL, NULL),
(55, '/product-cake-a@4.png', 1, NULL, NULL),
(56, '/product-dryfruit@8.png', 1, NULL, NULL),
(57, '/product-fry@4.png', 1, NULL, NULL),
(58, '/product-fry@5.png', 1, NULL, NULL),
(59, '/product-rice@5.png', 1, NULL, NULL),
(60, '/product-rice@7.png', 1, NULL, NULL),
(62, '/detail-12@1-dryfruit.png', 1, NULL, NULL),
(63, '/detail-13@1-dryfruit.png', 1, NULL, NULL),
(65, '/banner-4a.png', 1, NULL, NULL),
(66, '/product-vg@4.png', 1, NULL, NULL),
(67, '/product-vg@5.png', 1, NULL, NULL),
(68, '/product-vg@2.png', 1, NULL, NULL),
(69, '/product-vg@3.png', 1, NULL, NULL),
(70, '', 1, NULL, 1503462352),
(71, '/599d03e48dff0.jpg', 1, NULL, 1503462372),
(72, '', 1, NULL, 1503462417),
(77, '/599d4da8451da.jpg', 1, NULL, 1503481256),
(76, '/599d4d5ce0e21.jpg', 1, NULL, 1503481180),
(78, '/599d4dc53700a.jpg', 1, NULL, 1503481285),
(79, '/599d5d0680b3a.jpg', 1, NULL, 1503485190),
(80, '/599d5ece07008.jpg', 1, NULL, 1503485646),
(81, '/599d5ef50a8da.jpg', 1, NULL, 1503485685),
(82, '/599d5f135bdeb.jpg', 1, NULL, 1503485715),
(83, '/599d5f53b8fb3.jpg', 1, NULL, 1503485779),
(84, '/599d5f8eb8e93.jpg', 1, NULL, 1503485838),
(85, '/599d5fb36e332.jpg', 1, NULL, 1503485875),
(86, '/599d5fd39b8d9.jpg', 1, NULL, 1503485907),
(87, '/599d64cfad5b1.jpg', 1, NULL, 1503487183),
(88, '/599d64f658c41.jpg', 1, NULL, 1503487222),
(89, '/599e3f029190b.jpg', 1, NULL, 1503543042),
(90, '/599e40e00f6b3.jpg', 1, NULL, 1503543520),
(91, '/599e40f0bc69c.jpg', 1, NULL, 1503543536),
(92, '/599e41c48d2ad.jpg', 1, NULL, 1503543748),
(93, '/599e420258bc3.png', 1, NULL, 1503543810),
(94, '/599e4240c91ec.jpg', 1, NULL, 1503543872),
(95, '/599e44ddb4e93.jpg', 1, NULL, 1503544541),
(96, '/599e44f11cf3e.jpg', 1, NULL, 1503544561),
(97, '/599e480039f70.jpg', 1, NULL, 1503545344),
(98, '/599e48507b16c.jpg', 1, NULL, 1503545424),
(99, '/599e4999b4c14.jpg', 1, NULL, 1503545753),
(100, '/59a3e0c752318.jpg', 1, NULL, 1503912135),
(101, '/59a3e0cc47b25.jpg', 1, NULL, 1503912140),
(102, '/59a3e36dbf4c0.jpg', 1, NULL, 1503912813),
(103, '/59a3e3732b6aa.jpg', 1, NULL, 1503912819),
(104, '', 1, NULL, 1503992816),
(105, '/59a51e9d0233a.jpg', 1, NULL, 1503993501),
(106, '/59a51f4c2dc8d.jpg', 1, NULL, 1503993676),
(107, '/59a51f8047060.jpg', 1, NULL, 1503993728),
(108, '/59a8d96774203.jpg', 1, NULL, 1504237927),
(109, '/59a8d9e130b8f.jpg', 1, NULL, 1504238049),
(110, '/59a8db436fcba.jpg', 1, NULL, 1504238403),
(111, '/59a8db914a027.jpg', 1, NULL, 1504238481),
(112, '/59a8e49c8b329.jpg', 1, NULL, 1504240796),
(113, '/59a8e50db817f.jpg', 1, NULL, 1504240909),
(114, '/59a8e5bbd2dc6.jpg', 1, NULL, 1504241083),
(115, '/59a8e6027cb5a.jpg', 1, NULL, 1504241154),
(116, '/59a8e658b1eb1.jpg', 1, NULL, 1504241240),
(117, '/59a8e65ebb4d0.jpg', 1, NULL, 1504241246),
(118, '/59a8e663bd94c.jpg', 1, NULL, 1504241251),
(119, '/59a8e71c0b587.jpg', 1, NULL, 1504241436),
(120, '/59a8e72505511.jpg', 1, NULL, 1504241445),
(121, '/59a8e72a42a07.jpg', 1, NULL, 1504241450),
(122, '/59a8e72f726fe.jpg', 1, NULL, 1504241455),
(123, '/59a8e8e4ebb31.jpg', 1, NULL, 1504241892),
(124, '', 1, NULL, 1504241901),
(125, '/59a8e8eecbda1.jpg', 1, NULL, 1504241902),
(126, '/59a8e8efa4c57.jpg', 1, NULL, 1504241903),
(127, '/59a8f7cba30a9.jpg', 1, NULL, 1504245707),
(128, '/59a8f7d41237f.jpg', 1, NULL, 1504245716),
(129, '/59a8f7d4dadcb.jpg', 1, NULL, 1504245716),
(130, '/59a8f7d63ab94.jpg', 1, NULL, 1504245718),
(131, '/59a8f8e407d44.jpg', 1, NULL, 1504245988),
(132, '/59a8f8ef34910.jpg', 1, NULL, 1504245999),
(139, '/59a8fef73c72e.jpg', 1, NULL, 1504247543),
(134, '/59a8f8f17286c.jpg', 1, NULL, 1504246001),
(135, '/59a8f9d72315d.jpg', 1, NULL, 1504246231),
(137, '/59a8f9e0815be.jpg', 1, NULL, 1504246240),
(138, '/59a8f9e164b28.jpg', 1, NULL, 1504246241),
(140, '/59a8ff131e10b.jpg', 1, NULL, 1504247571),
(141, '/59a8ffba5fbb8.jpg', 1, NULL, 1504247738),
(142, '/59a900481869a.jpg', 1, NULL, 1504247880),
(143, '/59a90097adf17.jpg', 1, NULL, 1504247959),
(144, '/59a900a327579.jpg', 1, NULL, 1504247971),
(145, '/59a900a3e1521.jpg', 1, NULL, 1504247971),
(146, '', 1, NULL, 1504247972),
(147, '/59a903e06959b.jpg', 1, NULL, 1504248800),
(148, '/59a904dcafe2e.jpg', 1, NULL, 1504249052),
(149, '/59a904e71df78.jpg', 1, NULL, 1504249063),
(152, '/59a92bbfe925b.jpg', 1, NULL, 1504259007),
(151, '/59a904ea6b507.jpg', 1, NULL, 1504249066),
(153, '/59a93761f0c96.jpg', 1, NULL, 1504261985),
(154, '/59a93d052ffd9.jpg', 1, NULL, 1504263429),
(155, '', 1, NULL, 1504265037),
(156, '/59a9434ee879d.jpg', 1, NULL, 1504265038),
(158, '/59a9464cb861c.jpg', 1, NULL, 1504265804);

-- --------------------------------------------------------

--
-- 表的结构 `menu`
--

CREATE TABLE IF NOT EXISTS `menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '文档ID',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序（同级有效）',
  `url` char(255) NOT NULL DEFAULT '' COMMENT '链接地址',
  `hide` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否隐藏',
  `tip` varchar(255) NOT NULL DEFAULT '' COMMENT '提示',
  `group` varchar(50) DEFAULT '' COMMENT '分组',
  `is_dev` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否仅开发者模式可见',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='后台导航数据表' AUTO_INCREMENT=14 ;

--
-- 转存表中的数据 `menu`
--

INSERT INTO `menu` (`id`, `title`, `pid`, `sort`, `url`, `hide`, `tip`, `group`, `is_dev`) VALUES
(1, '菜单管理', 0, 0, 'Menu', 0, '这是菜单管理', '', 0),
(2, '添加菜单', 1, 0, 'Menu/add', 0, 'add menu', '', 0),
(3, '菜单列表', 1, 0, 'Menu/index', 0, 'list menu', '', 0),
(4, 'banner管理', 0, 0, 'Banner', 0, 'banner', '', 0),
(5, 'banner列表', 4, 0, 'Banner/index', 0, 'banner list', '', 0),
(6, '添加banner', 4, 0, 'Banner/add', 0, 'banner add', '', 0),
(7, '删除菜单', 1, 0, 'Menu/delete', 0, 'Menu', '菜单管理', 0),
(8, 'theme管理', 0, 0, 'Theme', 0, 'Theme', '内容管理', 0),
(9, 'theme列表', 8, 0, 'Theme/index', 0, 'Theme list', '内容管理', 0),
(10, '添加theme', 8, 0, 'Theme/add', 0, 'Theme add', '内容管理', 0),
(11, '产品管理', 0, 0, 'Product', 0, 'Product', '内容管理', 0),
(12, '添加产品', 11, 0, 'Product/add', 0, 'Product add', '内容管理', 0),
(13, '产品列表', 11, 0, 'Product/index', 0, 'Product index', '内容管理', 0);

-- --------------------------------------------------------

--
-- 表的结构 `nail_main_styles`
--

CREATE TABLE IF NOT EXISTS `nail_main_styles` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `title` varchar(15) NOT NULL,
  `style_img` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- 转存表中的数据 `nail_main_styles`
--

INSERT INTO `nail_main_styles` (`id`, `title`, `style_img`) VALUES
(1, '流行款', 'no'),
(2, '手法', 'no'),
(3, '材料', 'no');

-- --------------------------------------------------------

--
-- 表的结构 `nail_styles`
--

CREATE TABLE IF NOT EXISTS `nail_styles` (
  `id` tinyint(5) NOT NULL AUTO_INCREMENT,
  `main_id` tinyint(5) NOT NULL,
  `img_id` tinyint(5) NOT NULL,
  `title` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- 转存表中的数据 `nail_styles`
--

INSERT INTO `nail_styles` (`id`, `main_id`, `img_id`, `title`) VALUES
(1, 1, 13, '夏天'),
(2, 1, 13, '日式'),
(3, 2, 19, '渐变'),
(4, 2, 20, '手绘'),
(5, 3, 20, '钻饰'),
(6, 3, 20, '金银线');

-- --------------------------------------------------------

--
-- 表的结构 `order`
--

CREATE TABLE IF NOT EXISTS `order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_no` varchar(20) NOT NULL COMMENT '订单号',
  `user_id` int(11) NOT NULL COMMENT '外键，用户id，注意并不是openid',
  `delete_time` int(11) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `total_price` decimal(6,2) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1:未支付， 2：已支付，3：已发货 , 4: 已支付，但库存不足',
  `snap_img` varchar(255) DEFAULT NULL COMMENT '订单快照图片',
  `snap_name` varchar(80) DEFAULT NULL COMMENT '订单快照名称',
  `total_count` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) DEFAULT NULL,
  `snap_items` text COMMENT '订单其他信息快照（json)',
  `snap_address` varchar(500) DEFAULT NULL COMMENT '地址快照',
  `prepay_id` varchar(100) DEFAULT NULL COMMENT '订单微信支付的预订单id（用于发送模板消息）',
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_no` (`order_no`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=19 ;

--
-- 转存表中的数据 `order`
--

INSERT INTO `order` (`id`, `order_no`, `user_id`, `delete_time`, `create_time`, `total_price`, `status`, `snap_img`, `snap_name`, `total_count`, `update_time`, `snap_items`, `snap_address`, `prepay_id`) VALUES
(17, 'A812681447273385', 59, NULL, NULL, '0.03', 1, 'http://lymmsf.com/zerg/public/images/product-rice@1.png', '素米 327克', 3, NULL, '[{"id":3,"haveStock":true,"count":2,"name":"\\u7d20\\u7c73 327\\u514b","totalPrice":0.02,"product_img":"http:\\/\\/lymmsf.com\\/zerg\\/public\\/images\\/product-rice@1.png","price":"0.01"},{"id":9,"haveStock":true,"count":1,"name":"\\u51ac\\u6728\\u7ea2\\u67a3 500\\u514b","totalPrice":0.01,"product_img":"http:\\/\\/lymmsf.com\\/zerg\\/public\\/images\\/product-dryfruit@4.png","price":"0.01"}]', '{"id":2,"name":"\\u5f20\\u4e09","mobile":"18888888888","province":"\\u5e7f\\u4e1c\\u7701","city":"\\u5e7f\\u5dde\\u5e02","country":"\\u5929\\u6cb3\\u533a","detail":"\\u67d0\\u5df7\\u67d0\\u53f7","delete_time":null,"user_id":59,"update_time":"1970-01-01 08:00:00"}', NULL),
(18, 'A812681700109014', 59, NULL, NULL, '0.03', 1, 'http://lymmsf.com/zerg/public/images/product-rice@1.png', '素米 327克', 3, NULL, '[{"id":3,"haveStock":true,"count":2,"name":"\\u7d20\\u7c73 327\\u514b","totalPrice":0.02,"product_img":"http:\\/\\/lymmsf.com\\/zerg\\/public\\/images\\/product-rice@1.png","price":"0.01"},{"id":9,"haveStock":true,"count":1,"name":"\\u51ac\\u6728\\u7ea2\\u67a3 500\\u514b","totalPrice":0.01,"product_img":"http:\\/\\/lymmsf.com\\/zerg\\/public\\/images\\/product-dryfruit@4.png","price":"0.01"}]', '{"id":2,"name":"\\u5f20\\u4e09","mobile":"18888888888","province":"\\u5e7f\\u4e1c\\u7701","city":"\\u5e7f\\u5dde\\u5e02","country":"\\u5929\\u6cb3\\u533a","detail":"\\u67d0\\u5df7\\u67d0\\u53f7","delete_time":null,"user_id":59,"update_time":"1970-01-01 08:00:00"}', 'wx201708120018493b02028eba0517562182');

-- --------------------------------------------------------

--
-- 表的结构 `order_product`
--

CREATE TABLE IF NOT EXISTS `order_product` (
  `order_id` int(11) NOT NULL COMMENT '联合主键，订单id',
  `product_id` int(11) NOT NULL COMMENT '联合主键，商品id',
  `count` int(11) NOT NULL COMMENT '商品数量',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`product_id`,`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `order_product`
--

INSERT INTO `order_product` (`order_id`, `product_id`, `count`, `delete_time`, `update_time`) VALUES
(1, 1, 1, NULL, NULL),
(2, 1, 1, NULL, NULL),
(3, 1, 1, NULL, NULL),
(4, 1, 1, NULL, NULL),
(5, 1, 1, NULL, NULL),
(6, 1, 1, NULL, NULL),
(7, 1, 1, NULL, NULL),
(8, 1, 1, NULL, NULL),
(9, 1, 1, NULL, NULL),
(10, 1, 1, NULL, NULL),
(11, 1, 1, NULL, NULL),
(12, 1, 1, NULL, NULL),
(1, 2, 1, NULL, NULL),
(2, 2, 1, NULL, NULL),
(3, 2, 1, NULL, NULL),
(4, 2, 1, NULL, NULL),
(5, 2, 1, NULL, NULL),
(6, 2, 1, NULL, NULL),
(7, 2, 1, NULL, NULL),
(8, 2, 1, NULL, NULL),
(9, 2, 1, NULL, NULL),
(10, 2, 1, NULL, NULL),
(11, 2, 1, NULL, NULL),
(12, 2, 1, NULL, NULL),
(13, 2, 2, NULL, NULL),
(14, 2, 2, NULL, NULL),
(15, 2, 2, NULL, NULL),
(16, 3, 2, NULL, NULL),
(17, 3, 2, NULL, NULL),
(18, 3, 2, NULL, NULL),
(17, 9, 1, NULL, NULL),
(18, 9, 1, NULL, NULL),
(13, 11, 5, NULL, NULL),
(14, 11, 5, NULL, NULL),
(15, 11, 5, NULL, NULL),
(16, 11, 2, NULL, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `product`
--

CREATE TABLE IF NOT EXISTS `product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL COMMENT '商品名称',
  `price` decimal(6,2) NOT NULL COMMENT '价格,单位：分',
  `stock` int(11) NOT NULL DEFAULT '0' COMMENT '库存量',
  `delete_time` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `main_img_url` varchar(255) DEFAULT NULL COMMENT '主图ID号，这是一个反范式设计，有一定的冗余',
  `from` tinyint(4) NOT NULL DEFAULT '1' COMMENT '图片来自 1 本地 ，2公网',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL,
  `summary` varchar(50) DEFAULT NULL COMMENT '摘要',
  `img_id` int(11) DEFAULT NULL COMMENT '图片外键',
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=47 ;

--
-- 转存表中的数据 `product`
--

INSERT INTO `product` (`id`, `name`, `price`, `stock`, `delete_time`, `category_id`, `main_img_url`, `from`, `create_time`, `update_time`, `summary`, `img_id`, `width`, `height`) VALUES
(1, '芹菜 半斤', '0.01', 998, NULL, 3, '/product-vg@1.png', 1, NULL, NULL, NULL, 13, 430, 430),
(2, '梨花带雨 3个', '0.01', 984, NULL, 2, '/product-dryfruit@1.png', 1, NULL, NULL, NULL, 10, 480, 854),
(3, '素米 327克', '0.01', 996, NULL, 7, '/product-rice@1.png', 1, NULL, NULL, NULL, 31, 430, 430),
(4, '红袖枸杞 6克*3袋', '0.01', 998, NULL, 6, '/product-tea@1.png', 1, NULL, NULL, NULL, 32, 430, 430),
(5, '春生龙眼 500克', '0.01', 995, NULL, 2, '/product-dryfruit@2.png', 1, NULL, NULL, NULL, 33, 430, 430),
(6, '小红的猪耳朵 120克', '0.01', 0, NULL, 5, '/product-cake@2.png', 1, NULL, NULL, NULL, 53, 430, 430),
(7, '泥蒿 半斤', '0.01', 998, NULL, 3, '/product-vg@2.png', 1, NULL, NULL, NULL, 68, 0, 0),
(8, '夏日芒果 3个', '0.01', 995, NULL, 2, '/product-dryfruit@3.png', 1, NULL, NULL, NULL, 36, 0, 0),
(9, '冬木红枣 500克', '0.01', 996, NULL, 2, '/product-dryfruit@4.png', 1, NULL, NULL, NULL, 37, 0, 0),
(10, '万紫千凤梨 300克', '0.01', 996, NULL, 2, '/product-dryfruit@5.png', 1, NULL, NULL, NULL, 38, 0, 0),
(11, '贵妃笑 100克', '0.01', 994, NULL, 2, '/product-dryfruit-a@6.png', 1, NULL, NULL, NULL, 39, 0, 0),
(12, '珍奇异果 3个', '0.01', 999, NULL, 2, '/product-dryfruit@7.png', 1, NULL, NULL, NULL, 40, 0, 0),
(13, '绿豆 125克', '0.01', 999, NULL, 7, '/product-rice@2.png', 1, NULL, NULL, NULL, 41, 0, 0),
(14, '芝麻 50克', '0.01', 999, NULL, 7, '/product-rice@3.png', 1, NULL, NULL, NULL, 42, 0, 0),
(15, '猴头菇 370克', '0.01', 999, NULL, 7, '/product-rice@4.png', 1, NULL, NULL, NULL, 43, 0, 0),
(16, '西红柿 1斤', '0.01', 999, NULL, 3, '/product-vg@3.png', 1, NULL, NULL, NULL, 69, 0, 0),
(17, '油炸花生 300克', '0.01', 999, NULL, 4, '/product-fry@1.png', 1, NULL, NULL, NULL, 44, 0, 0),
(18, '春泥西瓜子 128克', '0.01', 997, NULL, 4, '/product-fry@2.png', 1, NULL, NULL, NULL, 45, 0, 0),
(19, '碧水葵花籽 128克', '0.01', 999, NULL, 4, '/product-fry@3.png', 1, NULL, NULL, NULL, 46, 0, 0),
(20, '碧螺春 12克*3袋', '0.01', 999, NULL, 6, '/product-tea@2.png', 1, NULL, NULL, NULL, 47, 0, 0),
(21, '西湖龙井 8克*3袋', '0.01', 998, NULL, 6, '/product-tea@3.png', 1, NULL, NULL, NULL, 48, 0, 0),
(22, '梅兰清花糕 1个', '0.01', 997, NULL, 5, '/product-cake-a@3.png', 1, NULL, NULL, NULL, 54, 0, 0),
(23, '清凉薄荷糕 1个', '0.01', 998, NULL, 5, '/product-cake-a@4.png', 1, NULL, NULL, NULL, 55, 0, 0),
(25, '小明的妙脆角 120克', '0.01', 999, NULL, 5, '/product-cake@1.png', 1, NULL, NULL, NULL, 52, 0, 0),
(26, '红衣青瓜 混搭160克', '0.01', 999, NULL, 2, '/product-dryfruit@8.png', 1, NULL, NULL, NULL, 56, 0, 0),
(27, '锈色瓜子 100克', '0.01', 998, NULL, 4, '/product-fry@4.png', 1, NULL, NULL, NULL, 57, 0, 0),
(28, '春泥花生 200克', '0.01', 999, NULL, 4, '/product-fry@5.png', 1, NULL, NULL, NULL, 58, 0, 0),
(29, '冰心鸡蛋 2个', '0.01', 999, NULL, 7, '/product-rice@5.png', 1, NULL, NULL, NULL, 59, 0, 0),
(30, '八宝莲子 200克', '0.01', 999, NULL, 7, '/product-rice@6.png', 1, NULL, NULL, NULL, 14, 0, 0),
(31, '深涧木耳 78克', '0.01', 999, NULL, 7, '/product-rice@7.png', 1, NULL, NULL, NULL, 60, 0, 0),
(32, '土豆 半斤', '0.01', 999, NULL, 3, '/product-vg@4.png', 1, NULL, NULL, NULL, 66, 0, 0),
(33, '青椒 半斤', '0.01', 999, NULL, 3, '/product-vg@5.png', 1, NULL, NULL, NULL, 67, 0, 0),
(46, '我得测试', '23.00', 99, NULL, 2, '/59a93761f0c96.jpg', 1, NULL, NULL, NULL, 153, 340, 450);

-- --------------------------------------------------------

--
-- 表的结构 `product_image`
--

CREATE TABLE IF NOT EXISTS `product_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `img_id` int(11) NOT NULL COMMENT '外键，关联图片表',
  `update_time` int(11) DEFAULT NULL COMMENT '状态，主要表示是否删除，也可以扩展其他状态',
  `product_id` int(11) NOT NULL COMMENT '商品id，外键',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=39 ;

--
-- 转存表中的数据 `product_image`
--

INSERT INTO `product_image` (`id`, `img_id`, `update_time`, `product_id`) VALUES
(4, 19, NULL, 11),
(5, 20, NULL, 11),
(6, 21, NULL, 11),
(7, 22, NULL, 11),
(8, 23, NULL, 11),
(9, 24, NULL, 11),
(10, 25, NULL, 11),
(11, 26, NULL, 11),
(12, 27, NULL, 11),
(13, 28, NULL, 11),
(14, 29, NULL, 11),
(18, 62, NULL, 11),
(19, 63, NULL, 11),
(31, 149, 1504249068, 46),
(32, 158, 1504266039, 46),
(29, 151, 1504249068, 46);

-- --------------------------------------------------------

--
-- 表的结构 `product_property`
--

CREATE TABLE IF NOT EXISTS `product_property` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) DEFAULT '' COMMENT '详情属性名称',
  `product_id` int(11) NOT NULL COMMENT '商品id，外键',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `place` varchar(255) NOT NULL,
  `taste` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=14 ;

--
-- 转存表中的数据 `product_property`
--

INSERT INTO `product_property` (`id`, `name`, `product_id`, `delete_time`, `update_time`, `place`, `taste`) VALUES
(13, '嘟嘟嘟rrrrrr', 46, NULL, NULL, '任溶溶fffftttttt', '她她她ddddd');

-- --------------------------------------------------------

--
-- 表的结构 `select_image`
--

CREATE TABLE IF NOT EXISTS `select_image` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `img_id` int(5) NOT NULL,
  `style_ids` varchar(150) NOT NULL,
  `width` varchar(100) NOT NULL,
  `height` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- 转存表中的数据 `select_image`
--

INSERT INTO `select_image` (`id`, `img_id`, `style_ids`, `width`, `height`) VALUES
(1, 10, '1-3-5', '480', '854'),
(2, 13, '2-4-1-3', '430', '430'),
(3, 31, '1-3-5-6', '430', '430'),
(4, 32, '2-3-5', '430', '430'),
(5, 33, '4-5-6-3', '430', '430'),
(6, 33, '4-5-6-3', '430', '430'),
(7, 33, '4-5-6-3', '430', '430'),
(8, 33, '4-5-6-3', '430', '430'),
(9, 33, '4-5-6-3', '430', '430'),
(10, 33, '4-5-6-3', '430', '430'),
(11, 33, '4-5-6-3', '430', '430'),
(12, 33, '4-5-6-3', '430', '430');

-- --------------------------------------------------------

--
-- 表的结构 `theme`
--

CREATE TABLE IF NOT EXISTS `theme` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '专题名称',
  `description` varchar(255) DEFAULT NULL COMMENT '专题描述',
  `topic_img_id` int(11) NOT NULL COMMENT '主题图，外键',
  `delete_time` int(11) DEFAULT NULL,
  `head_img_id` int(11) NOT NULL COMMENT '专题列表页，头图',
  `update_time` int(11) DEFAULT NULL,
  `products_id` varchar(255) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COMMENT='主题信息表' AUTO_INCREMENT=10 ;

--
-- 转存表中的数据 `theme`
--

INSERT INTO `theme` (`id`, `name`, `description`, `topic_img_id`, `delete_time`, `head_img_id`, `update_time`, `products_id`) VALUES
(1, '专题栏位一', '美味水果世界', 16, NULL, 49, NULL, ''),
(2, '专题栏位二', '新品推荐', 17, NULL, 50, NULL, ''),
(3, '专题栏位三', '做个干物女', 18, NULL, 18, NULL, ''),
(7, '洛阳指韵', '这里是洛阳指韵美甲的最新主题\r\n                                        ', 102, NULL, 103, NULL, '["1","2","3","4","7"]'),
(6, '洛阳指韵', '这里是洛阳指韵美甲的最新主题hahahah\r\n                                                                                  kkkkkkk                                                                                                                                               ', 106, NULL, 107, NULL, '                                                  ["1","4","5","6","7","8","9"]                                        '),
(8, '洛阳指韵', '这里是洛阳指韵美甲的最新主题\r\n                                        ', 102, NULL, 103, NULL, '["1","2","3","4","7"]');

-- --------------------------------------------------------

--
-- 表的结构 `theme_product`
--

CREATE TABLE IF NOT EXISTS `theme_product` (
  `theme_id` int(11) NOT NULL COMMENT '主题外键',
  `product_id` int(11) NOT NULL COMMENT '商品外键',
  PRIMARY KEY (`theme_id`,`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='主题所包含的商品';

--
-- 转存表中的数据 `theme_product`
--

INSERT INTO `theme_product` (`theme_id`, `product_id`) VALUES
(1, 2),
(1, 5),
(1, 8),
(1, 10),
(1, 12),
(2, 1),
(2, 2),
(2, 3),
(2, 5),
(2, 6),
(2, 16),
(2, 33),
(3, 15),
(3, 18),
(3, 19),
(3, 27),
(3, 30),
(3, 31),
(6, 1),
(6, 4),
(6, 5),
(6, 6),
(6, 7),
(6, 8),
(6, 9),
(7, 1),
(7, 2),
(7, 3),
(7, 4),
(7, 7),
(8, 1),
(8, 2),
(8, 3),
(8, 4),
(8, 7),
(9, 1),
(9, 2),
(9, 3),
(9, 4),
(9, 7),
(9, 8),
(9, 10),
(9, 11);

-- --------------------------------------------------------

--
-- 表的结构 `third_app`
--

CREATE TABLE IF NOT EXISTS `third_app` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_id` varchar(64) NOT NULL COMMENT '应用app_id',
  `app_secret` varchar(64) NOT NULL COMMENT '应用secret',
  `app_description` varchar(100) DEFAULT NULL COMMENT '应用程序描述',
  `scope` varchar(20) NOT NULL COMMENT '应用权限',
  `scope_description` varchar(100) DEFAULT NULL COMMENT '权限描述',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COMMENT='访问API的各应用账号密码表' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `third_app`
--

INSERT INTO `third_app` (`id`, `app_id`, `app_secret`, `app_description`, `scope`, `scope_description`, `delete_time`, `update_time`) VALUES
(1, 'starcraft', '777*777', 'CMS', '32', 'Super', NULL, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(50) NOT NULL,
  `nickname` varchar(50) DEFAULT NULL,
  `extend` varchar(255) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL COMMENT '注册时间',
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `openid` (`openid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=60 ;

--
-- 转存表中的数据 `user`
--

INSERT INTO `user` (`id`, `openid`, `nickname`, `extend`, `delete_time`, `create_time`, `update_time`) VALUES
(58, 'oFyXu0D3e0orlgw9akpr2roTGb2o', NULL, NULL, NULL, NULL, NULL),
(59, 'o2bb_0EYgl0N3iCHxbUKArkwKf_4', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `user_address`
--

CREATE TABLE IF NOT EXISTS `user_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL COMMENT '收获人姓名',
  `mobile` varchar(20) NOT NULL COMMENT '手机号',
  `province` varchar(20) DEFAULT NULL COMMENT '省',
  `city` varchar(20) DEFAULT NULL COMMENT '市',
  `country` varchar(20) DEFAULT NULL COMMENT '区',
  `detail` varchar(100) DEFAULT NULL COMMENT '详细地址',
  `delete_time` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL COMMENT '外键',
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `user_address`
--

INSERT INTO `user_address` (`id`, `name`, `mobile`, `province`, `city`, `country`, `detail`, `delete_time`, `user_id`, `update_time`) VALUES
(1, 'wkkk123', '13849939506', 'henan', 'luoyang', 'Chine', 'iiiiiiii', NULL, 58, NULL),
(2, '张三', '18888888888', '广东省', '广州市', '天河区', '某巷某号', NULL, 59, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `user_appoint`
--

CREATE TABLE IF NOT EXISTS `user_appoint` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `uid` int(15) NOT NULL,
  `selected_time` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- 转存表中的数据 `user_appoint`
--

INSERT INTO `user_appoint` (`id`, `uid`, `selected_time`) VALUES
(2, 59, '2017-8-15_19:00'),
(3, 59, '2017-8-18_08:00'),
(4, 59, '2017-8-18_10:00'),
(5, 59, '2017-8-16_10:00'),
(6, 59, '2017-8-16_09:00');

-- --------------------------------------------------------

--
-- 表的结构 `user_beloved_image`
--

CREATE TABLE IF NOT EXISTS `user_beloved_image` (
  `id` tinyint(5) NOT NULL AUTO_INCREMENT,
  `uid` tinyint(150) NOT NULL,
  `select_image_id` tinyint(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- 转存表中的数据 `user_beloved_image`
--

INSERT INTO `user_beloved_image` (`id`, `uid`, `select_image_id`) VALUES
(1, 59, 1),
(5, 59, 4),
(3, 59, 2),
(4, 59, 3),
(6, 59, 5),
(7, 59, 6),
(8, 59, 7),
(9, 59, 8),
(10, 59, 9),
(11, 59, 10),
(12, 59, 11),
(14, 59, 12);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


--
-- 表的结构 预约项目
--
CREATE TABLE IF NOT EXISTS `appoint_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `image_id` int(11) NOT NULL,
  `day_num` int(4) NOT NULL DEFAULT 0 COMMENT '每天预约次数',
  `isc` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否显示项目描述 0 不描述 1 描述',
  `content` text COMMENT '项目描述',
  `isshow` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否显示该项目 0 不描述 1 描述',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--
-- 表的结构 预约通常参数
--
CREATE TABLE IF NOT EXISTS `appoint_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `before_time` int(4) NOT NULL DEFAULT 60 COMMENT '提前预约时间',
  `limit_time` int(2) NOT NULL DEFAULT 30 COMMENT '提前预约时间',
  `notify_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 提交通知 1 提交与付款通知',
  `notify_cs_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 通知所有客服 1 只通知管理员',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--
-- 表的结构 预约结构
--
CREATE TABLE IF NOT EXISTS `appoint` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL COMMENT '预约主题title',
  `image_id` int(11) NOT NULL COMMENT '预约封面',
  `description` text COMMENT '预约描述',
  `begin_time` int(10) NOT NULL COMMENT '预约开始时间',
  `end_time` int(10) NOT NULL COMMENT '预约结束时间',
  `appoint_time_list` text NOT NULL COMMENT '每天预约时间段 ***重要',
  `appoint_days` int(10) NOT NULL default 7 COMMENT '可预约的天数默认是7天',
  `exclude_date` varchar(150) COMMENT '排除的日期',
  `notify_email` varchar(20) COMMENT '提醒email地址',
  `cs_templateid` varchar(100) COMMENT '通知客服的消息模板id',
  `fans_templateid` varchar(100) COMMENT '通知客服的消息模板id',
  `pre_total` int(3) NOT NULL default 4 COMMENT '每人总共可预约次数',
  `day_total` int(3) NOT NULL default 2 COMMENT '每人每天总共可预约次数',
  `edit` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否需要修改预约信息 0 不 1 是',
  `code` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否需要二维码核销 0 不 1 是',
  `follow` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否需要关注 0 不 1 是',
  `isshow` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否显示该预约 0 不 1 是',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


--
-- 表的结构 预约记录信息
--
CREATE TABLE IF NOT EXISTS `appoint_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL COMMENT '预约用户id',
  `image_id` int(11) '图片预留',
  `appoint_time` varchar(40) NOT NULL COMMENT '预约时间id',
  `openid` varchar(50) NOT NULL COMMENT '粉丝的openid',
  `mobile` int(12) NOT NULL COMMENT '粉丝的手机',
  `name` varchar(12) NOT NULL COMMENT '粉丝的真实姓名',
  `appoint_project` int(5) NOT NULL COMMENT '预约项目',
  `remark` test COMMENT '客户备注信息',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

