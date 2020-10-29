<?php

namespace venveo\hubspottoolbox\controllers;

use craft\helpers\UrlHelper;
use craft\web\Controller;

/**
 */
class IndexController extends BaseHubSpotController
{
    public function actionIndex()
    {
        $this->requireValidHubSpotToken();
        return $this->redirect(UrlHelper::cpUrl('hubspot-toolbox/ecommerce'));
    }
}
