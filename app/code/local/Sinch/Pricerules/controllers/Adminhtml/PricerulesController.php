<?php
/**
 * Price rules controller
 *
 * @author Stock in the Channel
 */
 
class Sinch_Pricerules_Adminhtml_PricerulesController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('pricerules/manage')
            ->_addBreadcrumb(
                  Mage::helper('sinch_pricerules')->__('Price Rules'),
                  Mage::helper('sinch_pricerules')->__('Price Rules')
              )
            ->_addBreadcrumb(
                  Mage::helper('sinch_pricerules')->__('Manage Price Rules'),
                  Mage::helper('sinch_pricerules')->__('Manage Price Rules')
              );
        
		return $this;
    }
	
	public function indexAction()
	{
		$this->_title($this->__('Price Rules'))
             ->_title($this->__('Manage Price Rules'));

        $this->_initAction();
        $this->renderLayout();
	}
	
	public function newAction()
	{
		$this->_title(Mage::helper('sinch_pricerules')->__('New Price Rule'));
		$breadCrumb = Mage::helper('sinch_pricerules')->__('New Price Rule');		
        $this->_initAction()->_addBreadcrumb($breadCrumb, $breadCrumb);
		
		$model = Mage::getModel('sinch_pricerules/pricerules');
		
		Mage::register('pricerules_item', $model);
		
		$this->renderLayout();
	}
	
	public function editAction()
    {
        $model = Mage::getModel('sinch_pricerules/pricerules');
        $priceRuleId = $this->getRequest()->getParam('id');
        
		if ($priceRuleId) 
		{
            $model->load($priceRuleId);

            if (!$model->getId()) 
			{
                $this->_getSession()->addError(
                    Mage::helper('sinch_pricerules')->__('Price rule does not exist.')
                );
				
                return $this->_redirect('*/*/');
            }

            $this->_title($model->getTitle());
            $breadCrumb = Mage::helper('sinch_pricerules')->__('Edit Price Rule');
			$this->_initAction()->_addBreadcrumb($breadCrumb, $breadCrumb);
        } 
		else 
		{
			return $this->_redirect('*/*/');
        }

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        
		if (!empty($data)) 
		{
            $model->addData($data);
        }

        Mage::register('pricerules_item', $model);

        $this->renderLayout();
    }
	
	public function saveAction()
    {
        $redirectPath = '*/*';
        $redirectParams = array();
        $data = $this->getRequest()->getPost();
		
        if ($data) 
		{
			//Below is a fix for "IS NULL" checks
            if(isset($data["category_id"]) && $data["category_id"] == 0){
                $data["category_id"] = null;
            }
			if(isset($data["brand_id"]) && $data["brand_id"] == 0){
				$data["brand_id"] = null;
			}

            $model = Mage::getModel('sinch_pricerules/pricerules');
            $priceRuleId = $this->getRequest()->getParam('pricerules_id');
            
			if ($priceRuleId) 
			{
                $model->load($priceRuleId);
            }
			
			$priceType = $data["price_types"];
			$priceValue = $data["price_type_value"];
			$productSku = $data["product_sku"];
		
            $hasError = false;
			
			if (!empty($productSku))
			{
				$productID = Mage::getModel('catalog/product')->getIdBySku($productSku);
				
				if ($productID)
				{
					$data["product_id"] = $productID;
				}
				else
				{
					$hasError = true;
					
					$this->_getSession()->addError(
						Mage::helper('sinch_pricerules')->__('An error occurred while saving the price rule. Product SKU not found!')
					);
				}
			}
			
			if (!$hasError)
			{
				switch ($priceType)
				{
					case 1: // markup percentage
						$data["markup_percentage"] = $priceValue;
						$data["markup_price"] = null;
						$data["absolute_price"] = null;
						break;
					case 2: // markup price
						$data["markup_percentage"] = null;
						$data["markup_price"] = $priceValue;
						$data["absolute_price"] = null;
						break;
					case 3: // absolute price
						$data["markup_percentage"] = null;
						$data["markup_price"] = null;
						$data["absolute_price"] = $priceValue;
						break;
					default:
						$hasError = true;
						$this->_getSession()->addError(Mage::helper('sinch_pricerules')->__('Invalid Price Type'));
						break;
				}
				
				$model->addData($data);

				try 
				{
					$model->save();

					$this->_getSession()->addSuccess(
						Mage::helper('sinch_pricerules')->__('The price rule has been saved.')
					);

					if ($this->getRequest()->getParam('back')) {
						$redirectPath = '*/*/edit';
						$redirectParams = array('id' => $model->getId());
					}
				} 
				catch (Mage_Core_Exception $e) 
				{
					$hasError = true;
					$this->_getSession()->addError($e->getMessage());
				} 
				catch (Exception $e) 
				{
					$hasError = true;
					
					$this->_getSession()->addException($e,
						Mage::helper('sinch_pricerules')->__('An error occurred while saving the price rule.')
					);
				}
			}

            if ($hasError) 
			{
                $this->_getSession()->setFormData($data);
				
				if ($priceRuleId)
				{
					$redirectPath = '*/*/edit';
					$redirectParams = array('id' => $this->getRequest()->getParam('id'));
				}
				else
				{
					$redirectPath = '*/*/new';
				}
            }
        }

        $this->_redirect($redirectPath, $redirectParams);
    }
	
	public function deleteAction()
    {
        $priceRuleId = $this->getRequest()->getParam('id');
		
        if ($priceRuleId) 
		{
            try 
			{
                $model = Mage::getModel('sinch_pricerules/pricerules');
                $model->load($priceRuleId);
				
                if (!$model->getId()) 
				{
                    Mage::throwException(Mage::helper('sinch_pricerules')->__('Unable to find price rule.'));
                }
				
                $model->delete();

                $this->_getSession()->addSuccess(
                    Mage::helper('sinch_pricerules')->__('The price rule has been deleted.')
                );
            } 
			catch (Mage_Core_Exception $e) 
			{
                $this->_getSession()->addError($e->getMessage());
            } 
			catch (Exception $e) 
			{
                $this->_getSession()->addException($e,
                    Mage::helper('sinch_pricerules')->__('An error occurred while deleting the price rule.')
                );
            }
        }

        $this->_redirect('*/*/');
    }
	
	protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) 
		{
            case 'new':
            case 'save':
                return Mage::getSingleton('admin/session')->isAllowed('pricerules/manage/save');
                break;
            case 'delete':
                return Mage::getSingleton('admin/session')->isAllowed('pricerules/manage/delete');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('pricerules/manage');
                break;
        }
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}