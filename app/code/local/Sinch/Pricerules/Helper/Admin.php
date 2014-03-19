<?php
/**
 * Price Rules Admin helper
 *
 * @author Stock in the Channel
 */
 
class Sinch_Pricerules_Helper_Admin extends Mage_Core_Helper_Abstract
{
    public function isActionAllowed($area, $action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('pricerules/' . $area . '_manage/' . $action);
    }
}