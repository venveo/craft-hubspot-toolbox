<?php

namespace venveo\hubspottoolbox\controllers;

use craft\commerce\elements\Order;
use craft\web\Controller;
use venveo\hubspottoolbox\HubSpotToolbox;
use venveo\hubspottoolbox\models\HubSpotObjectMapping;

class ObjectPropertyMappingController extends Controller
{

    public function actionGetObjectMappings()
    {
        $objectType = \Craft::$app->request->getRequiredQueryParam('objectType');
        $properties = HubSpotToolbox::$plugin->properties->getMappingData($objectType);
        return $this->asJson($properties);
    }

    public function actionSaveObjectMapping() {
        $this->requireAcceptsJson();
        $this->requirePostRequest();
        $data = \Craft::$app->request->getBodyParam('mapping');
        $mapping = new HubSpotObjectMapping($data);
        HubSpotToolbox::$plugin->properties->saveMapping($mapping);
        return $this->asJson(['success']);
    }

    public function actionGetMappingPreview() {
        $objectType = \Craft::$app->request->getRequiredQueryParam('objectType');
        $mappings = HubSpotToolbox::$plugin->properties->getObjectMappings($objectType);
        /** @var Order $order */
        $order = Order::find()->isCompleted(true)->orderBy('RAND()')->one();
        /** @var HubSpotObjectMapping $mapping */
        foreach($mappings as $mapping) {
            if ($mapping->template) {
                $renderedTemplate = \Craft::$app->view->renderObjectTemplate($mapping->template, [], ['order' => $order]);
                $mapping->preview = $renderedTemplate;
            }
        }
        return $this->asJson($mappings);
    }
}