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
        return $fields;
    }

    /**
     * @inheritdoc
     */
    public function extraFields()
    {
        $extra = parent::extraFields();
        $extra[] = 'sourceTypes';
        return $extra;
    }
}