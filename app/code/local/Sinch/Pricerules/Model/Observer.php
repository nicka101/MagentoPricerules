<?php
/**
 * Observer Methods
 *
 * @author Stock in the Channel
 */
define('CatIDPrefix', 'CategoryID_');

class Sinch_Pricerules_Model_Observer {
	public function getFinalPrice(Varien_Event_Observer $observer){
		$product = $observer->getProduct();
		$rulesTable = Mage::getSingleton('core/resource')->getTableName('sinch_pricerules/pricerules');
		$queryParams = array();
		$originalPrice = $product->getPrice();
		$queryParams["originalPrice"] = $originalPrice;
		$queryParams["productId"] = $product->getId();
		$queryParams["manufacturer"] = $product->getManufacturer();
		$custSession = Mage::getSingleton('customer/session');
		$queryParams["customerGroup"] = ($custSession->isLoggedIn() ? $custSession->getCustomer()->getSinchPricerulesGroup() : '0');
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
			FIND_IN_SET(group_id, :customerGroup) > 0
			ORDER BY execution_order ASC
			LIMIT 1
		";
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
				Mage::log("A Severe Pricerules Error Occurred");
				throw new Exception("Retrieved pricing rule not valid. Missing result action");
		}
		//Set all the Prices to prevent Magento revealing the original price in any of its "As low as *price*" blocks
        $product->setPrice($newPrice);
		$product->setMinPrice($newPrice);
		$product->setMinimalPrice($newPrice);
		$product->setMaxPrice($newPrice);
		$product->setTierPrice($newPrice);
		$product->setFinalPrice($newPrice);
		return $this;
	}
	
	public function ListCollectionPrice(Varien_Event_Observer $observer){
		$collection = $observer->getCollection();
		foreach($collection as $product){
			Mage::dispatchEvent('catalog_product_get_final_price', array('product' => $product, 'qty' => 1));
		}
	}

    public function ImportPriceRulesFtp(Varien_Event_Observer $observer){
        $host = $observer->getFtpHost();
        $username = $observer->getFtpUsername();
        $password = $observer->getFtpPassword();
        $pricerulesFilename = $observer->hasData('pricerules_file') ? $observer->getPricerulesFile() : "CustomerGroupRules.csv";
        $pricerulesGroupFilename = $observer->hasData('pricerules_group_file') ? $observer->getPricerulesGroupFile() : "CustomerGroups.csv";
        $pricerulesBrandFilename = $observer->hasData('pricerules_brand_file') ? $observer->getPricerulesBrandFile() : "Manufacturers.csv";
        $filePath = $observer->hasData('file_path') ? $observer->getFilePath() : "/";
        if(is_null($host) || is_null($username) || is_null($password) || is_null($pricerulesFilename) || is_null($pricerulesGroupFilename) || is_null($pricerulesBrandFilename) || is_null($filePath)){
            Mage::log("Incomplete Arguments Given to Sinch_Pricerules ImportPriceRulesFtp");
            return;
        }
        $conn = ftp_connect($host);
        if(!$conn){
            Mage::log("FTP Connect failed in Sinch_Pricerules to host: " . $host);
            return;
        }
        $loginSuccess = ftp_login($conn, $username, $password);
        if(!$loginSuccess){
            Mage::log("FTP Login Failed in Sinch_Pricerules for User: " . $username . " on Server: " . $host);
            ftp_close($conn);
            return;
        }
        if(!ftp_pasv($conn, true)){
            Mage::log("FTP PASV Failed in Sinch_Pricerules to host: " . $host);
            ftp_close($conn);
            return;
        }
        $files = ftp_nlist($conn, $filePath);
        if(!$files){
            Mage::log("Failed to List FTP Directory (" . $filePath . ") on Server: " . $host . " in Sinch_Pricerules");
            ftp_close($conn);
            return;
        }
        $pricerulesTempFolder = Mage::getBaseDir() . DIRECTORY_SEPARATOR . "var" . DIRECTORY_SEPARATOR . "pricerules_temp" . DIRECTORY_SEPARATOR;
        if(!file_exists($pricerulesTempFolder) || !is_dir($pricerulesTempFolder)){
            $mkSuccess = mkdir($pricerulesTempFolder, 755, true);
            if(!$mkSuccess){
                Mage::log("Sinch_Pricerules failed to create temp directory for CSV files at: " . $pricerulesTempFolder);
                ftp_close($conn);
                return;
            }
        }
        $preparedFiles = array();
        $dlSuccess = true;
        foreach($files as $file){
            $fileName = substr($file, strlen($filePath));
            if($fileName == $pricerulesFilename){
                $dlSuccess &= ftp_get($conn, $pricerulesTempFolder . $pricerulesFilename, $filePath . $pricerulesFilename, FTP_BINARY);
                if(!$dlSuccess)break;
                $preparedFiles['rule_file'] = $pricerulesTempFolder . $pricerulesFilename;
            } else if($fileName == $pricerulesBrandFilename){
                $dlSuccess &= ftp_get($conn, $pricerulesTempFolder . $pricerulesBrandFilename, $filePath . $pricerulesBrandFilename, FTP_BINARY);
                if(!$dlSuccess)break;
                $preparedFiles['brand_file'] = $pricerulesTempFolder . $pricerulesBrandFilename;
            } else if($fileName == $pricerulesGroupFilename){
                $dlSuccess &= ftp_get($conn, $pricerulesTempFolder . $pricerulesGroupFilename, $filePath . $pricerulesGroupFilename, FTP_BINARY);
                if(!$dlSuccess)break;
                $preparedFiles['group_file'] = $pricerulesTempFolder . $pricerulesGroupFilename;
            }
        }
        if(!$dlSuccess || !isset($preparedFiles['rule_file']) || !isset($preparedFiles['brand_file']) || !isset($preparedFiles['group_file'])){
            foreach($preparedFiles as $file){
                unlink($file);
            }
            rmdir($pricerulesTempFolder);
            Mage::log("Failed to download all required files for Sinch_Pricerules import");
            return;
        }
        $preparedData = $preparedFiles;
        $preparedData['seperator'] = '|';
        Mage::dispatchEvent('sinch_pricerules_import', $preparedData);
        foreach($preparedFiles as $file){
            unlink($file);
        }
        rmdir($pricerulesTempFolder);
    }
	
	public function ImportPriceRules(Varien_Event_Observer $observer){
		$ruleFile = $observer->getRuleFile();
        $groupFile = $observer->getGroupFile();
        $brandFile = $observer->getBrandFile();
		$terminate_char = $observer->getSeperator();
        if(is_null($ruleFile) || is_null($groupFile) || is_null($brandFile) || is_null($terminate_char)){
            Mage::log("ImportPriceRules missing Arguments!!");
            return;
        }
		$importTable = Mage::getSingleton('core/resource')->getTableName('sinch_pricerules/import');
		$prGroupTable = Mage::getSingleton('core/resource')->getTableName('sinch_pricerules/group');
        $prBrandTable = Mage::getSingleton('core/resource')->getTableName('sinch_pricerules/brand');
		$rulesTable = Mage::getSingleton('core/resource')->getTableName('sinch_pricerules/pricerules');
		$dbWrite = Mage::getSingleton('core/resource')->getConnection('core_write');

        //Clear Auto-imported Rules
        $dbWrite->query("DELETE FROM ". $prGroupTable . " WHERE is_manually_added = 0 AND group_id != 0");
        //Import the Updated Rules
        $dbWrite->query("LOAD DATA LOCAL INFILE '" . $groupFile . "'
            REPLACE
            INTO TABLE " . $prGroupTable . "
            FIELDS TERMINATED BY '" . $terminate_char . "'
            OPTIONALLY ENCLOSED BY '\"'
            LINES TERMINATED BY '\r\n'
            IGNORE 1 LINES
            (group_id, group_name)
        ");

		$dbWrite->query("TRUNCATE TABLE " . $importTable);
		$dbWrite->query("LOAD DATA LOCAL INFILE '" . $ruleFile . "'
			INTO TABLE " . $importTable . "
			FIELDS TERMINATED BY '" . $terminate_char . "'
			OPTIONALLY ENCLOSED BY '\"'
			LINES TERMINATED BY \"\r\n\"
			IGNORE 1 LINES
			(pricerules_id, @price_from, @price_to, @category_id, @brand_id, @product_sku, group_id, @markup_percentage, @markup_price, @absolute_price, @execution_order)
			SET	price_from = NULLIF(@price_from, ''),
				price_to = NULLIF(@price_to, ''), 
				category_id = NULLIF(@category_id, ''),
				brand_id = NULLIF(@brand_id, ''),
				product_sku = NULLIF(@product_sku, ''),
				markup_percentage = NULLIF(@markup_percentage, ''),
				markup_price = NULLIF(@markup_price, ''),
				absolute_price = NULLIF(@absolute_price, ''),
				execution_order = @execution_order
		");

		// update table with category IDs
		$dbWrite->query("UPDATE " . $importTable . " sipr
			INNER JOIN " . Mage::getSingleton('core/resource')->getTableName('catalog_category_entity') . " cce ON sipr.category_id = cce.store_category_id
			SET sipr.magento_category_id = cce.entity_id
		");

        //Import Brands from file
        $dbWrite->query("TRUNCATE TABLE " . $prBrandTable);
        $dbWrite->query("LOAD DATA LOCAL INFILE '" . $brandFile . "'
            INTO TABLE " . $prBrandTable . "
            FIELDS TERMINATED BY '" . $terminate_char . "'
            OPTIONALLY ENCLOSED BY '\"'
            LINES TERMINATED BY \"\r\n\"
            IGNORE 1 LINES
            (brand_id, brand_name, @thumb_url)");

		// update table with brand IDs
        $dbWrite->query("UPDATE " . $importTable . " sipr
            LEFT JOIN " . Mage::getSingleton('core/resource')->getTableName('sinch_pricerules/brand') . " sb ON sipr.brand_id = sb.brand_id
            LEFT JOIN " . Mage::getSingleton('core/resource')->getTableName('eav/attribute_option_value') . " eaov ON sb.brand_name = eaov.value
            SET sipr.magento_brand_id = eaov.option_id
            WHERE eaov.option_id IN ( SELECT option_id FROM " . Mage::getSingleton('core/resource')->getTableName('eav/attribute_option') . " WHERE attribute_id = :manufacturer )",
            array(
                'manufacturer' => Mage::getSingleton('catalog/product')->getResource()->getAttribute('manufacturer')->getAttributeId()
            )
        );

		// update table with product IDs
		$dbWrite->query("UPDATE " . $importTable . " sipr
			INNER JOIN " . Mage::getSingleton('core/resource')->getTableName('catalog/product') . " spm ON sipr.product_sku = spm.sku
			SET sipr.magento_product_id = spm.entity_id
		");

		//delete useless rules
		$dbWrite->query("DELETE FROM " . $importTable . "
			WHERE (category_id IS NOT NULL AND magento_category_id IS NULL)
			OR (brand_id IS NOT NULL AND magento_brand_id IS NULL)
			OR (product_sku IS NOT NULL AND magento_product_id IS NULL)
			OR (markup_percentage IS NULL AND markup_price IS NULL AND absolute_price IS NULL)
		");
		// insert rules into sinch_pricerules from sinch_pricerulesimport
		$dbWrite->query("INSERT INTO " . $rulesTable . "
			(
				price_from,
				price_to,
				category_id,
				brand_id,
				product_id,
				group_id,
				markup_percentage,
				markup_price,
				absolute_price,
				execution_order,
				is_manually_added
            )
			(
				SELECT
					price_from,
					price_to,
					magento_category_id,
					magento_brand_id,
					magento_product_id,
					group_id,
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
				group_id = a.group_id,
				markup_percentage = a.markup_percentage,
				markup_price = a.markup_price,
				absolute_price = a.absolute_price,
				execution_order = a.execution_order,
				is_manually_added = 0
		");
	}
}