<?php
 
class Icube_Autocancel_Adminhtml_AutocancelController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('icube/autocancel')
            ->_title($this->__('Icube'))->_title($this->__('Auto Cancel'))
            ->_addBreadcrumb($this->__('Icube'), $this->__('Autocancel'));
         
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction();
        $this->_addContent(
            $this->getLayout()->createBlock('icube_autocancel/adminhtml_autocancel')
        );

        $this->renderLayout();
    }
 
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('icube_autocancel/adminhtml_autocancel_grid')->toHtml()
        );
    }

    /**
     * Create new autocancel action
     */
    public function newAction()
    {  
        $this->_initAction();
     
        // Get id if available
        $id  = $this->getRequest()->getParam('id');
        $model = Mage::getModel('autocancel/autocancel');
     
        if ($id) {
            // Load record
            $model->load($id);
     
            // Check if record is loaded
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This Autocancel no longer exists.'));
                $this->_redirect('*/*/');
     
                return;
            }  
        }  
     
        $this->_title($model->getId() ? $model->getName() : $this->__('New Autocancel'));
     
        $data = Mage::getSingleton('adminhtml/session')->getAutocancelData(true);
        if (!empty($data)) {
            $model->setData($data);
        }  
     
        Mage::register('icube_autocancel', $model);
     
        $this->_initAction()
            ->_addBreadcrumb($id ? $this->__('Edit Autocancel') : $this->__('New Autocancel'), $id ? $this->__('Edit Autocancel') : $this->__('New Autocancel'))
            ->_addContent($this->getLayout()->createBlock('icube_autocancel/adminhtml_autocancel_edit')->setData('action', $this->getUrl('*/*/save')))
            ->renderLayout();
    }

    /**
     * Edit autocancel action
     */
    public function editAction()
    {
        $this->_forward('new');
    }
 
    /**
     * Delete autocancel action
     */
    public function deleteAction()
    {
        $autocancel = Mage::getModel('autocancel/autocancel');
        if ($id = (int)$this->getRequest()->getParam('id')) {
            try {
                $autocancel->load($id);
                $autocancel->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('icube_autocancel')->__('The autocancel has been deleted.'));
                $this->getResponse()->setRedirect($this->getUrl('*/autocancel'));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->getResponse()->setRedirect($this->getUrl('*/autocancel/edit', array('id' => $id)));
                return;
            }
        }

        $this->_redirect('*/autocancel');
    }

    /**
     * Create or save autocancel.
     */
    public function saveAction()
    {
        $autocancel = Mage::getModel('autocancel/autocancel');
        $id = $this->getRequest()->getParam('id');
        if (!is_null($id)) {
            $autocancel->load((int)$id);
        }

        $data = $this->getRequest()->getPost();

        if ($data) {
            try {

                $autocancel
                    ->setOrderPayment($data['order_payment'])
                    ->setOrderStatus($data['order_status'])
                    ->setTime($data['time'])
                    ->setReasonId($data['reason_id'])
                    ->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('icube_autocancel')->__('The autocancel has been saved.'));
                $this->getResponse()->setRedirect($this->getUrl('*/autocancel'));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setAutocancelData($autocancel->getData());
                $this->getResponse()->setRedirect($this->getUrl('*/autocancel/edit', array('id' => $id)));
                return;
            }
        } else {
            $this->_forward('new');
        }
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('icube/icube_autocancel');
    }
}