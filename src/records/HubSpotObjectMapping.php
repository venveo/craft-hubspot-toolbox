<?php

namespace venveo\hubspottoolbox\records;

use craft\db\ActiveRecord;

/**
 * @package venveo\hubspottoolbox\records
 * @property int $id [int]
 * @property string $type [varchar(255)]
 * @property string $property [varchar(255)]
 * @property string $template
 * @property \DateTime $datePublished
 */
class HubSpotObjectMapping extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%hubspot_object_mappings}}';
    }
}