<?php
class Midin_Base_Block_Sales_Order_Info extends Mage_Sales_Block_Order_Info
{
	public function getPaymentInfoHtml()
	{
		$payment = $this->getChildHtml('payment_info');
		$method = $this->getOrder()->getPayment()->getMethodInstance()->getCode();
		if($method == 'indomaret'){
			$payment .= "ini pake indomaret";
		}
	}
}