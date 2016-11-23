<?php

class Icube_Cancellationreasons_Block_Adminhtml_Sales_Order_Cancellationreasons extends Mage_Adminhtml_Block_Template
{
	function getCancellationreasonsList(){
		return Mage::getResourceModel('cancellationreasons/list_collection')->load();
	}
	
}