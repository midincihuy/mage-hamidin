<?php

$installer	=	$this;

$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('autocancel/autocancel'), 'reason_id', array(
            'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'length' => 10,
            'nullable' => true,
            'comment' => 'cancellation reason id'
        ));

$installer->getConnection()
    ->addColumn($installer->getTable('icube_autocancel_orders'), 'reason_id', array(
            'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'length' => 10,
            'nullable' => true,
            'comment' => 'cancellation reason id'
        ));

$installer->endSetup();