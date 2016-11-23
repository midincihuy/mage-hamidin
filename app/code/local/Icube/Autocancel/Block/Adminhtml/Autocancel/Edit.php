<?php

class Icube_Autocancel_Block_Adminhtml_Autocancel_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Init class
     */
    public function __construct()
    {  
        $this->_blockGroup = 'icube_autocancel';
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_autocancel';
     
        parent::__construct();
     
        $this->_updateButton('save', 'label', $this->__('Save Autocancel'));
        $this->_updateButton('delete', 'label', $this->__('Delete Autocancel'));
    }  
     
    /**
     * Get Header text
     *
     * @return string
     */
    public function getHeaderText()
    {  
        if (Mage::registry('icube_autocancel')->getId()) {
            return $this->__('Edit Autocancel');
        }  
        else {
            return $this->__('New Autocancel');
        }  
    }  
}