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

class Inchoo_Sale_Block_Adminhtml_Sale_Configure 
extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        /*
         * Path to form is constructed as follows
         * {$this->_blockGroup . ‘/’ . $this->_controller . ‘_’ . $this->_mode . ‘_form’}
         */
        $this->_objectId = 'group_id';
        $this->_blockGroup = 'inchoo_sale';
        $this->_controller = 'adminhtml_sale';
        $this->_mode = 'configure';
        
        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('adminhtml')->__('Save'));
        $this->_removeButton('delete');
        $this->_removeButton('reset');
    }

    public function getHeaderText()
    {
        $helper = Mage::helper('inchoo_sale');
        if (Mage::registry('sale_configuration_data')->getStoreGroupName()) {
            return $helper->__(
                'Configure sale category for \'%s\'',
                $this->escapeHtml(Mage::registry('sale_configuration_data')
                    ->getStoreGroupName())            );
        }
        else {
            return $helper->__('Configure Sale Category');
        }
    }

}