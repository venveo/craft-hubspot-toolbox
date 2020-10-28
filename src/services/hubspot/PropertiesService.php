<?php
/**
 * HubSpot Toolbox plugin for Craft CMS 3.x
 *
 * Turnkey HubSpot integration for CraftCMS
 *
 * @link      https://venveo.com
 * @copyright Copyright (c) 2018 Venveo
 */

namespace venveo\hubspottoolbox\services\hubspot;

use Craft;
use craft\base\Component;
use craft\helpers\ArrayHelper;
use craft\helpers\DateTimeHelper;
use venveo\hubspottoolbox\entities\ObjectProperty;
use venveo\hubspottoolbox\models\HubSpotObjectMapping;
use venveo\hubspottoolbox\records\HubSpotObjectMapping as HubSpotObjectMappingRecord;
use venveo\hubspottoolbox\traits\HubSpotTokenAuthorization;

class PropertiesService extends Component
{
    use HubSpotTokenAuthorization;

    public function getObjectProperties($objectType)
    {
        $data = $this->getHubSpot()->objectProperties($objectType)->all()->getData();
        $properties = array_map(function ($item) {
            return new ObjectProperty($item);
        }, $data);
        return $properties;
    }

    public function getObjectMappings($objectType, $context)
    {
        $mappings = $this->_createMappingQuery($objectType, $context)->all();
        return array_map(function (HubSpotObjectMappingRecord $record) {
            return $this->_createMapping($record);
        }, $mappings);
    }

    public function getMappingsByName($objectType, $context)
    {
        $mappings = $this->getObjectMappings($objectType, $context);
        return ArrayHelper::index($mappings, 'property');
    }

    /**
     * @param $objectType
     * @return array
     */
    public function getMappingData($objectType, $context)
    {
        $properties = $this->getObjectProperties($objectType);
        $mappingsByName = $this->getMappingsByName($objectType, $context);
        $data = [];
        foreach ($properties as $property) {
            $mapping = $mappingsByName[$property->name] ?? new HubSpotObjectMapping([
                    'type' => $objectType,
                    'property' => $property->name,
                    'context' => $context
                ]);
            $mapping->setObjectProperty($property);
            $data[] = $mapping->toArray([], $mapping->extraFields());
        }
        return $data;
    }

    public function saveMapping(HubSpotObjectMapping $mapping)
    {
        if ($mapping->id) {
            $record = $this->_createMappingQuery($mapping->type)->where(['=', 'id', $mapping->id])->one();
        } else {
            $record = new HubSpotObjectMappingRecord();
            $record->type = $mapping->type;
        }
        $record->context = $mapping->context;
        $record->property = $mapping->property;
        $record->template = $mapping->template;
        $record->datePublished = $mapping->datePublished;
        $record->save();
        $mapping->id = $record->id;
        $mapping->dateCreated = $record->dateCreated;
        $mapping->dateUpdated = $record->dateUpdated;
        $mapping->uid = $record->uid;
        return true;
    }

    public function publishMappings($objectType, $context) {
        $unpublishedMappings = $this->_createMappingQuery($objectType, $context)->andWhere(['datePublished' => null])->all();
        $unpublishedMappingProperties = ArrayHelper::getColumn($unpublishedMappings, 'property');
        $publishedMappings = $this->_createMappingQuery($objectType, $context)->andWhere(['IN', 'property', $unpublishedMappingProperties])->andWhere(['NOT', ['datePublished' => null]])->all();

        $transaction = Craft::$app->getDb()->beginTransaction();
        try {
            foreach ($publishedMappings as $mapping) {
                $mapping->delete();
            }
            /** @var HubSpotObjectMappingRecord $unpublishedMapping */
            foreach($unpublishedMappings as $unpublishedMapping) {
                $unpublishedMapping->datePublished = DateTimeHelper::currentUTCDateTime();
                $unpublishedMapping->save();
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    protected function _createMapping(HubSpotObjectMappingRecord $record, $objectProperty = null): HubSpotObjectMapping
    {
        $model = new HubSpotObjectMapping($record);
        if ($objectProperty) {
            $model->setObjectProperty($objectProperty);
        }
        return $model;
    }

    /**
     * @param $objectType
     * @return \craft\db\ActiveQuery
     */
    protected function _createMappingQuery($objectType, $context = null): \craft\db\ActiveQuery
    {
        return HubSpotObjectMappingRecord::find()->where(['=', 'type', $objectType])->andWhere(['context' => $context]);
    }
}
