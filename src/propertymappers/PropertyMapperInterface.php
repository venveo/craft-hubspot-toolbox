<?php
/*
 *  @link      https://www.venveo.com
 *  @copyright Copyright (c) 2020 Venveo
 */

namespace venveo\hubspottoolbox\propertymappers;

use venveo\hubspottoolbox\entities\ObjectProperty;
use venveo\hubspottoolbox\models\HubSpotObjectMapping;
use yii\base\Arrayable;

/**
 * Interface PropertyMapperInterface
 * @package venveo\hubspottoolbox\propertymappers
 *
 * @property string $type
 * @property-read array $templateParams
 */
interface PropertyMapperInterface extends \ArrayAccess, Arrayable, \IteratorAggregate
{
    /**
     * Returns the class type of the property mapper. Usually just `static`
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Sets the type property of the property mapper
     *
     * @param string $type
     */
    public function setType(string $type);

    /**
     * Sets the source object ID that is being mapped by this mapper
     *
     * @param $id
     */
    public function setSourceId($id);

    /**
     * Gets the source object ID that is being mapped by this mapper
     *
     * @return mixed
     */
    public function getSourceId();

    /**
     * What is the object name we're going to query from HubSpot for this mapper
     *
     * @return string
     */
    public static function getHubSpotObjectName(): string;

    /**
     * What is the context for this mapper?
     *
     * @return string
     */
    public static function getObjectContext(): string;

    /**
     * Gets all of the configured mappings for this mapper
     *
     * @return HubSpotObjectMapping[]
     */
    public function getPropertyMappings(): array;

    /**
     * Sets all of the configured mappings for this mapper
     *
     * @param HubSpotObjectMapping[] $mappings
     */
    public function setPropertyMappings(array $mappings);

    /**
     * Get all applicable object properties on this mapper
     *
     * @return ObjectProperty[]
     */
    public function getProperties(): array;

    /**
     * Sets all applicable object properties on this mapper
     *
     * @param ObjectProperty[] $properties
     */
    public function setProperties(array $properties);

    /**
     * Provides an array of params to be passed into object templates
     *
     * @return array
     */
    public function getTemplateParams(): array;

    /**
     * Sets the `renderedValue` on the supplied mapping
     *
     * @param HubSpotObjectMapping $mapping
     */
    public function renderProperty(HubSpotObjectMapping $mapping);

    /**
     * Renders all property mappings via the renderProperty() method
     */
    public function renderTemplates();
}