<?php
/*
 *  @link      https://www.venveo.com
 *  @copyright Copyright (c) 2020 Venveo
 */

namespace venveo\hubspottoolbox\listeners;

use craft\commerce\base\Purchasable;
use craft\commerce\elements\Order;
use craft\events\ModelEvent;
use venveo\hubspottoolbox\entities\ecommerce\ExternalSyncMessage;
use venveo\hubspottoolbox\entities\ecommerce\SyncMessagesWithMetaData;
use venveo\hubspottoolbox\enums\HubSpotObjectType;
use venveo\hubspottoolbox\features\EcommerceFeature;
use venveo\hubspottoolbox\helpers\HubSpotTimeHelper;
use venveo\hubspottoolbox\HubSpotToolbox;

class EcommerceListener
{
    public static function handlePurchasableSaved(ModelEvent $e) {
        /** @var EcommerceFeature $feature */
        $feature = HubSpotToolbox::$plugin->features->getFeatureByHandle('ecommerce');

        /** @var Purchasable $purchasable */
        $purchasable = $e->sender;
        $syncMessage = new ExternalSyncMessage([
            'action' => ExternalSyncMessage::ACTION_UPSERT,
            'changedAt' => HubSpotTimeHelper::getChangedAtTimeStamp(),
            'externalObjectId' => $purchasable->id,
            'properties' => [
                'product_name' => $purchasable->title,
                'product_price' => $purchasable->price,
                'product_description' => $purchasable->description
            ]
        ]);
        $payload = new SyncMessagesWithMetaData();
        $payload->objectType = HubSpotObjectType::Product;
        $payload->storeId = $feature->storeId;
        $payload->addMessage($syncMessage);

        HubSpotToolbox::$plugin->ecomm->sendSyncMessages($payload);
    }

    public static function handlePurchasableDeleted(ModelEvent $e) {

    }

    public static function handleOrderSaved(ModelEvent $e) {
        if ($e->isNew) {
            return null;
        }
        /** @var EcommerceFeature $feature */
        $feature = HubSpotToolbox::$plugin->features->getFeatureByHandle('ecommerce');

        /** @var Order $order */
        $order = $e->sender;
        $syncMessage = new ExternalSyncMessage([
            'action' => ExternalSyncMessage::ACTION_UPSERT,
            'changedAt' => HubSpotTimeHelper::getChangedAtTimeStamp(),
            'externalObjectId' => $order->email,
            'properties' => [
                'firstname' => $order->shippingAddress->firstName,
                'lastname' => $order->shippingAddress->lastName,
                'email' => $order->email,
            ]
        ]);
        $payload = new SyncMessagesWithMetaData();
        $payload->objectType = HubSpotObjectType::Contact;
        $payload->storeId = $feature->storeId;
        $payload->addMessage($syncMessage);

        $deal = new ExternalSyncMessage([
            'action' => ExternalSyncMessage::ACTION_UPSERT,
            'changedAt' => HubSpotTimeHelper::getChangedAtTimeStamp(),
            'externalObjectId' => $order->id,
            'properties' => [
                'order_id' => $order->number,
                'order_amount' => $order->total,
                'dealstage' => 'checkout_pending',
                'dealname' => $order->number
            ],
            'associations' => [
                'CONTACT' => [$order->email]
            ]
        ]);
        $payload2 = new SyncMessagesWithMetaData();
        $payload2->objectType = HubSpotObjectType::Contact;
        $payload2->storeId = $feature->storeId;
        $payload2->addMessage($deal);

        HubSpotToolbox::$plugin->ecomm->sendSyncMessages($payload);
        HubSpotToolbox::$plugin->ecomm->sendSyncMessages($payload2);
    }
}