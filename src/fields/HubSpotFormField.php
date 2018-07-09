<?php
/**
 * HubSpot Toolbox plugin for Craft CMS 3.x
 *
 * Turnkey HubSpot integration for CraftCMS
 *
 * @link      https://venveo.com
 * @copyright Copyright (c) 2018 Venveo
 */

namespace venveo\hubspottoolbox\fields;

use venveo\hubspottoolbox\HubspotToolbox;
use venveo\hubspottoolbox\assetbundles\hubspotformfield\HubSpotFormFieldAsset;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\helpers\Db;
use venveo\hubspottoolbox\models\HubSpotForm;
use venveo\hubspottoolbox\records\HubSpotFormRecord;
use yii\db\Schema;
use craft\helpers\Json;

/**
 * HubSpotForm Field
 *
 * Whenever someone creates a new field in Craft, they must specify what
 * type of field it is. The system comes with a handful of field types baked in,
 * and we’ve made it extremely easy for plugins to add new ones.
 *
 * https://craftcms.com/docs/plugins/field-types
 *
 * @author    Venveo
 * @package   HubspotToolbox
 * @since     1.0.0
 *
 * @property string $contentColumnType
 * @property mixed $settingsHtml
 */
class HubSpotFormField extends Field
{
    public $formId;
    public $formName;
    public $siteFormId;

    /**
     * Some attribute
     *
     * @var string
     */

    // Static Methods
    // =========================================================================

    /**
     * Returns the display name of this class.
     *
     * @return string The display name of this class.
     */
    public static function displayName(): string
    {
        return Craft::t('hubspot-toolbox', 'HubSpot Form');
    }

    // Public Methods
    // =========================================================================

    public function getContentColumnType(): string
    {
        return Schema::TYPE_STRING;
    }

    public function normalizeValue($value, ElementInterface $element = null)
    {
        if (is_string($value)) {
            $value = Json::decode($value);
        }
        $pluginSettings = HubspotToolbox::$plugin->getSettings();
        $form = HubSpotFormRecord::findOne($value['siteFormId']);
        if (!$form) {
            return null;
        }
        $hbPortalId = $pluginSettings['hubspotPortalId'];

        $attr = [];
        $attr['formId'] = $form->formId;
        $attr['siteFormId'] = $form->id;
        $attr['formName'] = $form->formName;
        $attr['siteId'] = $form->siteId;
        $attr['portalId'] = $hbPortalId;

        $form = new HubSpotForm($attr);
        return $form;
    }

    /**
     * Modifies an element query.
     *
     * This method will be called whenever elements are being searched for that may have this field assigned to them.
     *
     * If the method returns `false`, the query will be stopped before it ever gets a chance to execute.
     *
     * @param ElementQueryInterface $query The element query
     * @param mixed                 $value The value that was set on this field’s corresponding [[ElementCriteriaModel]] param,
     *                                     if any.
     *
     * @return null|false `false` in the event that the method is sure that no elements are going to be found.
     */
    public function serializeValue($value, ElementInterface $element = null)
    {
        return parent::serializeValue($value, $element);
    }

    public function getSettingsHtml()
    {
        // Render the settings template
        return Craft::$app->getView()->renderTemplate(
            'hubspot-toolbox/_components/fields/HubSpotForm_settings',
            [
                'field' => $this,
            ]
        );
    }

    public function getInputHtml($value, ElementInterface $element = null): string
    {
        $hubspotForms = [];
        $forms = HubSpotFormRecord::find()->all();
        foreach($forms as $form) {
            $hubspotForms[$form->id] = $form->formName;
        }
        $siteFormId = null;
        if (isset($value['siteFormId']))
        {
            $siteFormId = $value['siteFormId'];
        }
        // Render the input template
        return Craft::$app->getView()->renderTemplate(
            'hubspot-toolbox/_components/fields/HubSpotForm_input',
            [
                'id' => $this->id,
                'name' => $this->handle,
                'siteFormId' => $siteFormId,
                'hubspotForms' => $hubspotForms
            ]
        );
    }
}
