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

use craft\base\Component;
use craft\helpers\ArrayHelper;
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

    public function getObjectMappings($objectType)
    {
        $mappings = $this->_createMappingQuery($objectType)->all();
        return array_map(function (HubSpotObjectMappingRecord $record) {
            return $this->_createMapping($record);
        }, $mappings);
    }

    public function getMappingsByName($objectType)
    {
        $mappings = $this->getObjectMappings($objectType);
        return ArrayHelper::index($mappings, 'property');
    }

    /**
     * @param $objectType
     * @return array
     */
    public function getMappingData($objectType)
    {
        $properties = $this->getObjectProperties($objectType);
        $mappingsByName = $this->getMappingsByName($objectType);
        $data = [];
        foreach ($properties as $property) {
            $mapping = $mappingsByName[$property->name] ?? new HubSpotObjectMapping([
                    'type' => $objectType,
                    'property' => $property->name,
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
    protected function _createMappingQuery($objectType): \craft\db\ActiveQuery
    {
        return HubSpotObjectMappingRecord::find()->where(['=', 'type', $objectType]);
    }
}
