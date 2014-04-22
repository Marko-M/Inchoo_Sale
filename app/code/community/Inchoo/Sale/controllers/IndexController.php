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

class Inchoo_Sale_IndexController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
        Mage::dispatchEvent(
            'catalog_controller_category_init_before',
            array(
                'controller_action' => $this
            )
        );
        
        $rootCategoryId = (int) Mage::app()->getStore()->getRootCategoryId();
        if (!$rootCategoryId) {
            $this->_forward('noRoute');
            return;
        }

        $rootCategory = Mage::getModel('catalog/category')
            ->load($rootCategoryId)
                
            // TODO: Fetch from config
            ->setName($this->__('Sale'))
            ->setMetaTitle($this->__('Sale'))
            ->setMetaDescription($this->__('Sale'))
            ->setMetaKeywords($this->__('Sale'));

        Mage::register('current_category', $rootCategory);
        
        Mage::getSingleton('catalog/session')
            ->setLastVisitedCategoryId($rootCategory->getId());

        try {
            Mage::dispatchEvent('catalog_controller_category_init_after',
                array(
                    'category' => $rootCategory,
                    'controller_action' => $this
                )
            );
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            return;
        }
        
        // Observer can change category
        if (!$rootCategory->getId()){
            $this->_forward('noRoute');
            return;
        }

        $this->loadLayout();
        
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('checkout/session');
        
        $this->renderLayout();
    }

}
