<?php

$installer	=	$this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS `{$this->getTable('cancellation_reasons')}`;
CREATE TABLE `{$this->getTable('cancellation_reasons')}` (
  `order_id` INT(10) NOT NULL,
  `reason_cancel` VARCHAR(255) NOT NULL,
  `source` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL default '0000-00-00 00:00:00',
  `user_id` INT(10) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();