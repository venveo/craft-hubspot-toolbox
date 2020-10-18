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
            '{{%hubspot_forms}}',
            [
                'id' => $this->primaryKey(),
                'hubId' => $this->string(255)->notNull(),
                'name' => $this->string(255)->notNull(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
            ]
        );

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
            'settings' => $this->text(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        $this->createIndex(null, '{{%hubspot_forms}}', ['hubId'], true);
    }

    public function safeDown()
    {
        $this->dropTableIfExists('{{%hubspot_forms}}');
        $this->dropTableIfExists('{{%hubspot_tokens}}');
        $this->dropTableIfExists('{{%hubspot_features}}');
    }
}
