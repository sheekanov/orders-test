<?php

use yii\db\Migration;

/**
 * Handles the creation of table `services`.
 */
class m180802_164938_create_services_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('services', [
            'id' => $this->primaryKey(),
            'name' => $this->string(300)->notNull()
        ]);

        $this->execute(file_get_contents('migrations/services.sql'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('services');
    }
}
