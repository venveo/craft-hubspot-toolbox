<?php
namespace venveo\hubspottoolbox\propertymappers;

use craft\base\Component;
use craft\base\Element;
use craft\helpers\ArrayHelper;

class PropertyMapperPipeline extends Component {
    /**
     * @var PropertyMapper[]
     */
    public $propertyMappers = [];

    public function processInput($input) {
        $properties = [];
        foreach($this->propertyMappers as $propertyMapper) {
            $propertyMapper->setSourceId($input->id);
            foreach($propertyMapper->getPropertyMappings() as $mapping) {
                if (!isset($properties[$mapping->property])) {
                    $propertyMapper->renderProperty($mapping);
                    $properties[$mapping->property] = $mapping->getRenderedValue();
                }
            }
        }
//        $result = [];
//        foreach($properties as $property => $value) {
//            $item = [];
//            $item['name'] = $property;
//            $item['value'] = $value;
//            $result[] = $item;
//        }
//        return $result;
        return $properties;
    }

    /**
     * @return array
     */
    public function getPropertyMappers(): array
    {
        return $this->propertyMappers;
    }

    /**
     * @param array $propertyMappers
     */
    public function setPropertyMappers(array $propertyMappers): void
    {
        // Make sure we process more specific property mappers first
        $this->propertyMappers = $propertyMappers;
        ArrayHelper::multisort($this->propertyMappers, function(PropertyMapper $mapper) {
            return isset($mapper->sourceTypeId) ? 0 : 1;
        });
    }
}