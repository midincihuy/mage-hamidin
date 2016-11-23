<?php

class Icube_Cancellationreasons_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{   
	public function __construct()
    {
    	parent::__construct();
    }
    
	protected function _prepareMassaction(){
		parent::_prepareMassaction();    
        
        $this->getMassactionBlock()->removeItem('cancel_order');
        
        return $this;
	}
}