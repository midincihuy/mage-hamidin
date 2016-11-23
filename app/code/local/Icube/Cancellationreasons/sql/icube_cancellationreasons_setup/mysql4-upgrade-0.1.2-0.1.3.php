<?php

$installer = $this;
$installer->startSetup();

try {

$installer->run("ALTER TABLE `icube_cancellation_reasons`	CHANGE COLUMN `user_id` `user` varchar(255) NOT NULL;");

$installer->run("ALTER TABLE `icube_cancellation_reasons_list` DROP `role_id`;");
$installer->run("TRUNCATE `icube_cancellation_reasons_list`;");

$installer->run("
INSERT INTO `icube_cancellation_reasons_list` (`message`)
VALUES
	('System Problem - need to change order data'),
	('Customer change mind'),
	('Cancel over SLA'),
	('Out of home/town'),
	('Payment problem'),
	('Unable to meet customer\'s request (gift, delivery time & area, etc)'),
	('Others'),
	('Cancel during confirmation'),
	('Payment problem - Cash'),
	('Payment problem - Credit Card'),
	('Operational problem (double order, product defect)');
");


} catch (Exception $e) {
    throw new Exception('TABLE UPDATE FAILS. ' . $e->getMessage());
}




$installer->endSetup();