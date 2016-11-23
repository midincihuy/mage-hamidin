<?php

class Icube_Autocancel_Helper_Data extends Mage_Core_Helper_Abstract
{

	public function getCancellationReasonsList()
	{
		
		$collection = Mage::getModel('cancellationreasons/list')->getCollection();

		foreach ($collection as $key => $value) {
            $v[$key]['label'] = $value['message'];
            $v[$key]['value'] = $value['entity_id'];
        }

        return $v;
	}

}