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

class Inchoo_Sale_Model_Observer
{
    /**
     * Started by cron every hour
     */
    public function updateSale()
    {
        $saleCategories = Mage::getModel('inchoo_sale/category')
            ->getCollection();

        foreach ($saleCategories as $saleCategory) {
            $storeGroupId = $saleCategory->getStoreGroupId();

            $storeGroupDefaultStore = Mage::app()->getGroup($storeGroupId)
                ->getDefaultStore();

            $this->_updateSale(
                $storeGroupDefaultStore,
                $saleCategory->getSaleCategoryId()
            );
        }
    }

    /**
     * Update store's sale category
     *
     * @param Mage_Core_Model_Store $store
     * @param int $saleCategoryId
     */
    protected function _updateSale($store, $saleCategoryId)
    {
        $productIds = $this->_getSaleProductIds($store);

        $category = Mage::getModel('catalog/category')
            ->load($saleCategoryId);

        $category->setPostedProducts(array_flip($productIds));

        try {
            $category->save();
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Get product ids of sale products for a store
     *
     * @param Mage_Core_Model_Store $store
     * @return array
     */
    protected function _getSaleProductIds($store)
    {
        $rootCategory = Mage::getModel('catalog/category')
            ->load($store->getRootCategoryId());

        /*
         * We use raw SQL queries here instead of fetching collection with
         * $rootCategory->getProductCollection() or similar because in this case query
         * produced would depend on things like is flat catalog product/category
         * enabled or is the current store admin or not. This way code can be called
         * from wherever.
         */
        $coreResource = Mage::getSingleton('core/resource');

        $collection =
            Mage::getSingleton('core/resource')
            ->getConnection('core_read')
            ->select()
            ->from(
                array(
                    'e' => $coreResource->getTableName('catalog/product')
                )
            )
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns('e.entity_id')

            /* We need category id's to filter by root category. Reason to use
             * _index table is because catalog_category_product contains only
             * direct descendant product mappings for each category (e.g. no root).
             */
            ->join(
                array(
                    'catalog_category_product_index'=>
                        $coreResource->getTableName('catalog/category_product_index')
                ),
                'e.entity_id = catalog_category_product_index.product_id',
                null
            )

            /* We join to catalogrule_product_price to get items under catalog
             * price rules.
             */
            ->join(
                array(
                    'catalogrule_product_price'=>
                        $coreResource->getTableName('catalogrule/rule_product_price')
                ),
                '`e`.`entity_id` = `catalogrule_product_price`.`product_id`',
                null
            )
            ->where("`category_id`={$rootCategory->getId()}")
            ->group('e.entity_id');

        Mage::log($collection->assemble());

        return $coreResource->fetchCol($collection);
    }

}
