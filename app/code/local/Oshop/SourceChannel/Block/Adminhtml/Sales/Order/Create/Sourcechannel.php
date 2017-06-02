<?php

class Oshop_SourceChannel_Block_Adminhtml_Sales_Order_Create_Sourcechannel extends Mage_Adminhtml_Block_Template
{
     /**
     * Get current viewed product
     * @return Mage_Sales_Model_Order|null
     */
    public function getOrder()
    {
        if (Mage::registry('current_order')) {
            return Mage::registry('current_order');
        } elseif (Mage::registry('current_invoice')) {
            return Mage::registry('current_invoice')->getOrder();
        } elseif (Mage::registry('current_shipment')) {
            return Mage::registry('current_shipment')->getOrder();
        } elseif (Mage::registry('current_creditmemo')) {
            return Mage::registry('current_creditmemo')->getOrder();
        }
        return null;
    }

    public function getDropdown()
    {
        $source = Mage::getResourceSingleton('sourcechannel/source_collection');
        // $this->setData('sources', $source);
        // var_dump($source);
        return $source;
    }
}