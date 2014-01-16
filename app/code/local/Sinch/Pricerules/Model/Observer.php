<?php
/**
 * Observer Methods
 *
 * @author Stock in the Channel
 */
define('Table_Sinch_PriceRulesImport', 'sinch_pricerulesimport');
define('Table_Sinch_PriceRules', 'sinch_pricerules');
define('Table_Customer_Group', 'customer_group');
define('CatIDPrefix', 'CategoryID_');

class Sinch_Pricerules_Model_Observer {
	public function getFinalPrice(Varien_Event_Observer $observer){
		$product = $observer->getProduct();
		$rulesTable = Mage::getSingleton('core/resource')->getTableName(Table_Sinch_PriceRules);
		$queryParams = array();
		$originalPrice = $product->getPrice();
		$queryParams["originalPrice"] = $originalPrice;
		$queryParams["productId"] = $product->getId();
		$queryParams["manufacturer"] = $product->getManufacturer();
		$queryParams["customerGroup"] = Mage::getSingleton('customer/session')->getCustomerGroupId();
		$catParamNames = array();
		foreach($product->getCategoryIds() as $index => $id){
			$catParamNames[] = ":" . CatIDPrefix . $index;
			$queryParams[CatIDPrefix . $index] = $id;
		}
		$dbRead = Mage::getSingleton('core/resource')->getConnection('core_read');
		
		$query = "SELECT markup_percentage, markup_price, absolute_price FROM " . $rulesTable . " WHERE
			( price_from <= :originalPrice AND price_to >= :originalPrice ) AND
			( category_id IS NULL OR 
			  category_id IN ( " . implode(", ", $catParamNames) . " ) ) AND
			( product_id IS NULL OR
			  product_id = :productId ) AND
			( brand_id IS NULL OR
			  brand_id = :manufacturer ) AND
			customer_group_id = :customerGroup
			ORDER BY execution_order ASC
			LIMIT 1
		";
		Mage::log($queryParams);
		
		$relevantRules = $dbRead->query($query, $queryParams);
		$rule = $relevantRules->fetch();
		if(!$rule) return $this;
		if($rule["markup_percentage"]){
			$newPrice = $originalPrice + ($originalPrice * ($rule["markup_percentage"] / 100));
		} elseif($rule["markup_price"]){
			$newPrice = $originalPrice + $rule["markup_price"];
		} elseif($rule["absolute_price"]){
			$newPrice = $rule["absolute_price"];
		} else {
			Mage::log("A Severe Pricerules Error Occured");
		}
		$product->setFinalPrice($newPrice);
		return $this;
	}
	
	public function ListCollectionPrice(Varien_Event_Observer $observer){
		$collection = $observer->getCollection();
		//Mage::log("Using ListCollectionPrice Observer function");
		foreach($collection as $product){
			Mage::dispatchEvent('catalog_product_get_final_price', array('product' => $product, 'qty' => 1));
		}
	}
	
