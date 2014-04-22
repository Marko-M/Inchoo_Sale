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

class Inchoo_Sale_Model_Catalog_Layer_Filter_Sale extends Mage_Catalog_Model_Layer_Filter_Abstract
{

    const FILTER_ON_SALE = 1;
    const FILTER_NOT_ON_SALE = 2;

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->_requestVar = 'sale';
    }

    /**
     * Apply sale filter to layer
     *
     * @param   Zend_Controller_Request_Abstract $request
     * @param   Mage_Core_Block_Abstract $filterBlock
     * @return  Mage_Catalog_Model_Layer_Filter_Sale
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $filter = (int) $request->getParam($this->getRequestVar());
        if (!$filter || Mage::registry('inchoo_sale_filter')) {
            return $this;
        }

        $select = $this->getLayer()->getProductCollection()->getSelect();
        /* @var $select Zend_Db_Select */

        if ($filter == self::FILTER_ON_SALE) {
            $select->where('price_index.final_price < price_index.price');
            $stateLabel = Mage::helper('inchoo_sale')->__('On Sale');
        } else {
            $select->where('price_index.final_price >= price_index.price');
            $stateLabel = Mage::helper('inchoo_sale')->__('Not On Sale');
        }

        $state = $this->_createItem(
            $stateLabel, $filter
        )->setVar($this->_requestVar);
        /* @var $state Mage_Catalog_Model_Layer_Filter_Item */

        $this->getLayer()->getState()->addFilter($state);

        Mage::register('inchoo_sale_filter', true);

        return $this;
    }

    /**
     * Get filter name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('inchoo_sale')->__('Sale');
    }

    /**
     * Get data array for building sale filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $data = array();
        $status = $this->_getCount();

        $data[] = array(
            'label' => Mage::helper('inchoo_sale')->__('On Sale'),
            'value' => self::FILTER_ON_SALE,
            'count' => $status['yes'],
        );

        $data[] = array(
            'label' => Mage::helper('inchoo_sale')->__('Not On Sale'),
            'value' => self::FILTER_NOT_ON_SALE,
            'count' => $status['no'],
        );
        return $data;
    }

    protected function _getCount()
    {
        // Clone the select
    	$select = clone $this->getLayer()->getProductCollection()->getSelect();
        /* @var $select Zend_Db_Select */

	$select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);
        $select->reset(Zend_Db_Select::WHERE);

        // Count the on sale and not on sale
        $sql = 'SELECT IF(final_price >= price, "no", "yes") as on_sale, COUNT(*) as count from ('
                .$select->__toString().') AS q GROUP BY on_sale';

        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        /* @var $connection Zend_Db_Adapter_Abstract */

        return $connection->fetchPairs($sql);
    }

}
