<?php
/**
 * Price Rules Group List admin grid
 *
 * @author Stock in the Channel
 */

class Sinch_Pricerules_Block_Adminhtml_Pricerules_Group_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('pricerules_list_grid');
        $this->setDefaultSort('group_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('sinch_pricerules/group')->getResourceCollection();

        //$collection->printlogquery(true);exit;
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('sinch_pricerules')->__('ID'),
            'width' => '50px',
            'index' => 'entity_id',
            'type' => 'int'
        ));

        $this->addColumn('group_id', array(
            'header' => Mage::helper('sinch_pricerules')->__('Group ID'),
            'width' => '50px',
            'index' => 'group_id',
            'type' => 'int'
        ));

        $this->addColumn('group_name', array(
            'header' => Mage::helper('sinch_pricerules')->__('Group Name'),
            'width' => '50px',
            'index' => 'group_name',
            'type' => 'varchar'
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}