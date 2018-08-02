<?php

use yii\db\Migration;

/**
 * Handles the creation of table `orders`.
 */
class m180802_170848_create_orders_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('orders', [
            'id' => $this->primaryKey(),
            'user' => $this->string(300)->notNull()->append('COLLATE utf8mb4_estonian_ci'),
            'link' => $this->string(300)->notNull()->append('COLLATE utf8mb4_estonian_ci'),
            'quantity' => $this->integer()->notNull(),
            'service_id' => $this->integer()->notNull(),
            'status' => $this->tinyInteger(1)->notNull()->comment('0 - Pending, 1 - In progress, 2 - Completed, 3 - Canceled, 4 - Error'),
            'created_at' => $this->integer()->notNull(),
            'mode' => $this->tinyInteger(1)->notNull()->comment('0 - Manual, 1 - Auto')
        ], 'DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_estonian_ci');

        $this->addForeignKey(
            'fk-orders-service_id',
            'orders',
            'service_id',
            'services',
            'id',
            'NO ACTION'
        );

        $this->execute(file_get_contents('migrations/orders.sql'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('orders');
    }
}
