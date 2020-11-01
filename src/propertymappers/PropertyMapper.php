<?php
/*
 *  @link      https://www.venveo.com
 *  @copyright Copyright (c) 2020 Venveo
 */

namespace venveo\hubspottoolbox\propertymappers;

use Craft;
use craft\base\Component;
use venveo\hubspottoolbox\entities\ObjectProperty;
use venveo\hubspottoolbox\models\HubSpotObjectMapping;


abstract class PropertyMapper extends Component implements PropertyMapperInterface
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var mixed
     */
    public $sourceTypeId;
    /**
     * @var string
     */
    public $uid;
    /**
     * @var \DateTime
     */
    public $dateCreated;
    /**
     * @var \DateTime
     */
    public $dateUpdated;
    /**
     * @var HubSpotObjectMapping[]
     */
    protected $propertyMappings = [];
    /**
     * @var ObjectProperty[]
     */
    protected array $properties = [];
    protected $_type = null;

    /**
     * @inheritdoc
     */
    public function getType(): string
    {
        return $this->_type ?? static::class;
    }

    /**
     * @inheritdoc
     */
    public function setType(string $type)
    {
        $this->_type = $type;
    }

    /**
     * @inheritdoc
     */
    public function getPropertyMappings(): array
    {
        return $this->propertyMappings;
    }

    /**
     * @inheritdoc
     */
    public function setPropertyMappings(array $propertyMappings): void
    {
        $this->propertyMappings = $propertyMappings;
    }

    /**
     * @inheritdoc
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @inheritdoc
     */
    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    /**
     * @inheritdoc
     */
    public function renderTemplates($source): array
    {
        $results = [];
        foreach ($this->propertyMappings as $property) {
            $results[$property->property] = $this->renderProperty($property, $source);
        }
        return $results;
    }

    /**
     * @inheritdoc
     */
    public function renderProperty(HubSpotObjectMapping $mapping, $source): string
    {
        $renderedTemplate = Craft::$app->view->renderObjectTemplate($mapping->template, [],
            $this->getTemplateParams($source));
        return $renderedTemplate;
    }

    /**
     * @inheritdoc
     */
    public function getRecommendedMappings(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        $fields = parent::fields();
        $fields[] = 'type';
        return $fields;
    }

    /**
     * @inheritdoc
     */
    public function extraFields()
    {
        $extra = parent::extraFields();
        $extra[] = 'type';
        $extra[] = 'properties';
        $extra[] = 'propertyMappings';
        if ($this instanceof PreviewablePropertyMapperInterface) {
            $extra[] = 'initialPreviewObjectId';
        }
        return $extra;
    }

    /**
     * @inheritdoc
     */
    public function canBeAppliedToSource($source): bool
    {
        return true;
    }
}