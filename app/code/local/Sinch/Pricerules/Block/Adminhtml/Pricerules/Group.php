<?php
class Sinch_Pricerules_Block_Adminhtml_Pricerules_Group extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'sinch_pricerules';
        $this->_controller = 'adminhtml_pricerules_group';
        $this->_headerText = Mage::helper('sinch_pricerules')->__('Manage Groups');

        parent::__construct();
    }
}