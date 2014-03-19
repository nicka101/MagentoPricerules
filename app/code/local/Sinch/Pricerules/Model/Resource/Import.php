<?php
/**
 * Pricerules Import Table Resource
 *
 * @author Stock in the Channel
 */
class Sinch_Pricerules_Model_Resource_Import extends Mage_Core_Model_Resource_Db_Abstract {
    protected function _construct(){
        $this->_init('sinch_pricerules/import', 'pricerules_id');
    }
}