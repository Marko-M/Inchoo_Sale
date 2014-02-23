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

class Inchoo_Sale_Model_Resource_Category_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('inchoo_sale/category');
    }

    /**
     * Join store group and category info
     *
     * @return Inchoo_Sale_Model_Resource_Category_Collection
     */
    public function joinExtendedData()
    {
        // 9 - entity_type_id for category
        // name - attribute code for category name
        $categoryNameAttribute =
            Mage::getModel('eav/entity_attribute')
                ->loadByCode('catalog_category', 'name');

        $this->getSelect()
            ->reset(Zend_Db_Select::COLUMNS) // Address ambigous store_group_id
            ->columns(array(
                'id' => 'main_table.id',
                'sale_category_id' => 'main_table.sale_category_id')
            )
            // Join with category name attribute
            ->joinRight(
                array('ccev' => $this->getResource()->getValueTable('catalog/category', 'varchar')),
                    'ccev.entity_id = main_table.sale_category_id',
                array('sale_category_name' => 'value')
            )
            // Join with core_store_group table
            ->joinRight(
                array('scg' => $this->getResource()->getTable('core/store_group')),
                    'scg.group_id = main_table.store_group_id',
                array('store_group_id' => 'scg.group_id','store_group_name' => 'name')
            )
            ->where('scg.group_id > 0 AND' // Do not return default store group
                .' (ccev.attribute_id = ' . $categoryNameAttribute->getAttributeId()
                .' OR ccev.attribute_id IS NULL)'
            );

        return $this;
    }

}