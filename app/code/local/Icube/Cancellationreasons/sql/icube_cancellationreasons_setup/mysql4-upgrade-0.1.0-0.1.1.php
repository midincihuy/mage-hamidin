<?php

$installer	=	$this;

$installer->startSetup();

$installer->run("

 DROP TABLE IF EXISTS `{$this->getTable('icube_cancellation_reasons_list')}`;
CREATE TABLE `{$this->getTable('icube_cancellation_reasons_list')}` (
  `entity_id` INT(10) NOT NULL AUTO_INCREMENT,
  `message` VARCHAR(255) NOT NULL,
  `role_id` INT(10) NOT NULL,
  PRIMARY KEY (entity_id)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

");

$installer->run("
INSERT INTO icube_cancellation_reasons_list(message,role_id) VALUES('System Problem - need to change order data',8);
INSERT INTO icube_cancellation_reasons_list(message,role_id) VALUES('Customer change mind',8);
INSERT INTO icube_cancellation_reasons_list(message,role_id) VALUES('Cancel over SLA',8);
INSERT INTO icube_cancellation_reasons_list(message,role_id) VALUES('Out of home',8);
INSERT INTO icube_cancellation_reasons_list(message,role_id) VALUES('Payment problem',8);
INSERT INTO icube_cancellation_reasons_list(message,role_id) VALUES('Unable to meet customer\'s request (gift, delivery time & area, etc)',8);
INSERT INTO icube_cancellation_reasons_list(message,role_id) VALUES('Others',8);

INSERT INTO icube_cancellation_reasons_list(message,role_id) VALUES('System Problem - need to change order data',6);
INSERT INTO icube_cancellation_reasons_list(message,role_id) VALUES('Cancel over SLA',6);
INSERT INTO icube_cancellation_reasons_list(message,role_id) VALUES('Cancel during confirmation',6);
INSERT INTO icube_cancellation_reasons_list(message,role_id) VALUES('Payment problem - Cash',6);
INSERT INTO icube_cancellation_reasons_list(message,role_id) VALUES('Customer change mind',6);
INSERT INTO icube_cancellation_reasons_list(message,role_id) VALUES('Out of home/town',6);
INSERT INTO icube_cancellation_reasons_list(message,role_id) VALUES('Payment problem - Credit Card',6);
INSERT INTO icube_cancellation_reasons_list(message,role_id) VALUES('Operational problem (double order, product defect)',6);
INSERT INTO icube_cancellation_reasons_list(message,role_id) VALUES('Others',6);
");

/* the purpose is to rename cancellation_reasons to icube_cancellation_reasons, 
but it's error if table icube_cancelation_reasons exist. 
So the easiest way is to drop table and create again */
$installer->run("
DROP TABLE IF EXISTS `{$this->getTable('cancellation_reasons')}`;
DROP TABLE IF EXISTS `{$this->getTable('icube_cancellation_reasons')}`;

CREATE TABLE `{$this->getTable('icube_cancellation_reasons')}` (
  `order_id` INT(10) NOT NULL,
  `reason_cancel` VARCHAR(255) NOT NULL,
  `source` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL default '0000-00-00 00:00:00',
  `user_id` INT(10) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();