<?php

namespace venveo\hubspottoolbox\controllers;

use craft\web\Controller;

/**
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @since     2.0.0
 */
class HubspotController extends Controller
{
    // Public Methods
    // =========================================================================
    /**
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $this->redirect('hubspot/dashboard');
    }

    /**
     * @return mixed
     */
    public function actionDashboard()
    {
        $this->renderTemplate('hubspot-toolbox/dashboard');
    }

    public function actionFormsIndex()
    {
        return $this->renderTemplate('hubspot-toolbox/forms/_index');
    }

}
