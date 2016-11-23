<?php

class Icube_Cancellationreasons_Model_Mysql4_List extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
	{
		$this->_init('cancellationreasons/list','entity_id');
	}
}