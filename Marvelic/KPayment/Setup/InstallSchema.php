<?php
/**
 * Created by PhpStorm.
 * User: nvtro
 * Date: 10/16/2018
 * Time: 3:45 PM
 */

namespace Marvelic\KPayment\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        /**
         * Prepare database for install
         */
        $installer->startSetup();

        try {
            // Required tables
            $statusTable = $installer->getTable('sales_order_status');
            $statusStateTable = $installer->getTable('sales_order_status_state');

            // Insert statuses
            $installer->getConnection()->insertArray(
                $statusTable,
                array('status','label'),
                array(array('status' => 'Pending_KBank', 'label' => 'Pending Kbank'))
            );

            // Insert states and mapping of statuses to states
            $installer->getConnection()->insertArray(
                $statusStateTable,
                array(
                    'status',
                    'state',
                    'is_default',
                    'visible_on_front'
                ),
                array(
                    array(
                        'status' => 'Pending_KBank',
                        'state' => 'Pending_KBank',
                        'is_default' => 0,
                        'visible_on_front' => 1
                    )
                )
            );
        } catch (Exception $e) {}

        /**
         * Create kbank_meta table.
         */
        if(!$installer->tableExists('kbank/meta')) {

            $table = $installer->getConnection()->newTable(
                $installer->getTable('kbank_meta')
            )->addColumn(
                'kbank_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )->addColumn(
                'order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['unsigned' => true, 'nullable' => true]
            )->addColumn(
                'user_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => true]
            )->addColumn(
                'trans_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                4,
                ['nullable' => true]
            )->addColumn(
                'merchant_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                15,
                ['nullable' => true]
            )->addColumn(
                'terminal_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                8,
                ['nullable' => true]
            )->addColumn(
                'shop_no',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                2,
                ['nullable' => true]
            )->addColumn(
                'currency_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                3,
                ['nullable' => true]
            )->addColumn(
                'invoice_no',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                12,
                ['nullable' => true]
            )->addColumn(
                'date',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                8,
                ['nullable' => true]
            )->addColumn(
                'time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                6,
                ['nullable' => true]
            )->addColumn(
                'card_no',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                19,
                ['nullable' => true]
            )->addColumn(
                'expired_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                4,
                ['nullable' => true]
            )->addColumn(
                'cvv2_cvc2',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                4,
                ['nullable' => true]
            )->addColumn(
                'trans_amount',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                12,
                ['nullable' => true]
            )->addColumn(
                'response_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                2,
                ['nullable' => true]
            )->addColumn(
                'approval_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                6,
                ['nullable' => true]
            )->addColumn(
                'card_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                12,
                ['nullable' => true]
            )->addColumn(
                'fx_rate',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['nullable' => true]
            )->addColumn(
                'thb_amount',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['nullable' => true]
            )->addColumn(
                'customer_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => true]
            )->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable' => true]
            )->addColumn(
                'player_ip_address',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                18,
                ['nullable' => true]
            )->addColumn(
                'warning_light',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                1,
                ['nullable' => true]
            )->addColumn(
                'selected_bank',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                60,
                ['nullable' => true]
            )->addColumn(
                'issuer_bank',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                60,
                ['nullable' => true]
            )->addColumn(
                'selected_country',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                45,
                ['nullable' => true]
            )->addColumn(
                'ip_country',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                45,
                ['nullable' => true]
            )->addColumn(
                'issuer_country',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                45,
                ['nullable' => true]
            )->addColumn(
                'eci',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                4,
                ['nullable' => true]
            )->addColumn(
                'xid',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                40,
                ['nullable' => true]
            )->addColumn(
                'cavv',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                40,
                ['nullable' => true]
            )->addIndex(
                $installer->getIdxName('kbank_meta', ['kbank_id']),
                ['kbank_id']
            );

            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}