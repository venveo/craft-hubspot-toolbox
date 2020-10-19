<?php
/**
 * HubSpot Toolbox plugin for Craft CMS 3.x
 *
 * Turnkey HubSpot integration for CraftCMS
 *
 * @link      https://venveo.com
 * @copyright Copyright (c) 2018 Venveo
 */

namespace venveo\hubspottoolbox\services;

use craft\base\Component;
use SevenShores\Hubspot\Exceptions\BadRequest;
use SevenShores\Hubspot\Exceptions\HubspotException;
use venveo\hubspottoolbox\entities\ecommerce\Store;
use venveo\hubspottoolbox\entities\ecommerce\SyncMessagesWithMetaData;
use venveo\hubspottoolbox\HubSpotToolbox;

/**
 */
class HubSpotEcommService extends Component
{
    private $stores = null;

    /** @var \SevenShores\Hubspot\Factory $hs */
    private $hs = null;

    public function init()
    {
        $this->hs = HubSpotToolbox::$plugin->hubspot->getHubspot();
        parent::init();
    }

    public function getStores()
    {
//        \Craft::dd($this->getMappingSettings());
//        $this->saveMappingSettings();
        $results = $this->hs->ecommerceBridge()->allStores()->getData()->results;
        return array_map(function ($item) {
            return new Store($item);
        }, $results);
    }

    public function saveStore(Store $store)
    {
        try {
            $updatedStore = $this->hs->ecommerceBridge()->createOrUpdateStore($store->toArray());
        } catch (BadRequest $e) {
            $store->addError('storeId', $e->getMessage());
            return false;
        }
        $data = $updatedStore->getData();
        \Craft::configure($store, $data);
        return true;
    }

    public function sendSyncMessages(SyncMessagesWithMetaData $syncMessages)
    {
        if (!$syncMessages->validate()) {
            return false;
        }
        try {
            $this->hs->ecommerceBridge()->sendSyncMessages($syncMessages->storeId, $syncMessages->objectType,
                $syncMessages->getMessagesPayload());
        } catch (HubspotException $exception) {
            throw $exception;
//            \Craft::dd($exception->getResponse()->getBody()->getContents());
        }
        return true;
    }
}
