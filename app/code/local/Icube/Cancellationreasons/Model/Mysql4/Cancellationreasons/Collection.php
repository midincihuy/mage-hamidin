<?php
 
class Icube_Cancellationreasons_Model_Mysql4_Cancellationreasons_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
 	public function _construct()
 	{
 		parent::_construct();
    	$this->_init('cancellationreasons/cancellationreasons');
 	}
}

?>