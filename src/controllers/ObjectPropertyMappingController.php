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
        $mapping = new HubSpotObjectMapping([
            'id' => $data['id'] ?? null,
            'property' => $data['property'],
            'template' => $data['template'],
            'type' => $data['type'],
            'datePublished' => null
        ]);
        HubSpotToolbox::$plugin->properties->saveMapping($mapping);
        if ($mapping->template) {
            $order = Order::find()->isCompleted(true)->orderBy('RAND()')->one();
            $renderedTemplate = \Craft::$app->view->renderObjectTemplate($mapping->template, [], ['order' => $order]);
            $mapping->setPreview($renderedTemplate);
        }
        return $this->asJson($mapping->toArray([], ['preview']));
    }

    public function actionPublishObjectMapping() {
        $this->requireAcceptsJson();
        $this->requirePostRequest();
        $objectType = \Craft::$app->request->getBodyParam('objectType');
        HubSpotToolbox::$plugin->properties->publishMappings($objectType);
        return $this->asJson(['success' => true]);
    }
}