<?php
/**
 * Price Rules List admin edit form tabs block
 *
 * @author Stock in the Channel
 */
class Sinch_Pricerules_Block_Adminhtml_Pricerules_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('sinch_pricerules')->__('Price Rules Info'));
    }
}
