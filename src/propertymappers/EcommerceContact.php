<?php

namespace venveo\hubspottoolbox\propertymappers;

use craft\commerce\elements\Order;
use venveo\hubspottoolbox\traits\PreviewableMapperTrait;

class EcommerceContact extends PropertyMapper implements PreviewablePropertyMapperInterface
{
    use PreviewableMapperTrait;

    public static function getHubSpotObjectName(): string
    {
        return 'contacts';
    }

    public static function getObjectContext(): string
    {
        return 'ecomm.order';
    }

    public function getTemplateParams(): array
    {
        $order = Order::findOne($this->getSourceId());
        return [
            'order' => $order
        ];
    }

    public function producePreviewObjectId()
    {
        return Order::find()->orderBy('RAND()')->limit(1)->ids()[0] ?? null;
    }
}