	public function ImportPriceRules(Varien_Event_Observer $observer){
		$parse_file = $observer->getEvent()->getFile();
		$terminate_char = $observer->getEvent()->getTerminateChar();
		$importTable = Mage::getSingleton('core/resource')->getTableName(Table_Sinch_PriceRulesImport);
		$prCustGroupTable = Mage::getSingleton('core/resource')->getTableName(Table_Customer_Group);
		$rulesTable = Mage::getSingleton('core/resource')->getTableName(Table_Sinch_PriceRules);
		$dbWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        
		Mage::log("Start parse " . $parse_file);

		$dbWrite->query("TRUNCATE TABLE " . $importTable);
			
		$dbWrite->query("LOAD DATA LOCAL INFILE '" . $parse_file . "'
			INTO TABLE " . $importTable . "
			FIELDS TERMINATED BY '" . $terminate_char . "'
			OPTIONALLY ENCLOSED BY '\"'
			LINES TERMINATED BY \"\r\n\"
			IGNORE 1 LINES
			(pricerules_id, @price_from, @price_to, @category_id, @brand_id, @product_sku, @customer_group_name, @markup_percentage, @markup_price, @absolute_price, @execution_order)
			SET	price_from = NULLIF(@price_from, ''),
				price_to = NULLIF(@price_to, ''), 
				category_id = NULLIF(@category_id, ''),
				brand_id = NULLIF(@brand_id, ''),
				product_sku = NULLIF(@product_sku, ''),
				customer_group_name = @customer_group_name,
				markup_percentage = NULLIF(@markup_percentage, ''),
				markup_price = NULLIF(@markup_price, ''),
				absolute_price = NULLIF(@absolute_price, ''),
				execution_order = @execution_order
		");
		
		// delete customer groups
		$dbWrite->query("DELETE cg FROM ".$prCustGroupTable." AS cg
			WHERE cg.customer_group_id > 3
			AND NOT EXISTS (
				SELECT *
				FROM " . $importTable . " AS spri
				WHERE cg.customer_group_code = spri.customer_group_name
			)
		");
		
		// create customer groups
		$dbWrite->query("INSERT INTO " . $prCustGroupTable . "
			(
				customer_group_code,
				tax_class_id
			)
			SELECT DISTINCT
				customer_group_name,
				3
			FROM ".$importTable." AS spri
			WHERE NOT EXISTS (
				SELECT *
				FROM " . $prCustGroupTable . " AS cg
				WHERE cg.customer_group_code = spri.customer_group_name
			)
		");
		
		// update table with customer group IDs
		$dbWrite->query("UPDATE " . $importTable . " sipr
			INNER JOIN " . Mage::getSingleton('core/resource')->getTableName("customer_group") . " AS cg ON sipr.customer_group_name = cg.customer_group_code
			SET sipr.magento_customer_group_id = cg.customer_group_id
		");
		
		// update table with category IDs
		$dbWrite->query("UPDATE " . $importTable . " sipr
			INNER JOIN " . Mage::getSingleton('core/resource')->getTableName('catalog_category_entity') . " cce ON sipr.category_id = cce.store_category_id
			SET sipr.magento_category_id = cce.entity_id
		");
		
		// update table with brand IDs
		$dbWrite->query("UPDATE " . $importTable . " sipr
			INNER JOIN " . Mage::getSingleton('core/resource')->getTableName('stINch_manufacturers') . " sm ON sipr.brand_id = sm.sinch_manufacturer_id
			SET sipr.magento_brand_id = sm.shop_option_id
		");
			
		// update table with product IDs
		$dbWrite->query("UPDATE " . $importTable . " sipr
			INNER JOIN " . Mage::getSingleton('core/resource')->getTableName('stINch_products_mapping') . " spm ON sipr.product_sku = spm.product_sku
			SET sipr.magento_product_id = spm.entity_id
		");
		
		// delete rules with non matched categories
		$dbWrite->query("DELETE FROM " . $importTable . "
			WHERE category_id IS NOT NULL AND magento_category_id IS NULL
		"); 
		
		// delete rules with non matched brands
		$dbWrite->query("DELETE FROM " . $importTable . "
			WHERE brand_id IS NOT NULL AND magento_brand_id IS NULL
		"); 
		
		// delete rules with non matched skus
		$dbWrite->query("DELETE FROM " . $importTable . "
			WHERE product_sku IS NOT NULL AND magento_product_id IS NULL
		"); 
		
		// delete rules with non customer groups
		$dbWrite->query("DELETE FROM " . $importTable . "
			WHERE customer_group_name IS NOT NULL AND magento_customer_group_id IS NULL
		");
		
		// delete rules without any price rule set
		$dbWrite->query("DELETE FROM " . $importTable . "
			WHERE markup_percentage IS NULL AND markup_price IS NULL AND absolute_price IS NULL
		");
		
		Mage::log("Finish parse " . $parse_file);
		
		// delete non-existent rules
		$dbWrite->query("DELETE spr FROM " . $rulesTable . " as spr
			WHERE NOT EXISTS (
				SELECT *
				FROM " . $importTable . " AS spri
				WHERE spr.pricerules_id = spri.pricerules_id
				AND is_manually_added = 0
			)
		");
		
		// insert rules into sinch_pricerules from sinch_pricerulesimport
		$dbWrite->query("INSERT INTO " . $rulesTable . "
			(
				pricerules_id,
				price_from,
				price_to,
				category_id,
				brand_id,
				product_id,
				customer_group_id,
				markup_percentage,
				markup_price,
				absolute_price,
				execution_order,
				is_manually_added
            )
			(
				SELECT
					pricerules_id,
					price_from,
					price_to,
					magento_category_id,
					magento_brand_id,
					magento_product_id,
					magento_customer_group_id,
					markup_percentage,
					markup_price,
					absolute_price,
					execution_order,
					0
				FROM " . $importTable . " a
            )
            ON DUPLICATE KEY UPDATE
				price_from = a.price_from,
				price_to = a.price_to,
				category_id = a.magento_category_id,
				brand_id = a.magento_brand_id,
				product_id = a.magento_product_id,
				customer_group_id = a.magento_customer_group_id,
				markup_percentage = a.markup_percentage,
				markup_price = a.markup_price,
				absolute_price = a.absolute_price,
				execution_order = a.execution_order,
				is_manually_added = 0
		");
	}
}