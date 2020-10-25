<?php

namespace venveo\hubspottoolbox\controllers;

use craft\web\Controller;
use venveo\hubspottoolbox\HubSpotToolbox;

class ObjectPropertyMappingController extends Controller
{

    public function actionGetObjectMappings()
    {
        $objectType = \Craft::$app->request->getRequiredQueryParam('objectType');
        $properties = HubSpotToolbox::$plugin->properties->getMappingData($objectType);
        return $this->asJson($properties);
    }
}