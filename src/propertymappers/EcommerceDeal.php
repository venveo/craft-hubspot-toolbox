<?php

namespace venveo\hubspottoolbox\propertymappers;

use craft\commerce\elements\Order;
use craft\commerce\Plugin;
use venveo\hubspottoolbox\enums\HubSpotObjectType;
use venveo\hubspottoolbox\models\HubSpotObjectMapping;
use venveo\hubspottoolbox\traits\PreviewableMapperTrait;

class EcommerceDeal extends MultiTypePropertyMapper implements PreviewablePropertyMapperInterface
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
        return Order::find()->orderBy('RAND()')->one()->id;
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

    public function getRecommendedMappings(): array
    {
        $mappings = [];
        $mappings[] = new HubSpotObjectMapping(['property' => 'dealname', 'template' => '{order.reference}']);
        $mappings[] = new HubSpotObjectMapping(['property' => 'amount', 'template' => '{order.total}']);
        return $mappings;
    }
}