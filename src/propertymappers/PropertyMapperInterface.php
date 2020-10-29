<?php
namespace venveo\hubspottoolbox\propertymappers;

use venveo\hubspottoolbox\models\HubSpotObjectMapping;
use yii\base\Arrayable;

interface PropertyMapperInterface extends \ArrayAccess, Arrayable, \IteratorAggregate {
    public static function getHubSpotObjectName(): string;
    public static function getObjectContext(): string;

    public function getPropertyMappings(): array;
    public function setPropertyMappings(array $mappings);

    public function getProperties(): array;
    public function setProperties(array $mappings);

    public function getTemplateParams(): array;

    public function renderProperty(HubSpotObjectMapping $mapping);
    public function renderTemplates();
}