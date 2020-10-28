<?php

namespace venveo\hubspottoolbox\models;

use venveo\hubspottoolbox\entities\ObjectProperty;
use yii\base\Model;

class HubSpotObjectMapping extends Model
{
    public $id;
    public string $type;
    public string $context;
    public string $property;
    public $template;
    public $uid;

    public $datePublished;
    public $dateCreated;
    public $dateUpdated;

    private $preview;
    private $objectProperty;

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
     * @return string
     */
    public function getPreview()
    {
        return $this->preview;
    }

    /**
     * @param string $preview
     */
    public function setPreview(string $preview): void
    {
        $this->preview = $preview;
    }

    public function extraFields()
    {
        return [
            'preview',
            'objectProperty'
        ];
    }
}
