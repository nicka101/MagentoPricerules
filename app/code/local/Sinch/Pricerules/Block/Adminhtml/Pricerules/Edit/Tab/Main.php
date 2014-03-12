<?php
/**
 * Price Rules List admin edit form main tab
 *
 * @author Stock in the Channel
 */
class Sinch_Pricerules_Block_Adminhtml_Pricerules_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        $model = Mage::helper('sinch_pricerules')->getPriceRulesItemInstance();

        if (Mage::helper('sinch_pricerules/admin')->isActionAllowed('save')) 
		{
            $isElementDisabled = false;
        } 
		else 
		{
            $isElementDisabled = true;
        }

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('pricerules_main_');

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('sinch_pricerules')->__('Price Rule Details')
        ));
		
		$new = true;

        if ($model->getId()) 
		{
            $fieldset->addField('pricerules_id', 'hidden', array(
                'name' => 'pricerules_id'
            ));
			
			$new = false;
        }
		else
		{
			$model->is_manually_added = true;
		}
		
		$fieldset->addField('is_manually_added', 'hidden', array(
			'name' => 'is_manually_added'
		));
		
		if ($new)
		{
			$fieldset->addField('price_from', 'text', array(
				'name' => 'price_from',
				'label' => Mage::helper('sinch_pricerules')->__('Price From'),
				'title' => Mage::helper('sinch_pricerules')->__('Price From'),
				'required' => true
			));
		}
		else
		{
			$fieldset->addField('price_from', 'label', array(
				'name' => 'price_from',
				'label' => Mage::helper('sinch_pricerules')->__('Price From'),
				'title' => Mage::helper('sinch_pricerules')->__('Price From')
			));
		}
		
		if ($new)
		{
			$fieldset->addField('price_to', 'text', array(
				'name' => 'price_to',
				'label' => Mage::helper('sinch_pricerules')->__('Price To'),
				'title' => Mage::helper('sinch_pricerules')->__('Price To'),
				'required' => true
			));
		}
		else
		{
			$fieldset->addField('price_to', 'label', array(
				'name' => 'price_to',
				'label' => Mage::helper('sinch_pricerules')->__('Price To'),
				'title' => Mage::helper('sinch_pricerules')->__('Price To')
			));
		}
		
		if ($new)
		{
			$categories = Mage::getSingleton('sinch_pricerules/category')->getOptionArray();
			
			$fieldset->addField('category_id', 'select', array(
				'name' => 'category_id',
				'label' => 'Category',
				'title' => 'Category',
				'values' => $categories
			));
		}
		else
		{
			$fieldset->addField('category_name', 'label', array(
				'name' => 'category_name',
				'label' => 'Category',
				'title' => 'Category'
			));
			
			$model->category_name = Mage::getSingleton('sinch_pricerules/category')->getName($model->category_id);
		}
		
		if ($new)
		{
			$brands = Mage::getSingleton('sinch_pricerules/brand')->getOptionArray('manufacturer');
			
			$fieldset->addField('brand_id', 'select', array(
				'name' => 'brand_id',
				'label' => 'Brand',
				'title' => 'Brand',
				'values' => $brands
			));
		}
		else
		{
			$fieldset->addField('brand_name', 'label', array(
				'name' => 'brand_name',
				'label' => 'Brand',
				'title' => 'Brand'
			));
			
			$model->brand_name = Mage::getSingleton('sinch_pricerules/brand')->getName($model->brand_id);
		}
		
		if ($new)
		{
			$fieldset->addField('product_sku', 'text', array(
				'name' => 'product_sku',
				'label' => 'Product SKU',
				'title' => 'Product SKU'
			));
		}
		else
		{
			$fieldset->addField('product_sku', 'label', array(
				'name' => 'product_sku',
				'label' => 'Product SKU',
				'title' => 'Product SKU'
			));
			
			$model->product_sku = Mage::getSingleton('sinch_pricerules/product')->getSku($model->product_id);
		}
		
		if ($new)
		{
            $groups = Mage::getSingleton('sinch_pricerules/group')->getOptionArray();

			$fieldset->addField('group_id', 'select', array(
				'name' => 'customer_name',
				'label' => 'Price Group',
				'title' => 'Price Group',
                'values' => $groups
			));
		}
		else
		{
			$fieldset->addField('group_name', 'label', array(
				'name' => 'customer_name',
				'label' => 'Price Group',
				'title' => 'Price Group'
			));

            $model->group_name = Mage::getModel('sinch_pricerules/group')->loadByGroupId($model->group_id)->getName();
		}
		
		$fieldset->addField('price_types', 'radios', array(			
			'name' => 'price_types',
			'label' => 'Price Types',
			'values' => array(
				array('value' => '1', 'label' => 'Markup Percentage'),
                array('value' => '2', 'label' => 'Markup Price'),
                array('value' => '3', 'label' => 'Absolute Price')
            )
        ));
		
		$fieldset->addField('price_type_value', 'text', array(
			'name' => 'price_type_value',
            'label' => 'Price Type Value',
            'title' => 'Price Type Value',
			'required' => true
		));
		
		if ($new)
		{
			$model->price_types = 1;
		}
		else
		{
			if ($model->markup_percentage != null)
			{
				$model->price_types = 1;
				$model->price_type_value = $model->markup_percentage;
			}
			else if ($model->markup_price != null)
			{
				$model->price_types = 2;
				$model->price_type_value = $model->markup_price;
			}
			else if ($model->absolute_price != null)
			{
				$model->price_types = 3;
				$model->price_type_value = $model->absolute_price;
			}
		}
		
		$fieldset->addField('execution_order', 'text', array(
            'name' => 'execution_order',
            'label' => Mage::helper('sinch_pricerules')->__('Execution Order'),
            'title' => Mage::helper('sinch_pricerules')->__('Execution Order'),
			'required' => true
        ));

        Mage::dispatchEvent('adminhtml_pricerules_edit_tab_main_prepare_form', array('form' => $form));

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return Mage::helper('sinch_pricerules')->__('Price Rule Info');
    }

    public function getTabTitle()
    {
        return Mage::helper('sinch_pricerules')->__('Price Rule Info');
    }

    public function canShowTab()
    {
        return true;
    }
	
    public function isHidden()
    {
        return false;
    }
}
