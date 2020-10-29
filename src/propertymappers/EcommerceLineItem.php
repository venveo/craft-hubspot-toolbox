<?php

namespace venveo\hubspottoolbox\propertymappers;

use craft\commerce\elements\Order;
use craft\commerce\Plugin;
use venveo\hubspottoolbox\traits\PreviewableMapper;

class EcommerceLineItem extends PropertyMapper implements PreviewablePropertyMapperInterface
{

    public static function getHubSpotObjectName(): string
    {
        return 'line_items';
    }

    public static function getObjectContext(): string
    {
        return 'ecomm.order.lineitem';
    }

    public function getTemplateParams(): array
    {
        $lineitem = Plugin::getInstance()->lineItems->getLineItemById($this->getSourceId());
        return [
            'lineitem' => $lineitem
        ];
    }

    public function getInitialPreviewObjectId()
    {
        $order = Order::find()->orderBy('RAND()')->limit(1)->hasLineItems(true)->one();
        return $order->lineItems[0];
    }
}