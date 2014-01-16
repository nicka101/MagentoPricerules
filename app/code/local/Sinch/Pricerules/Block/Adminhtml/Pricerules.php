<?php
/**
 * Price Rules List admin grid container
 *
 * @author Stock in the Channel
 */
 
class Sinch_Pricerules_Block_Adminhtml_Pricerules extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'sinch_pricerules';
        $this->_controller = 'adminhtml_pricerules';
        $this->_headerText = Mage::helper('sinch_pricerules')->__('Manage Price Rules');

        parent::__construct();
    }
}