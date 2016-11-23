<?php
 
class Icube_Autocancel_Block_Adminhtml_Autocancel_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('autocancelGrid');
        
        $this->setDefaultSort('autocancel_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(true);
    }
 
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('autocancel/autocancel')->getCollection();
        $collection->getSelect()->join( array('reasons'=> icube_cancellation_reasons_list), 'reasons.entity_id = main_table.reason_id', array('reason' => 'reasons.message'));

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
 
    protected function _prepareColumns()
    {
        $this->addColumn('autocancel_id', array(
            'header'    => Mage::helper('icube_autocancel')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'autocancel_id',
        ));
 
        $this->addColumn('payment', array(
            'header'    => Mage::helper('icube_autocancel')->__('Payment Code'),
            'align'     =>'left',
            'index'     => 'order_payment',
        ));
 
        
        $this->addColumn('status', array(
            'header'    => Mage::helper('icube_autocancel')->__('Status Code'),
            'align'     =>'left',
            'index'     => 'order_status',
        ));
        
        $this->addColumn('time', array(
            'header'    => Mage::helper('icube_autocancel')->__('Time (Hours)'),
            'align'     =>'center',
            'width'     => '20px',
            'index'     => 'time',
        ));

        $this->addColumn('reason', array(
            'header'    => Mage::helper('icube_autocancel')->__('Reason'),
            'align'     =>'left',
            'filter_index' => 'reasons.message',
            'index'     => 'reason',
        ));
        
        
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('icube_autocancel')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('icube_autocancel')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

 
        return parent::_prepareColumns();
    }
 
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
 
    public function getGridUrl()
    {
      return $this->getUrl('*/*/grid', array('_current'=>true));
    }
 
 
}