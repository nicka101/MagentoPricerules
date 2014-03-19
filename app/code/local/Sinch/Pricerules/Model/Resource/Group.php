<?php
/**
 * Pricerules Group Resource
 *
 * @author Stock in the Channel
 */
class Sinch_Pricerules_Model_Resource_Group extends Mage_Core_Model_Resource_Db_Abstract {

    protected function _construct(){
        $this->_init('sinch_pricerules/group', 'entity_id');
    }
}