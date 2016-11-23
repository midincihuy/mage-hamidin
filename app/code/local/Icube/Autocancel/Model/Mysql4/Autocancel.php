<?php

class Icube_Autocancel_Model_Mysql4_Autocancel extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
	{
		$this->_init('autocancel/autocancel', 'autocancel_id');
	}
}