<?php
/*
 *  @link      https://www.venveo.com
 *  @copyright Copyright (c) 2020 Venveo
 */

namespace venveo\hubspottoolbox\propertymappers;

use craft\commerce\elements\Order;
use craft\commerce\Plugin;
use venveo\hubspottoolbox\enums\HubSpotObjectType;
use venveo\hubspottoolbox\traits\PreviewableMapperTrait;

class EcommerceLineItem extends PropertyMapper implements PreviewablePropertyMapperInterface
{
    use PreviewableMapperTrait;

    public static function getHubSpotObjectName(): string
    {
        return 'line_items';
    }

    public static function getHubSpotObjectType(): string
    {
        return HubSpotObjectType::LineItem;
    }

    public static function getObjectContext(): string
    {
        return 'ecomm.order.lineitem';
    }

    public function getTemplateParams(): array
    {
        $lineitem = Plugin::getInstance()->getLineItems()->getLineItemById($this->getSourceId());
        return [
            'lineitem' => $lineitem
        ];
    }

    public function producePreviewObjectId()
    {
        $order = Order::find()->orderBy('RAND()')->hasLineItems()->one();
        return $order->lineItems[0]->id;
    }
}