<?php
/*
 *  @link      https://www.venveo.com
 *  @copyright Copyright (c) 2020 Venveo
 */

namespace venveo\hubspottoolbox\controllers;

use craft\web\Controller;
use venveo\hubspottoolbox\HubSpotToolbox;
use venveo\hubspottoolbox\models\HubSpotObjectMapping;
use venveo\hubspottoolbox\propertymappers\PreviewablePropertyMapperInterface;
use venveo\hubspottoolbox\propertymappers\PropertyMapperInterface;
use yii\web\HttpException;

class ObjectPropertyMappingController extends Controller
{
    public function actionGetObjectMappings()
    {
        $mapper = $this->getMapperFromRequest();
        $attributes = ['properties', 'propertyMappings', 'propertyMappings.renderedValue', 'sourceId'];
        if ($mapper instanceof PreviewablePropertyMapperInterface) {
            $attributes[] = 'initialPreviewObjectId';
            $mapper->setSourceId($mapper->getInitialPreviewObjectId());
            $mapper->renderTemplates();
        }
        $data = $mapper->toArray([], $attributes);
        return $this->asJson($data);
    }

    public function actionSaveObjectMapping()
    {
        $this->requireAcceptsJson();
        $this->requirePostRequest();
        $data = \Craft::$app->request->getRequiredBodyParam('mapping');
        $mapper = $this->getMapperFromRequest();
        $mapping = new HubSpotObjectMapping([
            'id' => $data['id'] ?? null,
            'property' => $data['property'],
            'template' => $data['template'] ?? '',
            'mapperId' => $mapper->id,
            'datePublished' => null
        ]);
        HubSpotToolbox::$plugin->properties->saveMapping($mapping);
        $mapper->renderProperty($mapping);
        return $this->asJson($mapping->toArray([], ['renderedValue']));
    }

    public function actionPublishObjectMapping()
    {
        $this->requireAcceptsJson();
        $this->requirePostRequest();
        $mapper = $this->getMapperFromRequest();
        HubSpotToolbox::$plugin->properties->publishMappings($mapper);
        return $this->asJson(['success' => true]);
    }

    protected function getMapperFromRequest(): PropertyMapperInterface
    {
        $mapperType = \Craft::$app->request->getRequiredParam('mapper');
        $sourceTypeId = \Craft::$app->request->getParam('sourceTypeId');
        $previewId = \Craft::$app->request->getParam('previewObjectId');

        if (class_exists($mapperType) && in_array(PropertyMapperInterface::class, class_implements($mapperType), true)) {
            $mapper = HubSpotToolbox::$plugin->properties->getMapper($mapperType, $sourceTypeId);
            if($previewId && $mapper instanceof PreviewablePropertyMapperInterface) {
                if (!$previewId) {
                    $previewId = $mapper->getInitialPreviewObjectId();
                }
                $mapper->setSourceId($previewId);
            }
            return $mapper;
        }

        throw new HttpException('Invalid mapper');
    }
}