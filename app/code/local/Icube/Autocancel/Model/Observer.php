<?php
class Icube_Autocancel_Model_Observer
{


	public function orderSaveAfter($observer)  
	{

	    $session = Mage::getSingleton('admin/session');
	    $order 	= $observer->getEvent()->getOrder();
	    $paymentCode = $order->getPayment()->getMethodInstance()->getCode();
		$autocancelOrders = Mage::getModel('autocancel/orders');

	    /*check autocancel condition
	    */
	    $autocancel = Mage::getModel('autocancel/autocancel')->getCollection()
	    				->addFieldToFilter('order_payment', $paymentCode)
	    				->addFieldToFilter('order_status', $order->getStatus());
        // Start Check AutoCancel to Prevent extends duration for the same order status
        Mage::log("======================= Start Check Autocancel ==================", null, 'icube_autocancel_orders.log');
        $checks = Mage::getModel('autocancel/orders')->getCollection();
        $checks->addFieldToFilter('order_id',$order->getId());
        $order_status = '';
        Mage::log(count($checks->getItems()));
        if(count($checks->getItems()) != 0){
            foreach($checks->getItems() as $check){
                Mage::log("order status di table autocancel : ".$check->getOrderStatus(), null, 'icube_autocancel_orders.log');
                $order_status = $check->getOrderStatus();
            }
        }
        Mage::log("order status di table order : ".$order->getStatus(), null, 'icube_autocancel_orders.log');
        if($order->getStatus() != $order_status){
            Mage::log('autocancel AKAN menambah durasi jika order_status != status di order', null, 'icube_autocancel_orders.log');
            // Pause Check Autocancel / Do as Usual Autocancel
    	    $autocancelOrders->getCollection()->addFieldToFilter('order_id', $order->getId())->walk('delete');	//delete the record from icube_autocancel_order table

    	    if(count($autocancel)>0){    
    	    	$autocancel = $autocancel->getFirstItem();
    	    	$now = Mage::getModel('core/date')->timestamp(time());
    	    	$datetime = strtotime("+".$autocancel->getTime()." hours", $now);	//calculate expiration date

    			$data = array(
    	    			'order_id'=>$order->getId(), 
    	    			'order_status'=>$order->getStatus(),
    	    			'datetime'=>$datetime,
    	    			'reason_id'=>$autocancel->getReasonId(),
    	    			);
    		    try {
    			    $autocancelOrders = $autocancelOrders->setData($data);
    				$insertId = $autocancelOrders->save()->getId();
    			    Mage::log("Data successfully inserted. order id: ".$order->getId()." order status: ".$order->getStatus(), null, 'icube_autocancel_orders.log');
    			} catch (Exception $e){
    			 	Mage::log($e->getMessage(), null, 'icube_autocancel_orders.log');   
    			}
    		}
            // Resume Check Autocancel / Do as Usual Autocancel
        }else{
            Mage::log('order status == status di icube autocancel: '.$order_status.' dan '.$order->getStatus(), null, 'icube_autocancel_orders.log');
        }
        // End Check AutoCancel to Prevent extends duration for the same order status
		return $this;

	}  


	public function autocancelOrders()
	{
		$orderCollection = Mage::getModel('autocancel/orders')->getCollection();
        $now = Mage::getModel('core/date')->timestamp(time());

        /* search orders that have expiration datetime less than or equals to now
        */
        $orderCollection
                ->addFieldToFilter('datetime', array('lteq' => date("Y-m-d H:i:s", $now  )));
        
        foreach($orderCollection->getItems() as $order)
        {
            $orderModel = Mage::getModel('sales/order');
            $orderModel->load($order['order_id']);

            if ($orderModel->hasInvoices()) {
                $message = 'reminder : team finance harus melakukan refund / credit memo manually';
                //get department id
                $departmentId = Mage::getModel('aw_hdu3/department')->getCollection()->addFieldToFilter('title', 'Customer Service')->getFirstItem()->getId();
                //get status id for 'New'
                $resource = Mage::getSingleton('core/resource');
                $readConnection = $resource->getConnection('core_read');
                $query = 'SELECT `status_id` FROM `' . $resource->getTableName('aw_hdu3/ticket_status_label') . '` WHERE `value` = "New"';
                $results = $readConnection->fetchAll($query);
                $ticketStatusId = $results[0]['status_id'];
                //get department agent id
                $departmentAgentId = Mage::getModel('aw_hdu3/department_agent')->getCollection()->addFieldToFilter('name', 'Customer Service')->getFirstItem()->getId();
                //get priority id
                $query = 'SELECT `priority_id` FROM `' . $resource->getTableName('aw_hdu3/ticket_priority_label') . '` WHERE `value` = "To Do"';
                $results = $readConnection->fetchAll($query);
                $priorityId = $results[0]['priority_id'];
                //get store id
                $storeId = Mage::app()->getStore()->getStoreId();

                $ticket = Mage::getModel('aw_hdu3/ticket');
                $ticket
                ->setDepartmentId($departmentId)
                ->setDepartmentAgentId($departmentAgentId)
                ->setCustomerEmail($orderModel->getCustomerEmail())
                ->setCustomerName($orderModel->getCustomerFirstname() . $orderModel->getCustomerLastname())
                ->setStatus($ticketStatusId)
                ->setPriority($priorityId)
                ->setStoreId($storeId)
                ->setSubject('Order #'.$orderModel->getIncrementId().' Credit Memo')
                ->setOrderIncrementId($orderModel->getIncrementId())
                ->save();
                $ticketVariables = Mage::helper('aw_hdu3/ticket')->getTicketVariables($ticket);
                $ticket->addHistory(AW_Helpdesk3_Model_Ticket_History_Event_Message::TYPE,
                    array(
                        'content' => Mage::helper('aw_hdu3/ticket')->getParsedContent($message, $ticketVariables),
                        'attachments' => ''
                        )
                    );
                
                $this->deleteRecord($orderModel->getId());
                Mage::log("Order #".$orderModel->getIncrementId()." has invoice(s), unable to canceled", null, 'icube_autocancel_orders_list.log');

                continue;
            }

            if(!$orderModel->canCancel()) {
                $this->deleteRecord($orderModel->getId());
                Mage::log("Order #".$orderModel->getIncrementId()." unable to canceled", null, 'icube_autocancel_orders_list.log');
                
                continue;
            }
 
            $orderModel->cancel();
            if($orderModel->save()){

                $this->deleteRecord($orderModel->getId());

                $this->setCancellationreasons($orderModel, $order['reason_id']);

                Mage::log("Order successfully canceled. Order ID: ".$order['order_id'], null, 'icube_autocancel_orders_list.log');
            }
        }

        return $this;
	}

    
    public function deleteRecord($orderId)
    {
        $autocancelOrders = Mage::getModel('autocancel/orders');
        $autocancelOrders->getCollection()->addFieldToFilter('order_id', $orderId)->walk('delete');
    }


	
	/**
     * 
     *  insert to cancellation-reasons table
     */
    public function setCancellationreasons($order, $reason_id)
    {

        $now = Mage::getModel('core/date')->timestamp(time());
        $data = array( 
                'order_id'  =>  $order->getId(),
                'reason_cancel'  =>  $reason_id,
                'source'    => "system",
                'created_at' => $now
                );

        $model = Mage::getModel('cancellationreasons/cancellationreasons');
                        $model->setData($data);
                        $model->save();

        return $this;
    }


}