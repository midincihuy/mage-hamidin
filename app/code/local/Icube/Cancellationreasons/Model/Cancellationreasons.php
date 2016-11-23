<?php

class Icube_Cancellationreasons_Model_Cancellationreasons extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('cancellationreasons/cancellationreasons');
	}
}