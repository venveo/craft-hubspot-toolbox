<?php

namespace venveo\hubspottoolbox\propertymappers;

use craft\commerce\elements\Order;
use craft\commerce\models\Customer;
use craft\commerce\Plugin;
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
        return 'ecomm.customer';
    }

    public function getTemplateParams($source): array
    {
        /** @var Customer $customer */
        $customer = Plugin::getInstance()->customers->getCustomerById($source);
        $order = Order::find()->customerId($customer->id)->email('NOT :empty:')->one();
        return [
            'customer' => $customer,
            'customerOrder' => $order
        ];
    }

    public function producePreviewObjectId()
    {
        /** @var Order $order */
        $order = Order::find()->orderBy('RAND()')->isCompleted(true)->one();
        if (!$order) {
            return null;
        }
        return $order->customer->id;
    }

    public function getRecommendedMappings(): array
    {
        $mappings = [];
        $mappings[] = new HubSpotObjectMapping(['property' => 'firstname', 'template' => '{customer.billingAddress.firstName}']);
        $mappings[] = new HubSpotObjectMapping(['property' => 'lastname', 'template' => '{customer.billingAddress.firstName}']);
        $mappings[] = new HubSpotObjectMapping(['property' => 'email', 'template' => '{customerOrder.email}']);
        $mappings[] = new HubSpotObjectMapping(['property' => 'phone', 'template' => '{customer.primaryShippingAddress.phone|default(customer.primaryBillingAddress.phone)}']);
        $mappings[] = new HubSpotObjectMapping(['property' => 'city', 'template' => '{customer.primaryShippingAddress.city}']);
        $mappings[] = new HubSpotObjectMapping(['property' => 'state', 'template' => '{customer.primaryShippingAddress.state}']);
        $mappings[] = new HubSpotObjectMapping(['property' => 'zip', 'template' => '{customer.primaryShippingAddress.zipCode}']);
        $mappings[] = new HubSpotObjectMapping(['property' => 'country', 'template' => '{customer.primaryShippingAddress.country}']);
        return $mappings;
    }

    public function getExternalObjectId($source)
    {
        $customer = Plugin::getInstance()->customers->getCustomerById($source);
        $order = Order::find()->customerId($customer->id)->email(':notempty:')->one();
        return $order->email ?? $customer->id;
    }
}