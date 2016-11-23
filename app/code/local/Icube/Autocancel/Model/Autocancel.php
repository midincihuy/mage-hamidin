<?php

class Icube_Autocancel_Model_Autocancel extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('autocancel/autocancel');
	}
}