<?php

class Sinch_Pricerules_Model_Indexer extends Mage_Index_Model_Indexer_Abstract
{
	protected $_matchedEntities = array(
		'sinch_pricerules' => array(
			Mage_Index_Model_Event::TYPE_SAVE
		)
	);
 
	public function getName()
	{
		return 'Sinch Price Rules';
	}
 
	public function getDescription()
	{
		return 'Rebuild price rule prices';
	}
 
	protected function _registerEvent(Mage_Index_Model_Event $event)
	{
	}
 
	protected function _processEvent(Mage_Index_Model_Event $event)
	{
	}
 
	public function reindexAll()
	{
		$this->doReindexAll();
	}
}