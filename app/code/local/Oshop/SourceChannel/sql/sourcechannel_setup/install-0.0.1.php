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

if(!$installer->tableExists('sourcechannel/sourcechannel_table')){
    $installer->getConnection()->createTable($table);
}

$channelorder_table = $installer->getConnection()
    ->newTable($installer->getTable('sourcechannel/channelorder_table'))
    ->addColumn('co_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
        ), 'CO ID')
    ->addColumn('channel_text', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Channel Text')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
        ), 'Real Order ID')
    ->addForeignKey(
        $installer->getFkName(
            'sourcechannel/channelorder_table',
            'order_id',
            'sales/order',
            'entity_id'
            ), 
        'order_id', $installer->getTable('sales/order'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
        )
    ->setComment('Channel vs Order Table');
if(!$installer->tableExists('sourcechannel/channelorder_table')){
    $installer->getConnection()->createTable($channelorder_table);
}
$installer->endSetup();