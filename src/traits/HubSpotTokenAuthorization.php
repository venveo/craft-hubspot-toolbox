<?php

namespace venveo\hubspottoolbox\traits;

use SevenShores\Hubspot\Factory;
use venveo\hubspottoolbox\HubSpotToolbox;

trait HubSpotTokenAuthorization
{
    private static $hs = null;

    /**
     * @return Factory
     */
    public function getHubSpot(): Factory
    {
        if (self::$hs) {
            return self::$hs;
        }

        $settings = HubSpotToolbox::$plugin->getSettings();
        $token = HubSpotToolbox::$plugin->oauth->getToken(\Craft::parseEnv($settings->appId));
        return self::$hs = Factory::createWithOAuth2Token($token->accessToken);
    }
}