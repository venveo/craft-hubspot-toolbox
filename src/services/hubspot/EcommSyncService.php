<?php
namespace venveo\hubspottoolbox\services\hubspot;

use craft\base\Component;
use craft\commerce\elements\db\OrderQuery;
use craft\commerce\elements\Order;
use craft\commerce\elements\Product;
use SevenShores\Hubspot\Exceptions\BadRequest;
use venveo\hubspottoolbox\entities\ecommerce\ExternalObjectStatus;
use venveo\hubspottoolbox\entities\ecommerce\ExternalSyncMessage;
use venveo\hubspottoolbox\entities\ecommerce\SyncMessagesWithMetaData;
use venveo\hubspottoolbox\enums\HubSpotObjectType;
use venveo\hubspottoolbox\HubSpotToolbox;
use venveo\hubspottoolbox\propertymappers\EcommerceContact;
use venveo\hubspottoolbox\propertymappers\EcommerceDeal;
use venveo\hubspottoolbox\traits\HubSpotApiKeyAuthorization;

class EcommSyncService extends Component
{
    use HubSpotApiKeyAuthorization;

    /**
     * @param $source
     * @param string $propertyMapper
     * @return ExternalObjectStatus
     * @throws BadRequest
     */
    public function checkObjectSyncStatus($source, string $propertyMapper): ExternalObjectStatus
    {
        $storeId = HubSpotToolbox::$plugin->features->getFeatureByHandle('ecommerce')->storeId;
        $syncStatus = new ExternalObjectStatus($this->getHubSpotFromKey()->ecommerceBridge()->checkSyncStatus($storeId, $propertyMapper::getHubSpotObjectType(), $propertyMapper::getExternalObjectId($source))->getData());
        return $syncStatus;
    }

    public function syncOrders(OrderQuery $orderQuery) {
        $pipeline = HubSpotToolbox::$plugin->propertyMappings->createPropertyMapperPipeline(EcommerceDeal::class);
        $settings = HubSpotToolbox::$plugin->features->getFeatureByHandle('ecommerce');

        $orders = $orderQuery->batch(200);

        /** @var Product $product */
        foreach ($orders as $batchNumber => $batch) {
            $syncMessageWrapper = new SyncMessagesWithMetaData([
                'storeId' => $settings->storeId,
                'objectType' => HubSpotObjectType::Deal
            ]);

            /** @var Order $order */
            foreach ($batch as $order) {
                $message = $pipeline->produceExternalSyncMessage($order->id);
                $customer = $order->getCustomer();
                if ($customer) {
                    $message->addAssociation(ExternalSyncMessage::ASSOCIATION_CONTACT,
                        EcommerceContact::getExternalObjectId($order));
                }
                $syncMessageWrapper->addMessage($message);
            }
            HubSpotToolbox::$plugin->ecomm->sendSyncMessages($syncMessageWrapper);
        }
    }

    public function syncLineItems() {

    }
}
