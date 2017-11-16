CREATE DATABASE mydb;
use mydb;

-- ------------------------------------------------
-- Transaction Table
-- ------------------------------------------------
CREATE TABLE `paymentTransaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transactionTypeKey` enum('TOPUP','DRAWOUT','TRANSFER_AMOUNT','TRANSFER_FEE') NOT NULL DEFAULT 'TOPUP',
  `transactionRef` varchar(512) NOT NULL COMMENT 'For transaction code',
  `bankAccountID` int(11) NOT NULL,
  `transactionCurrency` enum('HKD','SGD','THB','NTD','PHP') NOT NULL DEFAULT 'HKD',
  `transactionAmount` decimal(14,4) NOT NULL DEFAULT '0.0000',
  `transactionRemark` varchar(512) NOT NULL DEFAULT '',
  `createTimeStamp` bigint(20) NOT NULL,
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `paymentTransaction` (`transactionTypeKey`, `transactionRef`, `bankAccountID`, `transactionAmount`, `createTimeStamp`, `createTime`) VALUES 
('TOPUP', 'testing1transactiontoken', 1, 100, UNIX_TIMESTAMP(NOW()), NOW()), 
('TOPUP', 'testing2transactiontoken', 2, 100, UNIX_TIMESTAMP(NOW()), NOW());


-- ------------------------------------------------
-- Owner Table
-- ------------------------------------------------
CREATE TABLE `ownAccount` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userName` varchar(128) NOT NULL DEFAULT '',
  `userContact` varchar(64) NOT NULL DEFAULT '',
  `userSex` enum('M','F') NOT NULL DEFAULT 'M',
  `isActive` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 Active, 0 Inactive',
  `createTimeStamp` bigint(20) NOT NULL,
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `ownAccount` (`userName`, `userContact`, `createTimeStamp`, `createTime`) VALUES 
('User 1', '98765432', UNIX_TIMESTAMP(NOW()), NOW()), 
('User 2', '87654321', UNIX_TIMESTAMP(NOW()), NOW());


-- ------------------------------------------------
-- Bank Account Table
-- ------------------------------------------------
CREATE TABLE `bankAccount` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ownerAccountID` int(11) NOT NULL COMMENT 'Owner Account ID',
  `bankName` enum('HSBC','HSB','SCB','CIT','BOC','BEA','ICB','DBS','DSB','COM','CCB','WHB','CYB','LCH','NYC','FBB','PUB','WLB') NOT NULL DEFAULT 'HSBC',
  `bankCurrency` enum('HKD','SGD','THB','NTD','PHP') NOT NULL DEFAULT 'HKD' COMMENT 'For Future extend',
  `bankAmount` decimal(14,4) NOT NULL DEFAULT '0.0000',
  `bankRemain` decimal(14,4) NOT NULL DEFAULT '0.0000',
  `bankDrawout` decimal(14,4) NOT NULL DEFAULT '0.0000',
  `isActive` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 Active, 0 Inactive',
  `createTimeStamp` bigint(20) NOT NULL,
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `bankAccount` (`id`, `ownerAccountID`, `bankName`, `bankAmount`, `bankRemain`, `bankDrawout`, `createTimeStamp`, `createTime`) VALUES 
(1, 1, 'HSBC', 100, 100, 0, UNIX_TIMESTAMP(NOW()), NOW()), 
(2, 2, 'HSBC', 100, 100, 0, UNIX_TIMESTAMP(NOW()), NOW()), 
(3, 1, 'HSB', 0, 0, 0, UNIX_TIMESTAMP(NOW()), NOW());


-- ------------------------------------------------
-- Config Table
-- ------------------------------------------------
CREATE TABLE `configSetting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `configKey` enum('MAX_TRANSFER','MIN_CHARGE') NOT NULL DEFAULT 'MAX_TRANSFER',
  `configAmount` decimal(14,4) NOT NULL DEFAULT '0.0000',
  `createTimeStamp` bigint(20) NOT NULL,
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `configSetting` (`configKey`, `configAmount`, `createTimeStamp`, `createTime`) VALUES 
('MAX_TRANSFER', 10000, UNIX_TIMESTAMP(NOW()), NOW()), 
('MIN_CHARGE', 100, UNIX_TIMESTAMP(NOW()), NOW());

