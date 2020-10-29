<?php
namespace venveo\hubspottoolbox\propertymappers;

use craft\base\Component;
use venveo\hubspottoolbox\entities\ObjectProperty;
use venveo\hubspottoolbox\models\HubSpotObjectMapping;

abstract class PropertyMapper extends Component implements PropertyMapperInterface {
    /**
     * @var HubSpotObjectMapping[]
     */
    protected $propertyMappings = [];
    /**
     * @var ObjectProperty[]
     */
    protected $properties = [];
    protected $_type = null;
    protected $sourceId;

    public $id;
    public $sourceTypeId;

    public $uid;
    public $dateCreated;
    public $dateUpdated;

    public function getType() {
        return $this->_type ?? static::class;
    }

    public function setType($type) {
        $this->_type = $type;
    }

    public function getPropertyMappings(): array
    {
        return $this->propertyMappings;
    }

    /**
     * @param array $propertyMappings
     */
    public function setPropertyMappings(array $propertyMappings): void
    {
        $this->propertyMappings = $propertyMappings;
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param array $properties
     */
    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    public function setSourceId($id) {
        $this->sourceId = $id;
    }

    public function getSourceId() {
        return $this->sourceId;
    }

    public function fields()
    {
        $fields = parent::fields();
        $fields[] = 'type';
        return $fields;
    }

    public function extraFields()
    {
        $extra = parent::extraFields();
        $extra[] = 'type';
        $extra[] = 'properties';
        $extra[] = 'propertyMappings';
        $extra[] = 'sourceId';
        return $extra;
    }

    public function renderProperty(HubSpotObjectMapping $mapping) {
        $renderedTemplate = \Craft::$app->view->renderObjectTemplate($mapping->template, [],
            $this->getTemplateParams());
        $mapping->setRenderedValue($renderedTemplate);
    }

    public function renderTemplates()
    {
        foreach($this->propertyMappings as $property) {
            $this->renderProperty($property);
        }
    }

}