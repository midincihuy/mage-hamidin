<?php

class Oshop_SourceChannel_Model_Resource_Source extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('sourcechannel/sourcechannel_table', 'source_id');
    }
}