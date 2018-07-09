<?php

namespace venveo\hubspottoolbox\controllers;

use craft\web\Controller;
use craftcms\oauth2;
use venveo\hubspottoolbox\HubSpotToolbox;
use venveo\hubspottoolbox\models\HubSpotApp;
use venveo\hubspottoolbox\records\TokenRecord;

/**
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @since     2.0.0
 */
class OauthController extends Controller
{
    public function actionCallback($appHandle)
    {
        /** @var HubSpotApp $app */
        $app = HubSpotToolbox::$plugin->getSettings()->getAppByHandle($appHandle);
        if (!$app) {
            $this->asErrorJson('App not found');
            return;
        }

        $code = \Craft::$app->request->getRequiredQueryParam('code');

        // Try to get an access token (using the authorization code grant)
        $token = $app->getProvider()->getAccessToken('authorization_code', [
            'code' => $code
        ]);

        // Delete old tokens
        TokenRecord::deleteAll(['appHandle' => $app->handle]);

        // Create new token
        $record = TokenRecord::hydrateFromAccessToken($token, $app);
        $record->save();
        $this->asJson(['message' => 'Success', 'resource' => $record->id]);
    }

    /**
     * Redirects to the login page for HubSpot
     *
     * @param $appHandle
     */
    public function actionLogin($appHandle)
    {
        /** @var HubSpotApp $app */
        $app = HubSpotToolbox::$plugin->getSettings()->getAppByHandle($appHandle);
        if (!$app) {
            $this->asErrorJson('App not found');
            return;
        }
        $provider = $app->getProvider();
        \Craft::$app->session->set('oauth2state', $provider->getState());

        $this->redirect($app->getConnectURL());
    }
}
