<?php
/**
 * Price Rules item resource model
 *
 * @author Stock in the Channel
 */
 
class Sinch_Pricerules_Model_Resource_Pricerules extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('sinch_pricerules/pricerules', 'pricerules_id');
    }
}