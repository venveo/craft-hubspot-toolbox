<?php

namespace venveo\hubspottoolbox\propertymappers;

use craft\commerce\elements\Variant;
use venveo\hubspottoolbox\traits\PreviewableMapper;

class EcommerceProduct extends PropertyMapper implements PreviewablePropertyMapperInterface
{
    use PreviewableMapper;

    public static function getHubSpotObjectName(): string
    {
        return 'products';
    }

    public static function getObjectContext(): string
    {
        return 'ecomm.variant';
    }


    public function getTemplateParams(): array
    {
        $variant = Variant::findOne($this->getSourceId());
        return [
            'variant' => $variant
        ];
    }

    public function getInitialPreviewObjectId()
    {
        return Variant::find()->orderBy('RAND()')->limit(1)->ids()[0] ?? null;
    }
}