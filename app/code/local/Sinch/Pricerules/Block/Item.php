<?php
/**
 * Price Rule Item block
 *
 * @author Stock in the Channel
 */
 
class Sinch_Pricerules_Block_Item extends Mage_Core_Block_Template
{
    protected $_item;

    protected function _getBackUrlQueryParams($additionalParams = array())
    {
        return array_merge(array('p' => $this->getPage()), $additionalParams);
    }

    public function getBackUrl()
    {
        return $this->getUrl('*/', array('_query' => $this->_getBackUrlQueryParams()));
    }
}
