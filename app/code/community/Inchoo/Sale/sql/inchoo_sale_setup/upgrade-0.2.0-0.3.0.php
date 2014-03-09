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

/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = Mage::getResourceModel('catalog/setup', 'inchoo_sale_setup');

$installer->startSetup();
if (!$installer->getAttributeId(Mage_Catalog_Model_Product::ENTITY, Inchoo_Sale_Model_Resource_Setup::SALE_CODE)) {
    $installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, Inchoo_Sale_Model_Resource_Setup::SALE_CODE, array(
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'group' => 'General',
        'label' => Mage::helper('inchoo_sale')->__('Sale'),
        'type' => 'int',
        'input' => 'select',
        'source' => 'inchoo_sale/eav_entity_attribute_source_boolean',
        'default' => Inchoo_Sale_Model_Eav_Entity_Attribute_Source_Boolean::VALUE_NO,
        'required' => false,
        'visible_on_front'=> false,
        'is_configurable' => false,
        'comparable' => false,
        'visible' => true,
        'user_defined' => true,
        'filterable' => true, // Include in layered nav
        'filterable_in_search' => true, // Include in layered nav
        'used_in_product_listing' => true, // Include in flat tables
        'position' => '1'
    ));
}

$installer->endSetup();