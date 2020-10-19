<?php
/*
 *  @link      https://www.venveo.com
 *  @copyright Copyright (c) 2020 Venveo
 */

namespace venveo\hubspottoolbox\listeners;

use craft\commerce\base\Purchasable;
use craft\events\ModelEvent;
use venveo\hubspottoolbox\entities\ecommerce\ExternalSyncMessage;
use venveo\hubspottoolbox\entities\ecommerce\SyncMessagesWithMetaData;
use venveo\hubspottoolbox\features\EcommerceFeature;
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
            'changedAt' => (int)(microtime(true) * 1000),
            'externalObjectId' => $purchasable->id,
            'properties' => [
                'product_name' => $purchasable->title,
                'product_price' => $purchasable->price,
                'product_description' => $purchasable->description
            ]
        ]);
        $payload = new SyncMessagesWithMetaData();
        $payload->objectType = SyncMessagesWithMetaData::TYPE_PRODUCT;
        $payload->storeId = $feature->storeId;
        $payload->addMessage($syncMessage);

        HubSpotToolbox::$plugin->ecomm->sendSyncMessages($payload);
    }
}