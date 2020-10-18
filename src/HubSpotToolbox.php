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
use venveo\hubspottoolbox\services\FeaturesService;
use venveo\hubspottoolbox\services\HubSpotEcommService;
use venveo\hubspottoolbox\services\HubSpotService;
use venveo\hubspottoolbox\services\OauthService;
use venveo\hubspottoolbox\twigextensions\HubspotToolboxTwigExtension;
use venveo\hubspottoolbox\variables\HubspotVariable;
use yii\base\Event;

/**
 * @author    Venveo
 * @package   HubspotToolbox
 * @since     1.0.0
 *
 * @property  HubSpotService $hubspot
 * @property  OauthService $oauth
 * @property  FeaturesService $features
 * @property  HubSpotEcommService $ecomm
 * @property-read array $cpNavItem
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class HubSpotToolbox extends Plugin
{
    public $hasCpSection = true;
    public $hasCpSettings = true;

    /**
     * @var HubSpotToolbox
     */
    public static $plugin;

    public $schemaVersion = '1.0.0';

    static $devServerManifestPath = 'https://craft3-plugindev.test:3000/dist';
    static $devServerPublicPath = 'https://craft3-plugindev.test:3000/dist/_assets';
    static $devServerEnabled = true;

    /**
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->setComponents([
            'hubspot' => HubSpotService::class,
            'ecomm' => HubSpotEcommService::class,
            'oauth' => OauthService::class,
            'features' => FeaturesService::class
        ]);

        // Add in our Twig extensions
        Craft::$app->view->registerTwigExtension(new HubspotToolboxTwigExtension());

        // Register our fields
        Event::on(Fields::class, Fields::EVENT_REGISTER_FIELD_TYPES, function (RegisterComponentTypesEvent $event) {
            $event->types[] = HubSpotFormField::class;
        }
        );

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['hubspot-toolbox'] = 'hubspot-toolbox/features/index';
                $event->rules['hubspot-toolbox/features'] = 'hubspot-toolbox/features/index';
                $event->rules['hubspot-toolbox/features/<section:{handle}>'] = 'hubspot-toolbox/features/index';
                $event->rules['hubspot-toolbox/connection'] = 'hubspot-toolbox/connection/index';
            }
        );

        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['api/hub/submit/<formId:\d+>'] = ['route' => 'hubspot-toolbox/form/submit'];
            }
        );

        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function (Event $event) {
            $variable = $event->sender;
            $variable->set('hubspot', HubspotVariable::class);
        }
        );
    }

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }


    public function getCpNavItem(): array
    {
        $ret = parent::getCpNavItem();
        $ret['label'] = Craft::t('hubspot-toolbox', 'HubSpot');

        $ret['subnav']['features'] = [
            'label' => Craft::t('hubspot-toolbox', 'Features'),
            'url' => 'hubspot-toolbox/features'
        ];

        $ret['subnav']['connection'] = [
            'label' => Craft::t('hubspot-toolbox', 'Connection'),
            'url' => 'hubspot-toolbox/connection'
        ];

        return $ret;
    }

    public function settingsHtml()
    {
        return Craft::$app->view->renderTemplate(
            'hubspot-toolbox/_settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }
}
