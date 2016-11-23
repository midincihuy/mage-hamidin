<?php
 
class Icube_Autocancel_Block_Adminhtml_Autocancel extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'icube_autocancel';
        $this->_controller = 'adminhtml_autocancel';
        $this->_headerText = Mage::helper('icube_autocancel')->__('Auto Cancel');
 
        $this->_addButtonLabel = Mage::helper('icube_autocancel')->__('Add New Autocancel');
        parent::__construct();
    }
}