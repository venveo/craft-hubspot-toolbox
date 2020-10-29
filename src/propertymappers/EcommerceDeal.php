<?php

namespace venveo\hubspottoolbox\propertymappers;

use craft\commerce\elements\Order;

class EcommerceDeal extends PropertyMapper implements PreviewablePropertyMapperInterface
{
    public static function getHubSpotObjectName(): string
    {
        return 'deals';
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

    public function getInitialPreviewObjectId()
    {
        return Order::find()->orderBy('RAND()')->limit(1)->ids()[0] ?? null;
    }
}