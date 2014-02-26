<?php
/**
 * Category Model
 *
 * @author Stock in the Channel
 */
class Sinch_Pricerules_Model_Category
{
	private $options = array();
	
	public function getName($id)
	{
		return Mage::getModel('catalog/category')->load($id)->getName();
	}
	
	private function nodeToArray(Varien_Data_Tree_Node $node) 
	{
		$result = array();
		$result ['category_id'] = $node->getId();
		$result ['parent_id'] = $node->getParentId();
		$result ['name'] = $node->getName();
		$result ['is_active'] = $node->getIsActive();
		$result ['position'] = $node->getPosition();
		$result ['level'] = $node->getLevel();
		$result ['children'] = array();
		
		foreach ($node->getChildren() as $child) 
		{
			$result['children'][] = $this->nodeToArray($child);
		}
		
		return $result;
	}
	
	private function loadTree() {
		
		$tree = Mage::getResourceSingleton('catalog/category_tree' )->load();
		
		$store = 1;
		$parentId = 1;
		
		$root = $tree->getNodeById($parentId);
		
		if ($root && $root->getId() == 1) 
		{
			$root->setName(Mage::helper('catalog')->__('Root'));
		}
		
		$collection = Mage::getModel('catalog/category')->getCollection()->setStoreId($store)->addAttributeToSelect('name')->addAttributeToSelect('category_id')->addAttributeToSelect('is_active');
		
		$tree->addCollectionData($collection, true);
		
		return $this->nodeToArray($root);
	}
	
	private function generateCategoryOptions($tree, $level = -1) 
	{
		$level ++;
		
		$this->options[0] = "-- Please Select --";
		
		foreach ($tree as $category) 
		{
			$this->options[$category['category_id']] = str_repeat('--', $level).$category['name'];
			$this->generateCategoryOptions($category['children'], $level);
		}
	}
	
	public function getOptionArray() 
	{
		if (!count($this->options))
		{
			$tree = $this->loadTree();
			$this->generateCategoryOptions($tree['children']);
		}
		
		return $this->options;
	}
}