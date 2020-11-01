<?php

namespace venveo\hubspottoolbox\controllers\console;


use craft\commerce\elements\Order;
use craft\commerce\elements\Product;
use craft\commerce\elements\Variant;
use craft\console\Controller;
use venveo\hubspottoolbox\entities\ecommerce\ExternalSyncMessage;
use venveo\hubspottoolbox\entities\ecommerce\SyncMessagesWithMetaData;
use venveo\hubspottoolbox\enums\HubSpotObjectType;
use venveo\hubspottoolbox\HubSpotToolbox;
use venveo\hubspottoolbox\propertymappers\EcommerceContact;
use venveo\hubspottoolbox\propertymappers\EcommerceDeal;
use venveo\hubspottoolbox\propertymappers\EcommerceProduct;

class SyncController extends Controller
{
    public function actionProducts()
    {
        $pipeline = HubSpotToolbox::$plugin->properties->createPropertyMapperPipeline(EcommerceProduct::class);
        $settings = HubSpotToolbox::$plugin->features->getFeatureByHandle('ecommerce');
        /** @var Product $product */
        foreach (Variant::find()->batch(200) as $batchNumber => $batch) {
            $syncMessageWrapper = new SyncMessagesWithMetaData([
                'storeId' => $settings->storeId,
                'objectType' => HubSpotObjectType::Product
            ]);

            foreach ($batch as $variant) {
                $message = $pipeline->produceExternalSyncMessage($variant);
                print('Adding to batch ' . $batchNumber . ' - ' . $variant->title . PHP_EOL);
                $syncMessageWrapper->addMessage($message);
            }
            if (HubSpotToolbox::$plugin->ecomm->sendSyncMessages($syncMessageWrapper)) {
                print ('Sent batch ' . $batchNumber . PHP_EOL);
            } else {
                print ('Failed to send ' . $batchNumber . PHP_EOL);
                return 1;
            }
        }
        return 0;
    }

    public function actionCustomers()
    {
        $pipeline = HubSpotToolbox::$plugin->properties->createPropertyMapperPipeline(EcommerceContact::class);
        $settings = HubSpotToolbox::$plugin->features->getFeatureByHandle('ecommerce');

        $orders = Order::find()->email(':notempty:')->batch(200);

        /** @var Product $product */
        foreach ($orders as $batchNumber => $batch) {
            $syncMessageWrapper = new SyncMessagesWithMetaData([
                'storeId' => $settings->storeId,
                'objectType' => HubSpotObjectType::Contact
            ]);

            /** @var Order $order */
            foreach ($batch as $order) {
                $customer = $order->getCustomer();
                $message = $pipeline->produceExternalSyncMessage($customer->id);
                $this->stdout('Adding to batch ' . $batchNumber . ' - ' . $message->externalObjectId . PHP_EOL);
                $syncMessageWrapper->addMessage($message);
            }
            if (HubSpotToolbox::$plugin->ecomm->sendSyncMessages($syncMessageWrapper)) {
                print ('Sent batch ' . $batchNumber . PHP_EOL);
            } else {
                print ('Failed to send ' . $batchNumber . PHP_EOL);
                return 1;
            }
        }
        return 0;
    }

    public function actionOrders()
    {
        $pipeline = HubSpotToolbox::$plugin->properties->createPropertyMapperPipeline(EcommerceDeal::class);
        $contactMapper = HubSpotToolbox::$plugin->properties->getMapper(EcommerceContact::class);
        $settings = HubSpotToolbox::$plugin->features->getFeatureByHandle('ecommerce');

        $orders = Order::find()->batch(200);

        /** @var Product $product */
        foreach ($orders as $batchNumber => $batch) {
            $syncMessageWrapper = new SyncMessagesWithMetaData([
                'storeId' => $settings->storeId,
                'objectType' => HubSpotObjectType::Deal
            ]);

            /** @var Order $order */
            foreach ($batch as $order) {
                $message = $pipeline->produceExternalSyncMessage($order->id);
                $email = $order->getEmail();
                if ($email) {
                    $message->addAssociation(ExternalSyncMessage::ASSOCIATION_CONTACT,
                        $contactMapper->getExternalObjectId($order->customerId));
                }
                $this->stdout('Adding to batch ' . $batchNumber . ' - ' . $message->externalObjectId . PHP_EOL);
                $syncMessageWrapper->addMessage($message);
            }
            if (HubSpotToolbox::$plugin->ecomm->sendSyncMessages($syncMessageWrapper)) {
                print ('Sent batch ' . $batchNumber . PHP_EOL);
            } else {
                print ('Failed to send ' . $batchNumber . PHP_EOL);
                return 1;
            }
        }
        return 0;
    }
}