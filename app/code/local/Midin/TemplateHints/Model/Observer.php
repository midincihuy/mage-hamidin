<?php

class Midin_TemplateHints_Model_Observer{
	function core_block_abstract_to_html_after($observer){
		$enable = Mage::getStoreConfig('midin_templatehints/midin_templatehints/enable');
		// Mage::log('==========> core_block_abstract_to_html_after '.$enable);
		if($enable){
			/* @var $block Mage_Core_Block_Abstract */
	        $block              = $observer->getBlock();
	        $transport          = $observer->getTransport();
	 
	        $fileName           = $block->getTemplateFile();
	        $thisClass          = get_class($block);
	        if($fileName){
	            $preHtml = '<div style="position:relative; border:1px dotted red; margin:6px 2px; padding:18px 2px 2px 2px; zoom:1;">
	<div style="position:absolute; left:0; top:0; padding:2px 5px; background:green; color:white; font:normal 11px Arial;
	text-align:left !important; z-index:998;" onmouseover="this.style.zIndex=\'999\'"
	onmouseout="this.style.zIndex=\'998\'" title="'.$fileName.'">'.$fileName.'</div>';
	            $preHtml .= '<div style="position:absolute; right:0; top:0; padding:2px 5px; background:grey; color:blue; font:normal 11px Arial;
	    text-align:left !important; z-index:998;" onmouseover="this.style.zIndex=\'999\'" onmouseout="this.style.zIndex=\'998\'"
	    title="'.$thisClass.'">'.$thisClass.'</div>';
	 
	            $postHtml = '</div>';
	        }else{
	            $preHtml    = null;
	            $postHtml   = null;
	        }
	 
	 
	        $html = $transport->getHtml();
	        $html = $preHtml . $html . $postHtml;
	        $transport->setHtml($html);
    	}
	}
}