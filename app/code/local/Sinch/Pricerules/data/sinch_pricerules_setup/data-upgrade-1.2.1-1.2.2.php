<?php
/**
 * Data Upgrade Script 1.2.1-1.2.2
 *
 * @author Stock in the Channel
 */
$pricerules = Mage::getModel('sinch_pricerules/pricerules')->getCollection();
$groupModel = Mage::getModel('sinch_pricerules/group');

$firstGroup = Mage::getModel('sinch_pricerules/group')->loadByGroupId(0);
if(!$firstGroup->hasData()){
    $newGroup = Mage::getModel('sinch_pricerules/group')->addData(array(
        'group_id'          => 0,
        'group_name'        => 'Default Group',
        'is_manually_added' => false
    ))->save();
} //Add the Default Group if it doesn't already exist

$preUpgradeGroups = array();

foreach($pricerules as $rule){
    $value = $rule->getData('group_id');
    if($value != null){
        $preUpgradeGroups[] = $value;
    }
}

$preUpgradeGroups = array_unique($preUpgradeGroups);

foreach($preUpgradeGroups as $groupId){
    if(!$groupModel->loadByGroupId($groupId)->hasData()){
        $newGroup = Mage::getModel('sinch_pricerules/group')->addData(array(
            'group_id' => $groupId,
            'group_name' => "Group " . $groupId,
            'is_manually_added' => false
        ))->save();
    }
}