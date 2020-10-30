<?php

namespace venveo\hubspottoolbox\controllers\console;


use craft\commerce\elements\Product;
use craft\commerce\elements\Variant;
use craft\console\Controller;
use venveo\hubspottoolbox\entities\ecommerce\ExternalSyncMessage;
use venveo\hubspottoolbox\entities\ecommerce\SyncMessagesWithMetaData;
use venveo\hubspottoolbox\enums\HubSpotObjectType;
use venveo\hubspottoolbox\HubSpotToolbox;
use venveo\hubspottoolbox\propertymappers\EcommerceProduct;

class SyncController extends Controller
{
    public function actionProducts()
    {
        $pipeline = HubSpotToolbox::$plugin->properties->createPropertyMapperPipeline(EcommerceProduct::class);
        $settings = HubSpotToolbox::$plugin->features->getFeatureByHandle('ecommerce');
        $syncMessageWrapper = new SyncMessagesWithMetaData([
            'storeId' => $settings->storeId,
            'objectType' => HubSpotObjectType::Product
        ]);

        /** @var Product $product */
        foreach (Variant::find()->limit(2)->each() as $variant) {
            $data = $pipeline->processInput($variant);
            $message = new ExternalSyncMessage([
                'externalObjectId' => $variant->id,
                'properties' => $data
            ]);
            $syncMessageWrapper->addMessage($message);

//            $response = $hs->ecommerceBridge()->sy->create($data)->getData();
//            $objectId = $response->objectId;
//            var_dump($response);
        }
        HubSpotToolbox::$plugin->ecomm->sendSyncMessages($syncMessageWrapper);
    }
}