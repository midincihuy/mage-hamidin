<?php

class Icube_Autocancel_Model_Mysql4_Orders_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
    
    public function _construct() {
        
        $this->_init('autocancel/orders');
    }
}