<?php
/**
 * HubSpot Toolbox plugin for Craft CMS 3.x
 *
 * Turnkey HubSpot integration for CraftCMS
 *
 * @link      https://venveo.com
 * @copyright Copyright (c) 2018 Venveo
 */

namespace venveo\hubspottoolbox\migrations;

use venveo\hubspottoolbox\HubspotToolbox;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

/**
 * HubSpot Toolbox Install Migration
 *
 * If your plugin needs to create any custom database tables when it gets installed,
 * create a migrations/ folder within your plugin folder, and save an Install.php file
 * within it using the following template:
 *
 * If you need to perform any additional actions on install/uninstall, override the
 * safeUp() and safeDown() methods.
 *
 * @author    Venveo
 * @package   HubspotToolbox
 * @since     1.0.0
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public $driver;

    // Public Methods
    // =========================================================================

    /**
     * This method contains the logic to be executed when applying this migration.
     * This method differs from [[up()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[up()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            $this->createIndexes();
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            $this->insertDefaultData();
        }

        return true;
    }

    /**
     * This method contains the logic to be executed when removing this migration.
     * This method differs from [[down()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[down()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates the tables needed for the Records used by the plugin
     *
     * @return bool
     */
    protected function createTables()
    {
        $tablesCreated = false;

        // hubspottoolbox_hubspotformrecord table
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%hubspottoolbox_hubspotformrecord}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%hubspottoolbox_hubspotformrecord}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    // Custom columns in the table
                    'siteId' => $this->integer()->notNull(),
                    'formId' => $this->string(255)->notNull(),
                    'formName' => $this->string(255)->notNull(),
                ]
            );
        }

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

    /**
     * Creates the indexes needed for the Records used by the plugin
     *
     * @return void
     */
    protected function createIndexes()
    {
        // hubspottoolbox_hubspotformrecord table
        $this->createIndex(
            $this->db->getIndexName(
                '{{%hubspottoolbox_hubspotformrecord}}',
                'formId',
                true
            ),
            '{{%hubspottoolbox_hubspotformrecord}}',
            'formId',
            true
        );
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
        // Additional commands depending on the db driver
        switch ($this->driver) {
            case DbConfig::DRIVER_MYSQL:
                break;
            case DbConfig::DRIVER_PGSQL:
                break;
        }
    }

    /**
     * Creates the foreign keys needed for the Records used by the plugin
     *
     * @return void
     */
    protected function addForeignKeys()
    {
        // hubspottoolbox_hubspotformrecord table
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%hubspottoolbox_hubspotformrecord}}', 'siteId'),
            '{{%hubspottoolbox_hubspotformrecord}}',
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

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
     * Populates the DB with the default data.
     *
     * @return void
     */
    protected function insertDefaultData()
    {
    }

    /**
     * Removes the tables needed for the Records used by the plugin
     *
     * @return void
     */
    protected function removeTables()
    {
        // hubspottoolbox_hubspotformrecord table
        $this->dropTableIfExists('{{%hubspottoolbox_hubspotformrecord}}');
        $this->dropTableIfExists('{{%hubspottoolbox_accesstoken}}');
    }
}
