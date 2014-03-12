<?php
class Sinch_Pricerules_Model_Attribute_Source_Group extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {


    public function getAllOptions(){
        $groups = array();
        foreach(Mage::getModel('sinch_pricerules/group')->getOptionArray() as $id => $name){
            $groups[] = array(
                'value' => $id,
                'label' => $name
            );
        }
        return $groups;
    }
}