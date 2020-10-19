<?php

namespace venveo\hubspottoolbox\controllers;

use craft\web\Controller;
use venveo\hubspottoolbox\entities\ecommerce\Store;
use venveo\hubspottoolbox\HubSpotToolbox;

class EcommerceController extends Controller
{

    public function actionSaveStore()
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();
        $storeId = \Craft::$app->request->getRequiredBodyParam('id');
        $storeLabel = \Craft::$app->request->getRequiredBodyParam('label');
        $storeUri = \Craft::$app->request->getBodyParam('adminUri');
        $store = new Store([
            'id' => $storeId,
            'label' => $storeLabel,
            'adminUri' => $storeUri
        ]);
        if (HubSpotToolbox::$plugin->ecomm->saveStore($store)) {
            return $this->asJson($store);
        } else {
            \Craft::$app->response->setStatusCode(400);
            return $this->asErrorJson($store->getFirstError('storeId'));
        }
    }
}