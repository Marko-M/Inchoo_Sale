<?php

/**
* Inchoo
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@magentocommerce.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Please do not edit or add to this file if you wish to upgrade
* Magento or this extension to newer versions in the future.
** Inchoo *give their best to conform to
* "non-obtrusive, best Magento practices" style of coding.
* However,* Inchoo *guarantee functional accuracy of
* specific extension behavior. Additionally we take no responsibility
* for any possible issue(s) resulting from extension usage.
* We reserve the full right not to provide any kind of support for our free extensions.
* Thank you for your understanding.
*
* @category Inchoo
* @package Sale
* @author Marko Martinović <marko.martinovic@inchoo.net>
* @copyright Copyright (c) Inchoo (http://inchoo.net/)
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
*/

class Inchoo_Sale_Adminhtml_Inchoo_SaleController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init layout for most actions
     */
    protected function _initLayout()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog/inchoo_sale')
            ->_title($this->__('Catalog'));

        return $this;
    }

    /**
     * Sale categories grid action
     */
    public function indexAction()
    {
        $this->_initLayout()
            ->_title($this->__('Manage Sale Categories'))
            ->renderLayout();
    }

    /**
     * Configure sale category action
     */
    public function configureAction()
    {
        // Check store group id
        $storeGroupId = $this->getRequest()->getParam('store_group_id');
        try {
            // Will throw Mage_Core_Exception if store group doesn't exist
            $storeGroup = Mage::app()->getGroup($storeGroupId);

            // Load sale category with extended info
            $data = Mage::getModel('inchoo_sale/category')
                ->loadByStoreGroupId($storeGroupId);

            // Place sale category info in registry to be used by form
            Mage::register('sale_configuration_data', $data);

            $this->_initLayout()
                ->_title($this->__('Manage Sale Categories'))
                ->_title($this->__('Configure'))
                ->renderLayout();
        } catch (Exception $e) {
            // Handle error conditions
            Mage::logException($e);
            Mage::getSingleton('core/session')
                ->addError($this->__($e->getMessage()));
            $this->_redirect('*/*/');
        }
    }

    /**
     * Save sale category mapping action
     */
    public function saveAction()
    {
        $storeGroupId = $this->getRequest()
            ->getParam('store_group_id');

        try {
            // Will throw Mage_Core_Exception if store group doesn't exist
            $storeGroup = Mage::app()->getGroup($storeGroupId);

            $saleCategoryId = $this->getRequest()
                ->getParam('sale_category_id');

            if(!$saleCategoryId || !is_numeric($saleCategoryId)) {
                Mage::throwException('Sale category id parameter missing.');
            }

            /*
             * Load sale category by store group id with additional
             * store and category data
             */
            $saleCategory = Mage::getModel('inchoo_sale/category')
                ->loadByStoreGroupId($storeGroupId);

            if($saleCategoryId == -1 && $saleCategory->getId()) {
                // If category mapping exists and we are about to remove it
                $saleCategory->delete();
            } else if ($saleCategoryId != -1) {
                $data = array(
                    'store_group_id' => $storeGroupId,
                    'sale_category_id' => $saleCategoryId
                );

                if(!$saleCategory->getId()) {
                    // If category mapping doesn't exist and we're about to add it
                    $saleCategory->setData($data);
                } else if($saleCategory->getId()) {
                    // If category mapping exists and we're about to change it
                    $saleCategory->addData($data);
                }

                $saleCategory->save();
            }

            Mage::getSingleton('core/session')
                ->addSuccess($this->__('Sale category configuration successfully updated.'));
        } catch (Exception $e) {
            // Handle error conditions
            Mage::logException($e);
            Mage::getSingleton('core/session')
                ->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }

    public function updateAction()
    {
        try {
            Mage::getModel('inchoo_sale/observer')
                ->updateSale();

            Mage::getSingleton('core/session')
                ->addSuccess($this->__('Sale categories successfully updated.'));
        } catch (Exception $e) {
            // Handle error conditions
            Mage::logException($e);
            Mage::getSingleton('core/session')
                ->addError($e->getMessage());
        }

        $this->_redirect('*/*/');
    }

}