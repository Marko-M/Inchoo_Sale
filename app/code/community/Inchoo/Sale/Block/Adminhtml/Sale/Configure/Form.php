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

class Inchoo_Sale_Block_Adminhtml_Sale_Configure_Form
extends Mage_Adminhtml_Block_Widget_Form
{
    
    protected function _prepareForm()
    {
        // Get store group id
        $storeGroupId = $this->getRequest()
            ->getParam('store_group_id');

        // Fetch helper instance
        $helper = Mage::helper('inchoo_sale');

        /* If it exists, fetch extended sale category data added to registry by
         * Inchoo_Sale_Adminhtml_Inchoo_SaleController::configureAction()
         */
        if (Mage::registry('sale_configuration_data')) {
            $data = Mage::registry('sale_configuration_data')
                ->getData();
        } else {
            $data = array();
        }

        $form = new Varien_Data_Form(
            array(
                'id' => 'edit_form',
                'action' => $this->getUrl(  // Form action for save button
                    '*/*/save',
                    array('store_group_id' => $storeGroupId)
                ),
                'method' => 'post',
                'enctype' => 'multipart/form-data'
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset('configure_sale_form_fieldset', array());

        // Per store sale category dropdown
        $fieldset->addField('sale_category_id', 'select', array(
            'label'     => $helper->__('Pick a category'),
            'name'      => 'sale_category_id',
            'onclick' => '',
            'onchange' => '',
            'value'  => '1',

            // Fetch per store categories array with default entry
            'values' => $this->_getStoreGroupCategories($storeGroupId),

            'disabled' => false,
            'readonly' => false,
            'tabindex' => 1
        ));

        $form->setValues($data);

        return parent::_prepareForm();
    }

    protected function _getStoreGroupCategories($storeGroupId)
    {
        $helper = Mage::helper('inchoo_sale');

        $categoriesArray = array_merge(
            array(
                array(
                    'label' => $helper->__('Not configured'),
                    'value' => -1
                )
            ),
            // Fetch per store categories array
            $helper->getStoreGroupCategories($storeGroupId)
        );

        return $categoriesArray;
    }

}