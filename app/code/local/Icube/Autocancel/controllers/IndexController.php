<?php
class Icube_Autocancel_IndexController extends Mage_Core_Controller_Front_Action
{

    public function autocancelOrdersAction()
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