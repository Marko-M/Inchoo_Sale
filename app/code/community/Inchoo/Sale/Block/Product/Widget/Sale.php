<?php

class Inchoo_Sale_Block_Product_Widget_Sale
extends Mage_Catalog_Block_Product_Abstract
implements Mage_Widget_Block_Interface
{
   /**
     * Display products type
     */
    const DISPLAY_TYPE_SALE_PRODUCTS = 'sale_products';

    /**
     * Default value whether show pager or not
     */
    const DEFAULT_SHOW_PAGER = false;

    /**
     * Default value for products per page
     */
    const DEFAULT_PRODUCTS_PER_PAGE = 5;

    /**
     * Name of request parameter for page number value
     */
    const PAGE_VAR_NAME = 'np';

    /**
     * Default value for products count that will be shown
     */
    const DEFAULT_PRODUCTS_COUNT = 10;

    /**
     * Products count
     *
     * @var null
     */
    protected $_productsCount;

    /**
     * Instance of pager block
     *
     * @var Mage_Catalog_Block_Product_Widget_Html_Pager
     */
    protected $_pager;

    /**
     * Product collection initialize process
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection|Object|Varien_Data_Collection
     */
    protected function _getProductCollection()
    {
        switch ($this->getDisplayType()) {
            case self::DISPLAY_TYPE_SALE_PRODUCTS:
            default:
                $collection = $this->_getSaleProductCollection();
                break;
        }
        return $collection;
    }

    protected function _getSaleProductCollection()
    {
        /** @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection = Mage::getResourceModel('catalog/product_collection');
        
        $inchooSaleCategory = Mage::getModel('inchoo_sale/category')
            ->loadByStoreGroupId(Mage::app()->getGroup()->getId());
        
        if(!$inchooSaleCategory->getSaleCategoryId()) {
            return new Varien_Data_Collection();
        }
        
        $saleCategory = Mage::getModel('catalog/category')
            ->load($inchooSaleCategory->getSaleCategoryId());

        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
            ->addCategoryFilter($saleCategory)
            ->addAttributeToSort('created_at', 'desc')
            ->setPageSize($this->getProductsCount())
            ->setCurPage(1)
        ;

        return $collection;
    }

    /**
     * Get key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return array_merge(
            array(
                'CATALOG_PRODUCT_SALE',
                Mage::app()->getStore()->getId(),
                Mage::getDesign()->getPackageName(),
                Mage::getDesign()->getTheme('template'),
                Mage::getSingleton('customer/session')->getCustomerGroupId(),
                'template' => $this->getTemplate(),
                $this->getProductsCount()
            ),
            array(
                $this->getDisplayType(),
                $this->getProductsPerPage(),
                intval($this->getRequest()->getParam(self::PAGE_VAR_NAME))
            )
        );
    }

    /**
     * Retrieve display type for products
     *
     * @return string
     */
    public function getDisplayType()
    {
        if (!$this->hasData('display_type')) {
            $this->setData('display_type', self::DISPLAY_TYPE_ALL_PRODUCTS);
        }
        return $this->getData('display_type');
    }

    /**
     * Retrieve how much products should be displayed
     *
     * @return int
     */
    public function getProductsPerPage()
    {
        if (!$this->hasData('products_per_page')) {
            $this->setData('products_per_page', self::DEFAULT_PRODUCTS_PER_PAGE);
        }
        return $this->getData('products_per_page');
    }

    /**
     * Return flag whether pager need to be shown or not
     *
     * @return bool
     */
    public function showPager()
    {
        if (!$this->hasData('show_pager')) {
            $this->setData('show_pager', self::DEFAULT_SHOW_PAGER);
        }
        return (bool)$this->getData('show_pager');
    }

    /**
     * Render pagination HTML
     *
     * @return string
     */
    public function getPagerHtml()
    {
        if ($this->showPager()) {
            if (!$this->_pager) {
                $this->_pager = $this->getLayout()
                    ->createBlock(
                        'catalog/product_widget_html_pager',
                        'widget.new.product.list.pager'
                    );

                $this->_pager->setUseContainer(true)
                    ->setShowAmounts(true)
                    ->setShowPerPage(false)
                    ->setPageVarName(self::PAGE_VAR_NAME)
                    ->setLimit($this->getProductsPerPage())
                    ->setTotalLimit($this->getProductsCount())
                    ->setCollection($this->getProductCollection());
            }
            if ($this->_pager instanceof Mage_Core_Block_Abstract) {
                return $this->_pager->toHtml();
            }
        }
        
        return '';
    }

    /**
     * Initialize block's cache
     */
    protected function _construct()
    {
        parent::_construct();

        $this->addColumnCountLayoutDepend('empty', 6)
            ->addColumnCountLayoutDepend('one_column', 5)
            ->addColumnCountLayoutDepend('two_columns_left', 4)
            ->addColumnCountLayoutDepend('two_columns_right', 4)
            ->addColumnCountLayoutDepend('three_columns', 3);

        $this->addData(array('cache_lifetime' => 86400));
        $this->addCacheTag(Mage_Catalog_Model_Product::CACHE_TAG);
    }

    /**
     * Prepare collection with new products
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        $this->setProductCollection($this->_getProductCollection());
        return parent::_beforeToHtml();
    }

    /**
     * Set how much product should be displayed at once.
     *
     * @param $count
     * @return Mage_Catalog_Block_Product_New
     */
    public function setProductsCount($count)
    {
        $this->_productsCount = $count;
        return $this;
    }

    /**
     * Retrieve how much products should be displayed
     *
     * @return int
     */
    public function getProductsCount()
    {
        if (!$this->hasData('products_count')) {
            if (null === $this->_productsCount) {
                $this->_productsCount = self::DEFAULT_PRODUCTS_COUNT;
            }
            return $this->_productsCount;
        }
        return $this->getData('products_count');
    }

}