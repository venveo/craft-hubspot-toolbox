<?php

namespace venveo\hubspottoolbox\propertymappers;

use craft\commerce\elements\Order;
use craft\commerce\Plugin;
use venveo\hubspottoolbox\enums\HubSpotObjectType;
use venveo\hubspottoolbox\traits\PreviewableMapperTrait;

class EcommerceDeal extends MultiTypePropertyMapper
{
    use PreviewableMapperTrait;

    public static function getHubSpotObjectName(): string
    {
        return 'deals';
    }

    public static function getHubSpotObjectType(): string
    {
        return HubSpotObjectType::Deal;
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

    public static function getSourceTypes(): array
    {
        $statuses = Plugin::getInstance()->orderStatuses->allOrderStatuses;
        $sourceTypes = array_map(function ($status) {
            return new MapperSourceType([
                'displayName' => $status->name,
                'id' => $status->id
            ]);
        }, $statuses);
        return $sourceTypes;
    }

    public static function getSourceTypeName(): string
    {
        return 'Order Status';
    }
}