<?php
/**
 * Price rules installation script
 *
 * @author Stock in the Channel
 */

$installer = $this;

$table = $installer->getConnection()
    ->newTable($installer->getTable('sinch_pricerules/pricerules'))
    ->addColumn('pricerules_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'identity' => true,
        'nullable' => false,
        'primary'  => true
    ), 'Entity id')
	->addColumn('price_from', Varien_Db_Ddl_Table::TYPE_DECIMAL, '10,2', array(
        'nullable' => true,
        'default'  => null
    ), 'Price from')
	->addColumn('price_to', Varien_Db_Ddl_Table::TYPE_DECIMAL, '10,2', array(
        'nullable' => true,
        'default'  => null
    ), 'Price to')
	->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => true,
        'default'  => null
    ), 'Category id')
	->addColumn('brand_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => true,
        'default'  => null
    ), 'Brand id')
	->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => true,
        'default'  => null
    ), 'Product id')
	->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false
    ), 'Customer group id')
	->addColumn('markup_percentage', Varien_Db_Ddl_Table::TYPE_DECIMAL, '10,2', array(
        'nullable' => true,
        'default'  => null,
    ), 'Markup percentage')
	->addColumn('markup_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '10,2', array(
        'nullable' => true,
        'default'  => null,
    ), 'Markup price')
	->addColumn('absolute_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '10,2', array(
        'nullable' => true,
        'default'  => null,
    ), 'Absolute price')
	->addColumn('execution_order', Varien_Db_Ddl_Table::TYPE_INTEGER, 0, array(
        'nullable' => false
    ), 'Absolute price')
	->addColumn('is_manually_added', Varien_Db_Ddl_Table::TYPE_BOOLEAN, 0, array(
        'nullable' => false
    ), 'Absolute price')
    ->setComment('Price rules');

$installer->getConnection()->createTable($table);

$installer->getConnection()
    ->addKey(
        $installer->getTable('sinch_pricerules/pricerules'), 
        'IDX_sinch_pricerules_category_id', 
        'category_id'
    );
	
$installer->getConnection()
    ->addConstraint(
        'FK_sinch_pricerules_catalog_category_entity',
        $installer->getTable('sinch_pricerules/pricerules'),		
        'category_id',
        $installer->getTable('catalog_category_entity'), 
        'entity_id',
        'cascade', 
        'cascade'
	);
	
$installer->getConnection()
    ->addKey(
        $installer->getTable('sinch_pricerules/pricerules'), 
        'IDX_sinch_pricerules_brand_id', 
        'brand_id'
    );
	
$installer->getConnection()
    ->addConstraint(
        'FK_sinch_pricerules_eav_attribute_option',
        $installer->getTable('sinch_pricerules/pricerules'),		
        'brand_id',
        $installer->getTable('eav_attribute_option'), 
        'option_id',
        'cascade', 
        'cascade'
	);
	
$installer->getConnection()
    ->addKey(
        $installer->getTable('sinch_pricerules/pricerules'), 
        'IDX_sinch_pricerules_product_id', 
        'product_id'
    );
	
$installer->getConnection()
    ->addConstraint(
        'FK_sinch_pricerules_catalog_product_entity',
        $installer->getTable('sinch_pricerules/pricerules'),		
        'product_id',
        $installer->getTable('catalog_product_entity'), 
        'entity_id',
        'cascade', 
        'cascade'
	);

$installer->getConnection()
    ->addKey(
        $installer->getTable('sinch_pricerules/pricerules'), 
        'IDX_sinch_pricerules_customer_group_id', 
        'customer_group_id'
    );
	
$installer->getConnection()
    ->addConstraint(
        'FK_sinch_pricerules_customer_group',
        $installer->getTable('sinch_pricerules/pricerules'),		
        'customer_group_id',
        $installer->getTable('customer_group'), 
        'customer_group_id',
        'cascade', 
        'cascade'
	);

$table = $installer->getConnection()
    ->newTable($installer->getTable('sinch_pricerulesimport/pricerulesimport'))
    ->addColumn('pricerules_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'identity' => true,
        'nullable' => false,
        'primary'  => true,
    ), 'Entity id')
	->addColumn('price_from', Varien_Db_Ddl_Table::TYPE_DECIMAL, '10,2', array(
        'nullable' => true,
        'default'  => null,
    ), 'Price from')
	->addColumn('price_to', Varien_Db_Ddl_Table::TYPE_DECIMAL, '10,2', array(
        'nullable' => true,
        'default'  => null,
    ), 'Price to')
	->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => true,
        'default'  => null,
    ), 'Category id')
	->addColumn('brand_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => true,
        'default'  => null,
    ), 'Brand id')
	->addColumn('product_sku', Varien_Db_Ddl_Table::TYPE_TEXT, '255', array(
        'nullable' => true,
        'default'  => null,
    ), 'Product sku')
	->addColumn('customer_group_name', Varien_Db_Ddl_Table::TYPE_TEXT, '30', array(
        'nullable' => true,
        'default'  => null,
    ), 'Customer group name')
	->addColumn('markup_percentage', Varien_Db_Ddl_Table::TYPE_DECIMAL, '10,2', array(
        'nullable' => true,
        'default'  => null,
    ), 'Markup percentage')
	->addColumn('markup_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '10,2', array(
        'nullable' => true,
        'default'  => null,
    ), 'Markup price')
	->addColumn('absolute_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '10,2', array(
        'nullable' => true,
        'default'  => null,
    ), 'Absolute price')
	->addColumn('execution_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false
    ), 'Absolute price')
	->addColumn('magento_customer_group_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => true,
        'default'  => null,
    ), 'Magento customer group id')
	->addColumn('magento_category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => true,
        'default'  => null,
    ), 'Magento category id')
	->addColumn('magento_brand_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => true,
        'default'  => null,
    ), 'Magento brand id')
	->addColumn('magento_product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => true,
        'default'  => null,
    ), 'Magento product id')
    ->setComment('Price rules import');
	
$installer->getConnection()->createTable($table);
