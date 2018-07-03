<?php
/**
 * HubSpot Toolbox plugin for Craft CMS 3.x
 *
 * Turnkey HubSpot integration for CraftCMS
 *
 * @link      https://venveo.com
 * @copyright Copyright (c) 2018 Venveo
 */

namespace venveo\hubspottoolbox\services;

use Craft;
use craft\base\Component;
use SevenShores;
use venveo\hubspottoolbox\HubspotToolbox;
use venveo\hubspottoolbox\models\HubSpotApp;
use venveo\hubspottoolbox\records\HubSpotFormRecord;
use venveo\hubspottoolbox\records\TokenRecord;

/**
 * HubSpotService Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Venveo
 * @package   HubspotToolbox
 * @since     1.0.0
 *
 * @property mixed $allForms
 * @property null $contact
 * @property null $uTK
 */
class HubspotService extends Component
{
    private $portalId;
    private $app;
    private $utk;
    /** @var \venveo\hubspottoolbox\models\Settings */
    private $settings;
    private $hubspot;

    // Public Methods
    // =========================================================================

    /**
     * HubspotService constructor.
     *
     * @param null $app
     */
    public function __construct($app = null)
    {
        parent::__construct();
        $this->settings = HubspotToolbox::$plugin->getSettings();
        if (!$app instanceof HubSpotApp) {
            $app = $this->settings->getDefaultApp();
        }
        $this->app = $app;
        $this->portalId = $this->settings->hubspotPortalId;
        $this->hubspot = $this->app->getHubspotService();
        $this->utk = $this->getUTK();
    }

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     HubspotToolbox::$plugin->hubSpotService->isContact()
     *
     * @return mixed
     */
    public function isContact()
    {
        if (!$this->utk) {
            return false;
        }
        $contact = $this->hubspot->contacts()->getByToken($this->utk)->getData();
        return (bool)$contact->{'is-contact'};
    }

    public function getContact()
    {
        if (!$this->utk) {
            return null;
        }
        $contact = $this->hubspot->contacts()->getByToken($this->utk)->getData();
        $isContact = $contact->{'is-contact'};
        if (!$isContact) {
            return null;
        }
        return $contact;
    }


    /**
     * @param $form
     * @return bool
     */
    public function hasFilledOutForm($form)
    {
        $filled = json_decode(\Craft::$app->getSession()->get('hs_forms', \GuzzleHttp\json_encode([])), true);
        if (array_key_exists($form->formId, $filled)) {
            return true;
        }
        if (!$this->utk) {
            return false;
        }
        $contact = $this->hubspot->contacts()->getByToken($this->utk)->getData();

        if (!$contact->{'is-contact'}) {
            return false;
        }
        if (!$contact->{'form-submissions'} || !count($contact->{'form-submissions'})) {
            return false;
        }
        foreach ($contact->{'form-submissions'} as $submission) {
            if ($submission->{'form-id'} === $form->formId) {
                return true;
            }
        }
        return false;
    }

    public function getAllForms()
    {
        $this->app->getToken();
        $this->syncForms();
        $siteId = Craft::$app->getSites()->getCurrentSite()->id;
        $forms = HubSpotFormRecord::find([
            'siteId' => $siteId
        ])->all();
        return $forms;
    }

    private function syncForms()
    {
        $forms = $this->hubspot->forms()->all()->getData();
        $siteId = Craft::$app->getSites()->getCurrentSite()->id;
        $exists = [];
        foreach ($forms as $form) {
            if ($siteForm = HubSpotFormRecord::findOne(['formId' => $form->guid])) {
                $siteForm->formName = $form->name;
            } else {
                $siteForm = new HubSpotFormRecord();
                $siteForm->formId = $form->guid;
                $siteForm->siteId = $siteId;
                $siteForm->formName = $form->name;
            }
            $exists[] = $siteForm->formId;
            $siteForm->save();
        }

        $siteForms = HubSpotFormRecord::find()->all();
        foreach ($siteForms as $form) {
            if (!in_array($form->formId, $exists)) {
                $form->delete();
            }
        }
    }

    private function getUTK()
    {
        if (isset($_COOKIE['hubspotutk'])) {
            return $_COOKIE['hubspotutk'];
        }
        return null;
    }

    /**
     * @param HubSpotFormRecord $formRecord
     * @param $data
     * @param string|null $pageURL
     * @param string|null $pageName
     * @return SevenShores\Hubspot\Http\Response
     */
    public function submitForm(HubSpotFormRecord $formRecord, $data, $pageURL = null, $pageName = null)
    {
        if (!$pageURL) {
            $pageURL = \Craft::$app->request->getReferrer();
        }
        $hs_context = json_encode(array_filter([
            'hutk' => $this->getUTK(),
            'ipAddress' => \Craft::$app->request->getRemoteIP(),
            'pageUrl' => $pageURL,
            'pageName' => $pageName
        ]));
        $data['hs_context'] = $hs_context;
        unset($data['pageTitle'], $data['pageURL']);

        $filled = json_decode(\Craft::$app->getSession()->get('hs_forms', \GuzzleHttp\json_encode([])), true);
        $filled[$formRecord->formId] = true;
        \Craft::$app->getSession()->set('hs_forms', \GuzzleHttp\json_encode($filled));


        return $this->hubspot->forms()->submit($this->portalId, $formRecord->formId, $data);
    }


    /**
     * @return SevenShores\Hubspot\Factory
     */
    public function getHubspot(): \SevenShores\Hubspot\Factory
    {
        return $this->hubspot;
    }
}
