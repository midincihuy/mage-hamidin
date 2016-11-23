<?php

class Icube_Cancellationreasons_Block_Adminhtml_Sales_Order_View extends Mage_Adminhtml_Block_Sales_Order_View
{
	public function __construct(){
		parent::__construct();
		// check wheter Order can cancel
		if (Mage::registry('current_order')->canCancel())
		{
			$reasons =	Mage::getResourceModel('cancellationreasons/list_collection')->load();
			$reasonsHtml = '<select id="cancellationreasons" style="width:200px;margin-left:10px;">';
			foreach($reasons as $list){
				$reasonsHtml .= '<option value="'. $list['entity_id'] .'">'. $list['message'].'</p>';
			}
			$reasonsHtml.= '</select>';
			$reasonsHtml.= '<script type="text/javascript">';
			$reasonsHtml.= "var reason= 1;
						Event.observe($('cancellationreasons'),'change',function(){
							reason = $('cancellationreasons').value;
						});";
			$reasonsHtml.= 'function cancelOrder() { deleteConfirm("Are you sure you want to cancel this order?","'.$this->getUrl('*/cancellationreasons/cancel').'reason/"+reason) }';
			$reasonsHtml.= '</script>';
			
			$this->_removeButton('order_cancel');
			 if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/cancel')) {
			 	if($this->getOrder()->getState()!='canceled'){
					$this->_addButton('cancel',array(
						'label' => Mage::helper('sales')->__('Cancel'),
						'onclick' => 'cancelOrder()',
						'before_html' => $reasonsHtml
					));
				}
			}
		}
	}
}