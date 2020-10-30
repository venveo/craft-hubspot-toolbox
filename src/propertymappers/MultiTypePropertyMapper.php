<?php
/*
 *  @link      https://www.venveo.com
 *  @copyright Copyright (c) 2020 Venveo
 */

namespace venveo\hubspottoolbox\propertymappers;


abstract class MultiTypePropertyMapper extends PropertyMapper implements MultiTypeMapperInterface
{
    /**
     * @inheritdoc
     */
    public function fields()
    {
        $fields = parent::fields();
        $fields[] = 'sourceTypes';
        $fields[] = 'sourceTypeName';
        return $fields;
    }
}