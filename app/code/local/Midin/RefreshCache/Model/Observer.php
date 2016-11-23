<?php

class Midin_RefreshCache_Model_Observer {
	function refreshCache(){
		Mage::log('refreshCache nih');
		try {
			$allTypes = Mage::app()->useCache();
			foreach($allTypes as $type => $blah) {
				Mage::log('type to clean : '.$type);
				Mage::app()->getCacheInstance()->cleanType($type);
			}
			Mage::log('done');
		} catch (Exception $e) {
			// do something
			Mage::log($e->getMessage());
		}
	}

	function productPrepareSave(){
		Mage::log('productPrepareSave');
	}
}