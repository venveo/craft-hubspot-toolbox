<?php
/**
 * HubSpot Toolbox plugin for Craft CMS 3.x
 *
 * Turnkey HubSpot integration for CraftCMS
 *
 * @link      https://venveo.com
 * @copyright Copyright (c) 2018 Venveo
 */

namespace venveo\hubspottoolbox\models;

use venveo\hubspottoolbox\HubspotToolbox;

use Craft;
use craft\base\Model;
use craft\validators\ArrayValidator;

/**
 * HubspotToolbox Settings Model
 *
 * This is a model used to define the plugin's settings.
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Venveo
 * @package   HubspotToolbox
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * Some field model attribute
     *
     * @var string
     */
    public $hubspotPortalId = '';

    public $apps;
    public $defaultApp;

    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();
    }
    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        return [
            ['hubspotPortalId', 'required'],
            ['apps', ArrayValidator::class],
        ];
    }

    public function getApps() {
        $apps = [];
        foreach($this->apps as $handle => $config) {
            $app = new HubSpotApp($config);
            $app->handle = $handle;
            $apps[] = $app;
        }

        return $apps;
    }

    /**
     * @param $handle
     * @return mixed
     */
    public function getAppByHandle($handle) {
        if (array_key_exists($handle, $this->apps)) {
            $app = new HubSpotApp($this->apps[$handle]);
            $app->handle = $handle;
            return $app;
        }

        return null;
    }

    public function getDefaultApp() {
        if (!$this->defaultApp) return null;
        return $this->getAppByHandle($this->defaultApp);
    }
}
