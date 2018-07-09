<?php

namespace venveo\hubspottoolbox\controllers;

use craft\web\Controller;
use venveo\hubspottoolbox\HubSpotToolbox;
use venveo\hubspottoolbox\records\HubSpotFormRecord;

/**
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @since     2.0.0
 */
class FormController extends Controller
{
    protected $allowAnonymous = ['submit'];
    public $enableCsrfValidation = false; // TODO: Remove this

    /**
     * @return mixed
     */
    public function actionSubmit($formId)
    {
        $this->requirePostRequest();

        $service = HubSpotToolbox::$plugin->getHubSpotService();
        /** @var HubSpotFormRecord $form */
        $form = HubSpotFormRecord::findOne(['id' => $formId]);
        if (!$form) {
            \Craft::$app->response->statusCode = 404;
            $this->asJson(['message' => 'Form not found']);
            return;
        }
        $data = \Craft::$app->request->getBodyParams();
        $pageURL = null;
        $pageTitle = null;
        if($data['pageURL']) {
            $pageURL = $data['pageURL'];
        }

        if($data['pageTitle']) {
            $pageTitle = $data['pageTitle'];
        }

        $submitResponse = $service->submitForm($form, $data, $pageURL, $pageTitle);
        $statusCode = $submitResponse->getStatusCode();

        /*
204 when the form submissions is successful
302 when the form submissions is successful and a redirectUrl is included or set in the form settings.
404 when the Form GUID is not found for the provided Portal ID
500 when an internal server error occurs
         */
        \Craft::$app->response->statusCode = $statusCode;
    }
}
