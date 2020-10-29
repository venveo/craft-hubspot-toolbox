<?php
/*
 *  @link      https://www.venveo.com
 *  @copyright Copyright (c) 2020 Venveo
 */

namespace venveo\hubspottoolbox\controllers;

use craft\helpers\UrlHelper;
use craft\web\Controller;
use venveo\hubspottoolbox\HubSpotToolbox;

abstract class BaseHubSpotController extends Controller
{
    /**
     * @throws \yii\base\ExitException
     */
    public function requireValidHubSpotToken()
    {
        try {
            $hs = $this->getHubSpot();
        } catch (\Exception $e) {
            \Craft::$app->session->setNotice('Please verify your connection');
            $this->redirect(UrlHelper::cpUrl('hubspot-toolbox/connection'));
            \Craft::$app->end();
        }
    }

    /**
     * @param $featureHandle
     * @throws \yii\base\ExitException
     */
    public function requireValidSettingsForFeature($featureHandle)
    {
        /** @var $feature */
        $feature = HubSpotToolbox::$plugin->features->getFeatureByHandle($featureHandle);
        if (!$feature->validate()) {
            \Craft::$app->session->setNotice('This feature requires additional settings');
            $this->redirect($feature->getCpEditUrl());
            \Craft::$app->end();
        }
    }
}