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
    private static $requestMapper = null;

    public function actionGetObjectMappings()
    {
        $mapper = $this->getMapperFromRequest();
        $attributes = ['properties', 'propertyMappings'];
        $data = $mapper->toArray([], $attributes);
        $data['previewData'] = [];
        if ($mapper instanceof PreviewablePropertyMapperInterface) {
            $previewId = $this->getPreviewSourceFromRequest();
            $preview = $mapper->renderTemplates($previewId);
            $data['previewData']['preview'] = $preview;
            $data['previewData']['previewObjectId'] = $previewId;
        }
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
        $mappingData = $mapping->toArray();
        if ($mapper instanceof PreviewablePropertyMapperInterface) {
            $previewId = $this->getPreviewSourceFromRequest();
            $preview = $mapper->renderProperty($mapping, $previewId);
            $mappingData['previewData']['preview'] = $preview;
            $mappingData['previewData']['previewObjectId'] = $previewId;
        }
        return $this->asJson($mappingData);
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
        if (self::$requestMapper) {
            return self::$requestMapper;
        }
        $mapperType = \Craft::$app->request->getRequiredParam('mapper');
        $sourceTypeId = \Craft::$app->request->getParam('sourceTypeId');

        if (class_exists($mapperType) && in_array(PropertyMapperInterface::class, class_implements($mapperType), true)) {
            return self::$requestMapper = HubSpotToolbox::$plugin->properties->getMapper($mapperType, $sourceTypeId);
        }

        throw new HttpException('Invalid mapper');
    }

    protected function getPreviewSourceFromRequest() {
        /** @var PreviewablePropertyMapperInterface $mapper */
        $mapper = $this->getMapperFromRequest();
        $previewId = \Craft::$app->request->getParam('previewObjectId') ?? $mapper->getInitialPreviewObjectId();
        return $previewId;
    }

    public function actionDeleteMapping() {
        $this->requirePostRequest();
        $mappingId = \Craft::$app->request->getRequiredBodyParam('id');
        $mapping = HubSpotToolbox::$plugin->properties->getMappingById($mappingId);
        HubSpotToolbox::$plugin->properties->deleteMapping($mapping);
        return $this->asJson(['success' => true]);
    }
}