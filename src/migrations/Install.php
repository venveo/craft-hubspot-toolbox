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

use craft\db\Table;
use venveo\hubspottoolbox\HubSpotToolbox;

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
    public $driver;

    public function safeUp()
    {
        $this->createTable(
            '{{%hubspot_tokens}}',
            [
                'id' => $this->primaryKey(),
                'appId' => $this->integer()->notNull(),
                'hubId' => $this->integer()->notNull(),
                'accessToken' => $this->string(300),
                'refreshToken' => $this->string(300),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'dateExpires' => $this->dateTime(),
                'uid' => $this->uid(),
            ]
        );

        $this->createTable('{{%hubspot_features}}', [
            'id' => $this->primaryKey(),
            'type' => $this->string()->notNull(),
            'enabled' => $this->boolean()->notNull(),
            'settings' => $this->text(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);


        $this->createTable('{{%hubspot_object_mappers}}', [
            'id' => $this->primaryKey(),
            'type' => $this->string(128)->notNull(),
            'sourceTypeId' => $this->string(128)->null(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        $this->createTable('{{%hubspot_object_mappings}}', [
            'id' => $this->primaryKey(),
            'mapperId' => $this->integer()->notNull(),
            'property' => $this->string()->notNull(),
            'template' => $this->text(),
            'datePublished' => $this->dateTime(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        $this->createTable('{{%hubspot_element_map}}', [
            'id' => $this->primaryKey(),
            'elementId' => $this->integer()->notNull(),
            'elementSiteId' => $this->integer()->notNull(),
            'remoteObjectId' => $this->integer()->notNull(),
            'dateLastSynced' => $this->dateTime()->null(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        $this->createIndex(null, '{{%hubspot_element_map}}', ['remoteObjectId'], true);
        $this->createIndex(null, '{{%hubspot_element_map}}', ['elementId'], true);

        $this->addForeignKey(null, '{{%hubspot_object_mappings}}', ['mapperId'], '{{%hubspot_object_mappers}}', ['id']);
        $this->addForeignKey(null, '{{%hubspot_element_map}}', ['elementId'], Table::ELEMENTS, ['id']);
        $this->addForeignKey(null, '{{%hubspot_element_map}}', ['elementSiteId'], Table::SITES, ['id']);
    }

    public function safeDown()
    {
        $this->dropTableIfExists('{{%hubspot_element_map}}');
        $this->dropTableIfExists('{{%hubspot_object_mappings}}');
        $this->dropTableIfExists('{{%hubspot_object_mappers}}');
        $this->dropTableIfExists('{{%hubspot_tokens}}');
        $this->dropTableIfExists('{{%hubspot_features}}');
    }
}
