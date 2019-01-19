<?php

namespace venveo\hubspottoolbox\models;

use craft\base\Element;
use craft\base\ElementInterface;
use craft\helpers\Template;
use venveo\hubspottoolbox\records\HubSpotFormRecord;
use yii\base\Model;

/**
 * Class Link
 *
 *
 * @property \craft\models\Site $ownerSite
 */
class HubSpotForm extends Model
{
    /**
     * @var string
     */
    public $formId;

    /**
     * @var string
     */
    public $siteFormId;

    /**
     * @var string
     */
    public $formName;

    /**
     * @var string
     */
    public $portalId;

    /**
     * @var integer
     */
    public $siteId;

    /**
     * @var ElementInterface|null
     */
    private $owner;


    /**
     * Link constructor.
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    /**
     * Renders the HubSpot form on the site
     *
     * @return null|\Twig_Markup
     */
    public function render()
    {
        $formId = $this->formId;
        $portalId = $this->portalId;
        $domId = "form_${formId}_${portalId}";
        $text = <<<EOD
<!--[if lte IE 8]>
<script charset="utf-8" type="text/javascript" src="//js.hsforms.net/forms/v2-legacy.js"></script>
<![endif]-->
<script charset="utf-8" type="text/javascript" src="//js.hsforms.net/forms/v2.js"></script>
<script>
  hbspt.forms.create({
	portalId: "${portalId}",
	formId: "${formId}",
	target: "#${domId}",
	css: '',
	cssRequired: ''
});
</script>
<div id="${domId}"></div>
EOD;

        return Template::raw($text);
    }


    /**
     * @return \craft\models\Site
     */
    public function getOwnerSite()
    {
        if ($this->owner instanceof Element) {
            try {
                return $this->owner->getSite();
            } catch (\Exception $e) {
            }
        }

        return \Craft::$app->sites->currentSite;
    }

    public function submitted() {
        $service = \venveo\hubspottoolbox\HubSpotToolbox::$plugin->getHubSpotService();
        return $service->hasFilledOutForm($this);
    }


    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->formId === null;
    }

    /**
     * @return string
     */
    public function __toString()
    {
//        $url = $this->getUrl();
//        return is_null($url) ? '' : $url;
        return '';
    }

    public function id() {
        return $this->siteFormId;
    }

    public static function fromRecord(HubSpotFormRecord $record) {
        $instance = new self();
        $instance->siteFormId = $record->id;
        $instance->siteId = $record->siteId;
        $instance->formId = $record->formId;
        $instance->formName = $record->formName;
        return $instance;
    }

}
