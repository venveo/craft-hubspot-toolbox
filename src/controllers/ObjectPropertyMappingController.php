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
        $context = \Craft::$app->request->getRequiredQueryParam('context');
        $properties = HubSpotToolbox::$plugin->properties->getMappingData($objectType, $context);
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
            'context' => $data['context'],
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
        $objectType = \Craft::$app->request->getRequiredBodyParam('objectType');
        $context = \Craft::$app->request->getRequiredBodyParam('context');
        HubSpotToolbox::$plugin->properties->publishMappings($objectType, $context);
        return $this->asJson(['success' => true]);
    }
}