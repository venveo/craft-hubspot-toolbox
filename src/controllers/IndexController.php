<?php

namespace venveo\hubspottoolbox\controllers;

use craft\helpers\UrlHelper;
use craft\web\Controller;

/**
 */
class IndexController extends Controller
{

    public function actionIndex()
    {
        return $this->redirect(UrlHelper::cpUrl('hubspot-toolbox/ecommerce'));
    }
}
