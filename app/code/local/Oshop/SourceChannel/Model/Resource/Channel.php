<?php

class Oshop_SourceChannel_Model_Resource_Channel extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('sourcechannel/channelorder_table', 'co_id');
    }
}