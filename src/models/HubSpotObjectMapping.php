<?php

namespace venveo\hubspottoolbox\models;

use craft\base\Element;
use craft\base\ElementInterface;
use craft\helpers\Template;
use venveo\hubspottoolbox\records\HubSpotFormRecord;
use yii\base\Model;

class HubSpotObjectMapping extends Model
{
    public $id;
    public $type;
    public $property;
    public $template;
    public $dateCreated;
    public $dateUpdated;
    public $uid;

    public $properyObject;
}
