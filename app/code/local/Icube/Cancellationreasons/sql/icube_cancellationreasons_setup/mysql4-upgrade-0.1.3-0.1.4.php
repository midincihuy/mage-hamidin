<?php

$installer=$this;
$installer->startSetup();
$installer->run("ALTER TABLE icube_cancellation_reasons ADD id int(11) NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY(`id`);");
$installer->endSetup();

?>