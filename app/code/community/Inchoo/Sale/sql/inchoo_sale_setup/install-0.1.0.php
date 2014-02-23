<?php

$installer = $this;

$installer->startSetup();

$connection = $installer->getConnection();

$table = $connection->newTable($installer->getTable('inchoo_sale/category'))
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
            'auto_increment' => true
            ), 'Sale category entry id')
        ->addColumn('store_group_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
            ), 'Store Group id')
        ->addColumn('sale_category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
            ), 'Sale category id')
        ->addIndex($installer->getIdxName('inchoo_sale/category', array('store_group_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
            array('store_group_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
        ->addIndex($installer->getIdxName('catalog/category', array('sale_category_id')),
            array('sale_category_id'))
        ->addForeignKey($installer->getFkName('inchoo_sale/category', 'store_group_id', 'core/store_group', 'group_id'),
            'store_group_id', $installer->getTable('core/store_group'), 'group_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addForeignKey($installer->getFkName('inchoo_sale/category', 'sale_category_id', 'catalog/category', 'entity_id'),
            'sale_category_id', $installer->getTable('catalog/category'), 'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment('Per store sale category for inchoo_sale/category entity');

$connection->createTable($table);

$installer->endSetup();