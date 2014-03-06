<?php
class Sinch_Pricerules_Model_Group extends Mage_Core_Model_Abstract {

    protected function _construct(){
        $this->_init('sinch_pricerules/group');
    }

    public function loadByGroupId($id){
        return $this->getCollection()->addFilter('group_id', $id)->getFirstItem();
    }

    public function getName(){
        return $this->getData('group_name');
    }

    public function getGroupId(){
        return $this->getData('group_id');
    }

    public function getOptionArray(){
        $groups = array();
        foreach($this->getCollection() as $group){
            $groups[$group->getGroupId()] = $group->getName();
        }
        return $groups;
    }
}