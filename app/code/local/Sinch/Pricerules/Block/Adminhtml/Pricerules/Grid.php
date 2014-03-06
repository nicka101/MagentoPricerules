<?php
/**
 * Price Rules List admin grid
 *
 * @author Stock in the Channel
 */
 
class Sinch_Pricerules_Block_Adminhtml_Pricerules_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('pricerules_list_grid');
        $this->setDefaultSort('execution_order');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('sinch_pricerules/pricerules')->getResourceCollection();
		$collection->getSelect()
			->reset(Zend_Db_Select::COLUMNS)
			->columns(array('pricerules_id', 'price_from', 'price_to', 'markup_percentage', 'markup_price', 'absolute_price', 'execution_order', 'group_id'))
			->joinLeft(array('ccev' => 'catalog_category_entity_varchar'), 'ccev.entity_id = main_table.category_id and ccev.attribute_id = 41 and store_id = 0', array('category_name' => 'ccev.value'))
			->joinLeft(array('eaov' => 'eav_attribute_option_value'), 'eaov.option_id = main_table.brand_id', array('brand_name' => 'eaov.value'))
			->joinLeft(array('cpe' => 'catalog_product_entity'), 'cpe.entity_id = main_table.product_id', array('product_sku' => 'cpe.sku'))
            ->joinLeft(array('spg' => 'sinch_pricerules_groups'), 'spg.group_id = main_table.group_id', array('group_name' => 'spg.group_name'));
			
		//$collection->printlogquery(true);exit;
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('pricerules_id', array(
            'header' => Mage::helper('sinch_pricerules')->__('ID'),
            'width' => '50px',
            'index' => 'pricerules_id',
			'type' => 'int'
        ));
		
		$this->addColumn('price_from', array(
            'header' => Mage::helper('sinch_pricerules')->__('Price From'),
            'width' => '50px',
            'index' => 'price_from',
			'type' => 'decimal'
        ));
		
		$this->addColumn('price_to', array(
            'header' => Mage::helper('sinch_pricerules')->__('Price To'),
            'width' => '50px',
            'index' => 'price_to',
			'type' => 'decimal'
        ));
		
		$this->addColumn('category_name', array(
            'header' => Mage::helper('sinch_pricerules')->__('Category'),
            'width' => '50px',
			'index' => 'category_name',
            'filter_index' => 'ccev.value',
			'type' => 'varchar'
        ));
		
		$this->addColumn('brand_name', array(
            'header' => Mage::helper('sinch_pricerules')->__('Brand'),
            'width' => '50px',
            'index' => 'brand_name',
			'filter_index' => 'eaov.value',
			'type' => 'varchar'
        ));
		
		$this->addColumn('product_sku', array(
            'header' => Mage::helper('sinch_pricerules')->__('Product SKU'),
            'width' => '50px',
            'index' => 'product_sku',
			'filter_index' => 'cpe.sku',
			'type' => 'varchar'
        ));
		
		$this->addColumn('group_name', array(
            'header' => Mage::helper('sinch_pricerules')->__('Price Group'),
            'width' => '50px',
            'index' => 'group_name',
            'filter_index' => 'spg.group_name',
			'type' => 'varchar'
        ));
		
		$this->addColumn('markup_percentage', array(
            'header' => Mage::helper('sinch_pricerules')->__('Markup Percentage'),
            'width' => '50px',
            'index' => 'markup_percentage',
			'type' => 'decimal'
        ));
		
		$this->addColumn('markup_price', array(
            'header' => Mage::helper('sinch_pricerules')->__('Markup Price'),
            'width' => '50px',
            'index' => 'markup_price',
			'type' => 'decimal'
        ));
		
		$this->addColumn('absolute_price', array(
            'header' => Mage::helper('sinch_pricerules')->__('Absolute Price'),
            'width' => '50px',
            'index' => 'absolute_price',
			'type' => 'decimal'
        ));
		
		$this->addColumn('execution_order', array(
            'header' => Mage::helper('sinch_pricerules')->__('Execution Order'),
            'width' => '50px',
            'index' => 'execution_order',
			'type' => 'int'
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('sinch_pricerules')->__('Action'),
                'width'     => '100px',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(array(
                    'caption' => Mage::helper('sinch_pricerules')->__('Edit'),
                    'url'     => array('base' => '*/*/edit'),
                    'field'   => 'id'
                )),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'pricerules',
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