<?php

class Icube_Autocancel_Block_Adminhtml_Autocancel_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Init class
     */
    public function __construct()
    {  
        parent::__construct();
     
        $this->setId('icube_autocancel_autocancel_form');
        $this->setTitle($this->__('Autocancel Information'));
    }  
     
    /**
     * Setup form fields for inserts/updates
     *
     * return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {  
        parent::_prepareLayout();
        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method'    => 'post'
        ));
        $model = Mage::registry('icube_autocancel');
     
        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'    => Mage::helper('icube_autocancel')->__('Autocancel Information')
        ));
     
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array(
                'name' => 'id',
            ));
        }  
     
        $fieldset->addField('order_payment', 'select',
            array(
                'name'  => 'order_payment',
                'label' => Mage::helper('icube_autocancel')->__('Payment'),
                'title' => Mage::helper('icube_autocancel')->__('Payment'),
                'class' => 'required-entry',
                'required' => true,
                'values' => Mage::helper('payment')->getPaymentMethodList(true, true, true),
            )
        );

        $fieldset->addField('order_status', 'select',
            array(
                'name'  => 'order_status',
                'label' => Mage::helper('icube_autocancel')->__('Order Status'),
                'title' => Mage::helper('icube_autocancel')->__('Order Status'),
                'class' => 'required-entry',
                'required' => true,
                'values' => Mage::getSingleton('sales/order_config')->getStatuses(),
            )
        );

        $fieldset->addField('time', 'text', array(
            'name'      => 'time',
            'label'     => Mage::helper('icube_autocancel')->__('Time (Hours)'),
            'title'     => Mage::helper('icube_autocancel')->__('Time (Hours)'),
            'class' => 'required-entry',
            'required'  => true,
        ));
     
        $fieldset->addField('reason_id', 'select', array(
            'name'      => 'reason_id',
            'label'     => Mage::helper('icube_autocancel')->__('Reason'),
            'title'     => Mage::helper('icube_autocancel')->__('Reason'),
            'class' => 'required-entry',
            'required'  => true,
            'values' => Mage::helper('icube_autocancel')->getCancellationReasonsList(),
        ));
     
        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);
     
        return parent::_prepareForm();
    }  
}