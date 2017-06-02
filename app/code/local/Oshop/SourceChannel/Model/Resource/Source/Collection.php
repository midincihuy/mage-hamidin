<?php

class Oshop_SourceChannel_Model_Resource_Source_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('sourcechannel/source');
    }

    // public function addCustomerFilter($customer)
    // {
    //     $select = $this->getSelect();
    //     if($customer->getId()){
    //         $select->where('order.customer_id=?', $customer->getId());
    //     } else {
    //         $select->where('1=0');
    //     }

    //     $select->join(array('order' => $this->getTable('sales/order')),
    //             'order.entity_id = main_table.order_id',
    //             array('status', 'created_at'));
    //     $select->join(array('order_item' => $this->getTable('sales/order_item')),
    //             'order_item.order_id = main_table.order_id
    //             AND order_item.parent_item_id is NULL',
    //             array());

    //     Mage::getResourceHelper('core')->addGroupConcatColumn($select, 'product_names', 'order_item.name', ', ');
    //     $select->group('main_table.comment_id');
    //     return $this;
    // }
}