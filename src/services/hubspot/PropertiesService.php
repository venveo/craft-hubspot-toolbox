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
            return new HubSpotObjectMapping($record);
        }, $mappings);
    }

    public function getMappingsByName($objectType) {
        $mappings = $this->getObjectMappings($objectType);
        $index = ArrayHelper::index($mappings, 'property');
        return $index;
    }

    public function getMappingData($objectType) {
        $properties = $this->getObjectProperties($objectType);
        $mappingsByName = $this->getMappingsByName($objectType);
        $data = [];
        foreach($properties as $property) {
            if (isset($mappingsByName[$property->name])) {
                $mapping = $mappingsByName[$property->name];
            } else {
                $mapping = new HubSpotObjectMapping([
                    'type' => $objectType,
                    'property' => $property->name,
                ]);
            }
            $mapping->properyObject = $property;
            $data[] = $mapping;
        }
        return $data;
    }

    /**
     * @param $objectType
     * @return \craft\db\ActiveQuery
     */
    protected function _createMappingQuery($objectType) {
        return HubSpotObjectMappingRecord::find()->where(['=', 'type', $objectType]);
    }
}
