<?php

namespace venveo\hubspottoolbox\controllers;

use craft\helpers\UrlHelper;
use craft\web\Controller;
use venveo\hubspottoolbox\features\HubSpotFeature;
use venveo\hubspottoolbox\HubSpotToolbox;

/**
 */
class FeaturesController extends Controller
{
    public function actionIndex($section = null)
    {
        $features = HubSpotToolbox::$plugin->features->getFeatures();
        /** @var HubSpotFeature $feature */
        $feature = null;

        if ($section) {
            $feature = HubSpotToolbox::$plugin->features->getFeatureByHandle($section);
        }
        if (!$feature) {
            $feature = $features[0];
            return $this->redirect(UrlHelper::cpUrl('hubspot-toolbox/features/' . $feature::getHandle()));
        }

        return $this->renderTemplate('hubspot-toolbox/_features/index', [
            'features' => $features,
            'feature' => $feature
        ]);
    }

    public function actionSave() {
        $this->requirePostRequest();
        $feature = HubSpotToolbox::$plugin->features->getFeatureByHandle(\Craft::$app->request->getRequiredBodyParam('feature'));
        $settings = \Craft::$app->request->getRequiredBodyParam('settings');
        \Craft::configure($feature, $settings);

        if (HubSpotToolbox::$plugin->features->saveFeature($feature)) {
            \Craft::$app->session->setNotice('Saved settings');
        }
        return null;
    }
}
