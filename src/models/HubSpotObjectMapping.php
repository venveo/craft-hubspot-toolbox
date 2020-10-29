<?php

namespace venveo\hubspottoolbox\models;

use venveo\hubspottoolbox\entities\ObjectProperty;
use yii\base\Model;

class HubSpotObjectMapping extends Model
{
    public $id;
    public int $mapperId;
    public string $property;
    public $template;
    public $uid;

    public $datePublished;
    public $dateCreated;
    public $dateUpdated;

    protected $objectProperty;

    protected $renderedValue;

    /**
     * @return ObjectProperty
     */
    public function getObjectProperty()
    {
        return $this->objectProperty;
    }

    /**
     * @param ObjectProperty $objectProperty
     */
    public function setObjectProperty(ObjectProperty $objectProperty): void
    {
        $this->objectProperty = $objectProperty;
    }

    /**
     * @return mixed
     */
    public function getRenderedValue()
    {
        return $this->renderedValue;
    }

    /**
     * @param mixed $renderedValue
     */
    public function setRenderedValue($renderedValue): void
    {
        $this->renderedValue = $renderedValue;
    }

    public function extraFields()
    {
        return [
            'objectProperty',
            'renderedValue'
        ];
    }
}
