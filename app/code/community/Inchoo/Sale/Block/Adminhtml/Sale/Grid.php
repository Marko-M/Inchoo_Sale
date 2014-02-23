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

class Inchoo_Sale_Block_Adminhtml_Sale_Grid
extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
    {
        parent::__construct();
        $this->setId('inchoo_sale_grid')
            ->setDefaultSort('store_group_id')
            ->setDefaultDir('ASC')
            ->setSaveParametersInSession(false)
            ->setUseAjax(false);

        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
    }

    protected function _prepareCollection()
    {
        /* Fetch sale category data collection and join store group and
         * category info
         */
        $collection = Mage::getModel('inchoo_sale/category')
            ->getCollection()
            ->joinExtendedData();

        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $helper = Mage::helper('inchoo_sale');

        $this->addColumn('store_group_id', array(
            'header' => $helper->__('Store Group Id'),
            'width' => '80px',
            'index'  => 'store_group_id'
        ));

        $this->addColumn('store_group_name', array(
            'header' => $helper->__('Store Group Name'),
            'type'  => 'text',
            'index'  => 'store_group_name'
         ));

        $this->addColumn('sale_category_id', array(
            'header' => $helper->__('Sale Category Id'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'sale_category_id',
            'renderer' =>
                'Inchoo_Sale_Block_Adminhtml_Sale_Renderer_Category_Id',
            'order_condition_callback' => array($this, '_saleCategoryOrderCallback')
        ));

        $this->addColumn('sale_category_name', array(
            'header' => $helper->__('Sale Category Name'),
            'type'  => 'text',
            'index' => 'sale_category_name',
            'renderer' =>
                'Inchoo_Sale_Block_Adminhtml_Sale_Renderer_Category_Name',
            'order_condition_callback' => array($this, '_saleCategoryOrderCallback')
        ));

        $this->addColumn('action',
            array(
                'header' => $helper->__('Action'),
                'width' => '80px',
                'type' => 'action',
                'getter' => 'getStoreGroupId',
                'actions' => array(
                    array(
                        'caption' => $helper->__('Configure'),
                        'url' => array(
                            'base'=>'*/*/configure'
                        ),
                        'field' => 'store_group_id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
        ));

         return parent::_prepareColumns();
    }

    /**
     * Order callback function
     *
     * @param Inchoo_Sale_Model_Resource_Category_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     */
    protected function _saleCategoryOrderCallback($collection, $column)
    {
        $collection->getSelect()
            ->order($column->getIndex() . ' ' . strtoupper($column->getDir()));
    }

    /**
     * Add support for order callback
     *
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return \Inchoo_Sale_Block_Adminhtml_Sale_Grid
     */
    protected function _setCollectionOrder($column)
    {
        if ($column->getOrderConditionCallback()) {
            call_user_func($column->getOrderConditionCallback(), $this->getCollection(), $column);

            return $this;
        }

        return parent::_setCollectionOrder($column);
    }

    /**
     * Set row URL
     *
     * @param Inchoo_Sale_Model_Category $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/*/configure',
            array('store_group_id' => $row->getStoreGroupId())
        );
    }

}