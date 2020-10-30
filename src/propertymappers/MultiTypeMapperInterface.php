<?php
/*
 *  @link      https://www.venveo.com
 *  @copyright Copyright (c) 2020 Venveo
 */

namespace venveo\hubspottoolbox\propertymappers;

use venveo\hubspottoolbox\entities\ObjectProperty;
use venveo\hubspottoolbox\models\HubSpotObjectMapping;
use yii\base\Arrayable;

interface MultiTypeMapperInterface
{
    /**
     * Gets all source types indexed by their id
     *
     * @return MapperSourceType[]
     */
    public static function getSourceTypes(): array;

    /**
     * Gets a human readable name for the subtype
     *
     * @return string
     */
    public static function getMultiTypeName(): string;
}