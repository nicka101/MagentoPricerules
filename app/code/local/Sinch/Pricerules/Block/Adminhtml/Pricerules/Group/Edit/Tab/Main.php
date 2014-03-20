<?php
/**
 * Price Group List admin edit form main tab
 *
 * @author Stock in the Channel
 */
class Sinch_Pricerules_Block_Adminhtml_Pricerules_Group_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        $model = Mage::helper('sinch_pricerules')->getPriceRulesGroupItemInstance();

        if (Mage::helper('sinch_pricerules/admin')->isActionAllowed('group', 'save'))
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
            'legend' => Mage::helper('sinch_pricerules')->__('Price Group Details')
        ));

        $new = true;

        if ($model->getId())
        {
            $fieldset->addField('entity_id', 'hidden', array(
                'name' => 'entity_id'
            ));

            $new = false;
        }
        else
        {
            $model->is_manually_added = true;

            $fieldset->addField('is_manually_added', 'hidden', array(
                'name' => 'is_manually_added'
            ));
        }

        if ($new)
        {
            $fieldset->addField('group_id', 'text', array(
                'name' => 'group_id',
                'label' => Mage::helper('sinch_pricerules')->__('Group ID'),
                'title' => Mage::helper('sinch_pricerules')->__('Group ID'),
                'required' => true
            ));
        }
        else
        {
            $fieldset->addField('group_id', 'label', array(
                'name' => 'group_id',
                'label' => Mage::helper('sinch_pricerules')->__('Group ID'),
                'title' => Mage::helper('sinch_pricerules')->__('Group ID')
            ));
        }

        $fieldset->addField('group_name', 'text', array(
            'name' => 'group_name',
            'label' => Mage::helper('sinch_pricerules')->__('Group Name'),
            'title' => Mage::helper('sinch_pricerules')->__('Group Name'),
            'required' => true
        ));

        Mage::dispatchEvent('adminhtml_pricerules_group_edit_tab_main_prepare_form', array('form' => $form));

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return Mage::helper('sinch_pricerules')->__('Price Group Info');
    }

    public function getTabTitle()
    {
        return Mage::helper('sinch_pricerules')->__('Price Group Info');
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
