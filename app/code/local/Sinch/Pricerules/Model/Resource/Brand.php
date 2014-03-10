<?php
class Sinch_Pricerules_Model_Resource_Brand extends Mage_Core_Model_Resource_Db_Abstract {

    protected function _construct(){
        $this->_init('sinch_pricerules/brand', 'brand_id');
    }
}