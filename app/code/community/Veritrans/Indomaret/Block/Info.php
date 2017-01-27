<?php
/**
 * Veritrans VT direct Permata virtual account form block
 *
 * @category   Mage
 * @package    Mage_Veritrans_Permatava_Block_Form
 * when Veritrans payment method is chosen, permatava/info.phtml template will be rendered at the right side, in progress bar.
 */
class Veritrans_Indomaret_Block_Info extends Mage_Payment_Block_Info {
    
    protected function _construct() {
      parent::_construct();
		  $this->setInfoMessage( Mage::helper('indomaret/data')->_getInfoTypeIsImage() == true ? 
		  '<img src="'. $this->getSkinUrl('images/Veritrans.png'). '"/>' : '<b>'. Mage::helper('indomaret/data')->_getTitle() . '</b>');
		  $this->setPaymentMethodTitle( Mage::helper('indomaret/data')->_getTitle() );
      $this->setTemplate('indomaret/info.phtml');
    }

    public function getOrder()
	{
		$order_id = Mage::app()->getRequest()->getParam('order_id');
		return Mage::getModel("sales/order")->load($order_id);
	}
	public function getVadetail($order)
	{
		$table = Mage::getSingleton('core/resource');
		$readAdapter = $table->getConnection('core_read');
		$sql        = "Select * from sales_order_virtualaccount where order_id=".$order;
		try{
			$rows       = $readAdapter->fetchAll($sql);
			return $rows;
		}catch(Exception $e){
			Mage::log("VA not found ");
			return false;
		}
	}
    
}
