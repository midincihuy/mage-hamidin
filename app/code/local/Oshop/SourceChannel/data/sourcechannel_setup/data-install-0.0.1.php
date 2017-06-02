<?php

$channels =  array(
    array(
        'channel_code' => 'OchannelTV',
        'channel_text' => 'Ochannel TV',
        'channel_sort' => '1',
    ),
    array(
        'channel_code' => 'Website',
        'channel_text' => 'Website',
        'channel_sort' => '2',
    ),
);
 
foreach ($channels as $channel) {
    Mage::getModel('sourcechannel/source')
        ->setData($channel)
        ->save();
}