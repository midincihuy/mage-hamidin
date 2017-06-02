<?php

$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('sourcechannel/sourcechannel_table'))
    ->addColumn('source_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true,
        ), 'Source ID')
    ->addColumn('channel_code', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Channel Code')
    ->addColumn('channel_text', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Channel Text')
    ->addColumn('channel_sort', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        ), 'Channel Sort')
    ->setComment('Source Channel Table');

$installer->getConnection()->createTable($table);
$installer->endSetup();