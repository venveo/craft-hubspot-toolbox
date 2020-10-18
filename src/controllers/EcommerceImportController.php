<?php
namespace venveo\hubspottoolbox\controllers;

use craft\web\Controller;

class EcommerceImportController extends Controller {

    public function actionWebhookImport() {
        $this->requirePostRequest();
        /*
        {
  "portalId": 1234567,
  "storeId": "store-to-import-id",
  "importStartedAt": 1552678940201,
  "settingsToImport": [
    {
      "settingsId": 1,
      "objectType": "PRODUCT"
    },
    {
      "settingsId": 2,
      "objectType": "CONTACT"
    },
    {
      "settingsId": 3,
      "objectType": "LINE_ITEM"
    },
    {
      "settingsId": 4,
      "objectType": "DEAL"
    }
  ]
}
         */
    }
}