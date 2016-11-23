<?php

class Icube_Cancellationreasons_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getCancellationreasonsAssoc()
	{
		$data = Array();
		foreach(Mage::getResourceModel('cancellationreasons/list_collection')->load() as $list)
			$data[$list['entity_id']] = $list['message'];
		return $data;
	}
}