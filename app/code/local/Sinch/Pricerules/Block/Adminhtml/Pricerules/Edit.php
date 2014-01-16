<?php
/**
 * Price Rules List admin edit form container
 *
 * @author Stock in the Channel
 */
 
class Sinch_Pricerules_Block_Adminhtml_Pricerules_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId= 'id';
        $this->_blockGroup = 'sinch_pricerules';
        $this->_controller = 'adminhtml_pricerules';

        parent::__construct();

        if (Mage::helper('sinch_pricerules/admin')->isActionAllowed('save')) 
		{
            $this->_updateButton('save', 'label', Mage::helper('sinch_pricerules')->__('Save Price Rule'));
            $this->_addButton('saveandcontinue', array(
                'label'   => Mage::helper('adminhtml')->__('Save and Continue Edit'),
                'onclick' => 'saveAndContinueEdit()',
                'class'   => 'save',
            ), -100);
        } 
		else 
		{
            $this->_removeButton('save');
        }

        if (Mage::helper('sinch_pricerules/admin')->isActionAllowed('delete')) 
		{
            $this->_updateButton('delete', 'label', Mage::helper('sinch_pricerules')->__('Delete Price Rule'));
        } 
		else 
		{
            $this->_removeButton('delete');
        }

        $this->_formScripts[] = "
            function toggleEditor() 
			{
                if (tinyMCE.getInstanceById('page_content') == null) 
				{
                    tinyMCE.execCommand('mceAddControl', false, 'page_content');
                } 
				else 
				{
                    tinyMCE.execCommand('mceRemoveControl', false, 'page_content');
                }
            }

            function saveAndContinueEdit()
			{
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        $model = Mage::helper('sinch_pricerules')->getPriceRulesItemInstance();
        
		if ($model->getId()) 
		{
            return Mage::helper('sinch_pricerules')->__("Edit Price Rule '%s'",
                 $this->escapeHtml($model->getId()));
        } 
		else 
		{
            return Mage::helper('sinch_pricerules')->__('New Price Rule');
        }
    }
}