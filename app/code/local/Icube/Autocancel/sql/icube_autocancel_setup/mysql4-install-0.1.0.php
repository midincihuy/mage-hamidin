<?php

$installer	=	$this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS `{$this->getTable('autocancel/autocancel')}`;
CREATE TABLE `{$this->getTable('autocancel/autocancel')}` (
  	`autocancel_id` int not null auto_increment, 
	`order_payment` varchar(100), 
	`order_status` varchar(32), 
	`time` int(10) not null, 
	primary key(autocancel_id)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();