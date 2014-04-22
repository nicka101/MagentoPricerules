<?php
/**
 * Price rules group controller
 *
 * @author Stock in the Channel
 */
class Sinch_Pricerules_Adminhtml_Pricerules_GroupController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('sinch/pricerules/groups')
            ->_addBreadcrumb(
                Mage::helper('sinch_pricerules')->__('Price Rules'),
                Mage::helper('sinch_pricerules')->__('Price Rules')
            )
            ->_addBreadcrumb(
                Mage::helper('sinch_pricerules')->__('Manage Groups'),
                Mage::helper('sinch_pricerules')->__('Manage Groups')
            );

        return $this;
    }

    public function indexAction(){
        $this->_title($this->__('Price Rules'))
             ->_title($this->__('Manage Groups'));

        $this->_initAction();
        $this->renderLayout();
    }

    public function gridAction(){
        $this->loadLayout();
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_title(Mage::helper('sinch_pricerules')->__('New Price Group'));
        $breadCrumb = Mage::helper('sinch_pricerules')->__('New Price Group');
        $this->_initAction()->_addBreadcrumb($breadCrumb, $breadCrumb);

        $model = Mage::getModel('sinch_pricerules/group');

        Mage::register('pricerules_group_item', $model);

        $this->renderLayout();
    }

    public function editAction()
    {
        $model = Mage::getModel('sinch_pricerules/group');
        $priceRuleId = $this->getRequest()->getParam('id');

        if ($priceRuleId)
        {
            $model->load($priceRuleId);

            if (!$model->getId())
            {
                $this->_getSession()->addError(
                    Mage::helper('sinch_pricerules')->__('Price group does not exist.')
                );

                return $this->_redirect('*/*/');
            }

            $this->_title($model->getTitle());
            $breadCrumb = Mage::helper('sinch_pricerules')->__('Edit Price Group');
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

        Mage::register('pricerules_group_item', $model);

        $this->renderLayout();
    }

    public function saveAction()
    {
        $redirectPath = '*/*';
        $redirectParams = array();
        $data = $this->getRequest()->getPost();
        print_r($data);
        //exit(1);

        if ($data)
        {

            $model = Mage::getModel('sinch_pricerules/group');

            $hasError = false;

            if(!isset($data["group_id"]) && !isset($data["entity_id"])){
                $hasError = true;
                if(!isset($data["group_id"]))$this->_getSession()->addError("Group ID is not specified");
                else $this->_getSession()->addError("ID is not specified");
            }

            if(!isset($data["group_name"])){
                $hasError = true;
                $this->_getSession()->addError("Group Name is not specified");
            }

            if(!isset($data["execution_order"])){
                $hasError = true;
                $this->_getSession()->addError("Execution Order is not specified");
            }

            $groupId = $this->getRequest()->getParam('group_id');
            $entityId = $this->getRequest()->getParam('entity_id');

            if (!is_null($groupId))
            {
                $model->loadByGroupId($groupId);
                if($model->hasData()){ //Unique key violation if we try and add. bad practice to overwrite
                    $this->_getSession()->addError("A Group with that ID already exists! If you wish to edit the rule you need to do it from the edit screen");
                    $hasError = true;
                }
            } else if(!is_null($entityId)){ //This indicates the request came from the edit page
                $model->load($entityId);
                if(!$model->hasData()){ //Got an empty model back from the database, there was probably another user deleting this rule at the same time
                    $this->_getSession()->addError("Group not found! Perhaps another user deleted it while you were editing");
                    $hasError = true;
                }
            }

            if (!$hasError)
            {

                if(isset($data["group_id"]) && !is_null($model->getGroupId())){
                    $this->updateCustomerGroup($model->getGroupId(), $data["group_id"]);
                }

                $model->addData($data);

                try
                {
                    $model->save();

                    $this->_getSession()->addSuccess(
                        Mage::helper('sinch_pricerules')->__('The price group has been saved.')
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
                        Mage::helper('sinch_pricerules')->__('An error occurred while saving the price group.')
                    );
                }
            }

            if ($hasError)
            {
                $this->_getSession()->setFormData($data);

                if ($groupId)
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
                $model = Mage::getModel('sinch_pricerules/group');
                $model->load($priceRuleId);

                if (!$model->getId())
                {
                    Mage::throwException(Mage::helper('sinch_pricerules')->__('Unable to find price group.'));
                }

                if($model->getGroupId() == 0){
                    Mage::throwException(Mage::helper('sinch_pricerules')->__('You may not delete the default group!'));
                }

                $groupId = $model->getGroupId();
                $model->delete();
                $this->updateCustomerGroup($groupId);

                $this->_getSession()->addSuccess(
                    Mage::helper('sinch_pricerules')->__('The price group has been deleted.')
                );
            }
            catch (Mage_Core_Exception $e)
            {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e)
            {
                $this->_getSession()->addException($e,
                    Mage::helper('sinch_pricerules')->__('An error occurred while deleting the price group.')
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
                return Mage::getSingleton('admin/session')->isAllowed('pricerules/group_manage/save');
                break;
            case 'delete':
                return Mage::getSingleton('admin/session')->isAllowed('pricerules/group_manage/delete');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('pricerules/group_manage');
                break;
        }
    }

    private function updateCustomerGroup($groupId, $newGroupId = 0){
        $dbWrite = Mage::getSingleton('core/resource')->getConnection('core_write');

        $attrRes = $dbWrite->query("SELECT attribute_id FROM " . Mage::getSingleton('core/resource')->getTableName('eav/attribute') . " WHERE attribute_code = 'sinch_pricerules_group' LIMIT 1")->fetch();
        $attributeId = $attrRes["attribute_id"];

        $res = $dbWrite->query("SELECT value_id, value FROM " . Mage::getSingleton('core/resource')->getTableName('customer_entity_varchar') . " WHERE attribute_id = :attributeId AND FIND_IN_SET(:$groupId, value)", array(
            "attributeId" => $attributeId,
            "groupId"     => $groupId
        ));
        while(($row = $res->fetch()) != null){
            $groups = explode(",", $row["value"]);
            foreach($groups as $id => $group){
                if($group == $groupId){
                    if($newGroupId != 0){
                        $groups[$id] = $newGroupId;
                    } else {
                        if(count($groups) > 1){
                            unset($groups[$id]);
                        } else {
                            $groups = $newGroupId;
                        }
                    }
                }
            }
            $dbWrite->query("UPDATE " . Mage::getSingleton('core/resource')->getTableName('customer_entity_varchar') . " SET value = :updatedGroup WHERE value_id = :valueId", array(
                "updatedGroup" => implode(",", $groups),
                "valueId"      => $row["value_id"]
            ));
        }
    }
}