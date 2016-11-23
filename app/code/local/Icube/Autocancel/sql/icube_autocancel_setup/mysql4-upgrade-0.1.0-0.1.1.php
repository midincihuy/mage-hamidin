<?php

$installer	=	$this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('icube_autocancel_orders')};
CREATE TABLE `{$this->getTable('icube_autocancel_orders')}` (
	`id` INT(10) not null auto_increment,
  	`order_id` INT(10) NOT NULL, 
	`order_status` varchar(32), 
	`datetime` TIMESTAMP NOT NULL default '0000-00-00 00:00:00',
	primary key(id)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();