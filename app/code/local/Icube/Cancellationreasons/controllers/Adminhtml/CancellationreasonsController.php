<?php

class Icube_Cancellationreasons_Adminhtml_CancellationreasonsController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    protected function _initOrder()
    {
        $id = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($id);

        if (!$order->getId()) {
            $this->_getSession()->addError($this->__('This order no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        Mage::register('sales_order', $order);
        Mage::register('current_order', $order);
        return $order;
    }
	
	protected function _saveHistoryCancel($order, $reason)
    {
        $orderId        = $order->getId();
        $orderStatus    = $order->getStatus();

        $user = Mage::getSingleton('admin/session')->getUser();
        $source = $user->getRole()->getData();
        $dataUser = $user->getData();

        $date = new DateTime();
        $data = array( 
                'order_id'  =>  $orderId,
                'reason_cancel' => $reason,
                'source'    => $source["role_name"] ,
                'created_at' => $date->getTimestamp(),
                'user'   => $dataUser['firstname'].' '.$dataUser['lastname'] );
        
        $model = Mage::getModel('cancellationreasons/cancellationreasons');
                        $model->setData($data);
                        $model->save();
    }
    
    public function cancelAction(){
    	$reason = $this->getRequest()->getParam('reason');
    	
		if ($order = $this->_initOrder()) {
            try {      
                if (!$order->hasInvoices() && (strcmp($order->getPayment()->getMethod(), 'cashondelivery') == 0) && (strcmp($order->getStatus(), 'cod_changed') == 0)) {
                    $order->cancel();
                    $order->setData('state', Mage_Sales_Model_Order::STATE_PROCESSING);
                    $order->setStatus('cod_changed');
                    $order->save();
                    $history = $order->addStatusHistoryComment('The order has been cancelled and order status changed to "Order Changed"', false);
                    $history->setIsCustomerNotified(false);
                    $session = Mage::getSingleton('admin/session');
                    if ($session->isLoggedIn()) { //only for login admin user
                        $user = $session->getUser();
                        $history->setUserId($user->getId());
                        $role = $user->getRole(); //if you have the column userrole
                        $history->setTypeUser($role->getRoleName()); //you can save it too         
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('The order has been cancelled and order status changed to "Order Changed"')
                    ); 
                    $this->_saveHistoryCancel($order,$reason);
                }
                else {
                    $order->cancel();
                    $order->save();   
                    $this->_getSession()->addSuccess(
                        $this->__('The order has been cancelled.')
                    ); 
                    $this->_saveHistoryCancel($order,$reason);
                }
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addError($this->__('The order has not been cancelled.'));
                Mage::logException($e);
            }
            $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
        }
	}
	
    public function massCancelAction(){
		
	}

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('icube/cancellationreasons');
    }
}