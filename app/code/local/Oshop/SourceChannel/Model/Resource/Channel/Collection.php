<?php

class Oshop_SourceChannel_Model_Resource_Channel_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('sourcechannel/channel');
    }
}