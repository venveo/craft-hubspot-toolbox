<?php

namespace venveo\hubspottoolbox\propertymappers;

use craft\commerce\elements\Order;
use venveo\hubspottoolbox\enums\HubSpotObjectType;
use venveo\hubspottoolbox\models\HubSpotObjectMapping;
use venveo\hubspottoolbox\traits\PreviewableMapperTrait;

class EcommerceContact extends PropertyMapper implements PreviewablePropertyMapperInterface
{
    use PreviewableMapperTrait;

    public static function getHubSpotObjectName(): string
    {
        return 'contacts';
    }

    public static function getHubSpotObjectType(): string
    {
        return HubSpotObjectType::Contact;
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
        return Order::find()->orderBy('RAND()')->isCompleted(true)->limit(1)->ids()[0] ?? null;
    }

    public function getRecommendedMappings(): array
    {
        $mappings = [];
        $mappings[] = new HubSpotObjectMapping(['property' => 'firstname', 'template' => '{order.billingAddress.firstName}']);
        $mappings[] = new HubSpotObjectMapping(['property' => 'lastname', 'template' => '{order.billingAddress.firstName}']);
        $mappings[] = new HubSpotObjectMapping(['property' => 'email', 'template' => '{order.email}']);
        $mappings[] = new HubSpotObjectMapping(['property' => 'phone', 'template' => '{order.shippingAddress.phone|default(order.billingAddress.phone)}']);
        $mappings[] = new HubSpotObjectMapping(['property' => 'city', 'template' => '{order.shippingAddress.city}']);
        $mappings[] = new HubSpotObjectMapping(['property' => 'state', 'template' => '{order.shippingAddress.state}']);
        $mappings[] = new HubSpotObjectMapping(['property' => 'zip', 'template' => '{order.shippingAddress.zipCode}']);
        $mappings[] = new HubSpotObjectMapping(['property' => 'country', 'template' => '{order.shippingAddress.country}']);
        return $mappings;
    }
}