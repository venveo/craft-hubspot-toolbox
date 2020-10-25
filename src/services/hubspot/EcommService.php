<?php
/**
 * HubSpot Toolbox plugin for Craft CMS 3.x
 *
 * Turnkey HubSpot integration for CraftCMS
 *
 * @link      https://venveo.com
 * @copyright Copyright (c) 2018 Venveo
 */

namespace venveo\hubspottoolbox\services\hubspot;

use craft\base\Component;
use SevenShores\Hubspot\Exceptions\BadRequest;
use SevenShores\Hubspot\Exceptions\HubspotException;
use venveo\hubspottoolbox\entities\ecommerce\Store;
use venveo\hubspottoolbox\entities\ecommerce\SyncMessagesWithMetaData;
use venveo\hubspottoolbox\traits\HubSpotTokenAuthorization;

class EcommService extends Component
{
    use HubSpotTokenAuthorization;

    public function getStores()
    {
        $results = $this->getHubSpot()->ecommerceBridge()->allStores()->getData()->results;
        return array_map(function ($item) {
            return new Store($item);
        }, $results);
    }

    public function saveStore(Store $store): bool
    {
        try {
            $updatedStore = $this->getHubSpot()->ecommerceBridge()->createOrUpdateStore($store->toArray());
        } catch (BadRequest $e) {
            $store->addError('storeId', $e->getMessage());
            return false;
        }
        $data = $updatedStore->getData();
        \Craft::configure($store, $data);
        return true;
    }

    public function sendSyncMessages(SyncMessagesWithMetaData $syncMessages): bool
    {
        if (!$syncMessages->validate()) {
            return false;
        }
        try {
            $this->getHubSpot()->ecommerceBridge()->sendSyncMessages($syncMessages->storeId, $syncMessages->objectType,
                $syncMessages->getMessagesPayload());
        } catch (HubspotException $exception) {
            throw $exception;
//            \Craft::dd($exception->getResponse()->getBody()->getContents());
        }
        return true;
    }
}
