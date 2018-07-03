<?php

namespace venveo\hubspottoolbox\migrations;

use Craft;
use craft\db\Migration;

/**
 * FacebookConnector Install Migration
 *
 * @author    boscho87\itscoding
 * @package   FacebookConnector
 * @since     0.1.0
 */
class m180531_204226_add_access_tokens extends Migration
{

    /**
     * @var string The database driver to use
     */
    public $driver;

    /**
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            $this->createIndexes();
            $this->addForeignKeys();
            Craft::$app->db->schema->refresh();
        }

        return true;
    }

    /**
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();
        return true;
    }

    /**
     * Creates the tables needed for the Records used by the plugin
     * @return bool
     */
    protected function createTables()
    {
        $tablesCreated = false;
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%hubspottoolbox_accesstoken}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%hubspottoolbox_accesstoken}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'accessToken' => $this->string(300),
                    'refreshToken' => $this->string(300),
                    'expires' => $this->integer(),
                    'appHandle' => $this->string()->notNull(),
                    'createdBy' => $this->integer(),
                    'siteId' => $this->integer()->notNull()
                ]
            );
        }

        return $tablesCreated;
    }

    protected function createIndexes() {
        $this->createIndex(
            $this->db->getIndexName(
                '{{%hubspottoolbox_accesstoken}}',
                'appHandle',
                true
            ),
            '{{%hubspottoolbox_accesstoken}}',
            'appHandle',
            true
        );
    }

    protected function addForeignKeys()
    {
        // test_test table
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%hubspottoolbox_accesstoken}}', 'siteId'),
            '{{%hubspottoolbox_accesstoken}}',
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // test_test table
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%hubspottoolbox_accesstoken}}', 'createdBy'),
            '{{%hubspottoolbox_accesstoken}}',
            'createdBy',
            '{{%users}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * Removes the tables needed for the Records used by the plugin
     * @return void
     */
    protected function removeTables()
    {
        $this->dropTableIfExists('{{%hubspottoolbox_accesstoken}}');
    }
}
