<?php

$installer = $this;
$installer->startSetup();

try {

$installer->run("ALTER TABLE `icube_cancellation_reasons`	CHANGE COLUMN `reason_cancel` `reason_cancel` INT(10) NOT NULL AFTER `order_id`;");

} catch (Exception $e) {
    throw new Exception('CMS PAGE UPDATE FAILS. ' . $e->getMessage());
}

$installer->endSetup();