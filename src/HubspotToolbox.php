<?php
/**
 * HubSpot Toolbox plugin for Craft CMS 3.x
 *
 * Turnkey HubSpot integration for CraftCMS
 *
 * @link      https://venveo.com
 * @copyright Copyright (c) 2018 Venveo
 */

namespace venveo\hubspottoolbox;

use Craft;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\services\Fields;
use craft\web\twig\variables\CraftVariable;
use craft\web\UrlManager;
use venveo\hubspottoolbox\fields\HubSpotFormField;
use venveo\hubspottoolbox\models\Settings;
use venveo\hubspottoolbox\services\HubspotService;
use venveo\hubspottoolbox\services\HubspotService as HubSpotServiceService;
use venveo\hubspottoolbox\twigextensions\HubspotToolboxTwigExtension;
use venveo\hubspottoolbox\variables\HubspotVariable;
use yii\base\Event;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://craftcms.com/docs/plugins/introduction
 *
 * @author    Venveo
 * @package   HubspotToolbox
 * @since     1.0.0
 *
 * @property  HubSpotServiceService $hubSpotService
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class HubspotToolbox extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * HubspotToolbox::$plugin
     *
     * @var HubspotToolbox
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    public $_hubspotService;
    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '1.0.1';

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * HubspotToolbox::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Add in our Twig extensions
        Craft::$app->view->registerTwigExtension(new HubspotToolboxTwigExtension());

        // Register our fields
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function(RegisterComponentTypesEvent $event) {
                $event->types[] = HubSpotFormField::class;
            }
        );

        // Register our CP routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function(RegisterUrlRulesEvent $event) {
                $event->rules['hubspot-toolbox'] = 'hubspot-toolbox/hubspot/dashboard';
                $event->rules['hubspot-toolbox/forms'] = 'hubspot-toolbox/hubspot/forms-index';

                $event->rules['hubspot-toolbox/oauth/<appHandle:[a-zA-Z0-9_\-]+>/login'] = ['route' => 'hubspot-toolbox/oauth/login'];
                $event->rules['hubspot-toolbox/oauth/<appHandle:[a-zA-Z0-9_\-]+>/callback'] = ['route' => 'hubspot-toolbox/oauth/callback'];
            }
        );

        // Register site routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['api/hub/submit/<formId:\d+>'] = ['route' => 'hubspot-toolbox/form/submit'];
            }
        );

        // Register our variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function(Event $event) {
                $variable = $event->sender;
                $variable->set('hubspot', HubspotVariable::class);
            }
        );
    }

    public function getHubSpotService($app = null)
    {
        if ($this->_hubspotService == null) {
            $this->_hubspotService = new HubspotService($app);
        }

        return $this->_hubspotService;
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * Returns the rendered settings HTML, which will be inserted into the content
     * block on the settings page.
     *
     * @return string The rendered settings HTML
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'hubspot-toolbox/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }


    public function getCpNavItem(): array
    {
        $ret = parent::getCpNavItem();
        $ret['label'] = Craft::t('hubspot-toolbox', 'HubSpot');

        $ret['subnav']['dashboard'] = [
            'label' => Craft::t('hubspot-toolbox', 'Dashboard'),
            'url' => 'hubspot-toolbox/dashboard'
        ];
        $ret['subnav']['forms'] = [
            'label' => Craft::t('hubspot-toolbox', 'Forms'),
            'url' => 'hubspot-toolbox/forms'
        ];

        return $ret;
    }
}
