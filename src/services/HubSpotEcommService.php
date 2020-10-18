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
use craft\helpers\UrlHelper;
use SevenShores\Hubspot\Exceptions\BadRequest;
use SevenShores\Hubspot\Exceptions\HubspotException;
use venveo\hubspottoolbox\HubSpotToolbox;
use venveo\hubspottoolbox\entities\HubSpotStore;
use venveo\hubspottoolbox\payloads\SyncMessages;

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
//        $this->saveMappingSettings();
        $results = $this->hs->ecommerceBridge()->allStores()->getData()->results;
        return array_map(function ($item) {
            return new HubSpotStore($item);
        }, $results);
    }

    public function saveStore(HubSpotStore $store)
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

    public function getMappingSettings() {
        $hs = HubSpotToolbox::$plugin->hubspot->getHubspot(true);

        try {
            $results = $hs->ecommerceBridge()->getSettings();
        } catch (BadRequest $e) {
            if ($e->getCode() === 404) {
                return [];
            } else {
                throw $e;
            }
        }
        return $results->getData();
    }

    public function saveMappingSettings() {
        $hs = HubSpotToolbox::$plugin->hubspot->getHubspot(true);
        try {
            $response = $hs->ecommerceBridge()->upsertSettings([
                'enabled' => true,
                'webhookUri' => UrlHelper::actionUrl('hubspot-toolbox/ecommerce-import/webhook-import'),
                'mappings' => [
                    'CONTACT' => [
                        'properties' => [
                            [
                                'externalPropertyName' => 'firstname',
                                'hubspotPropertyName' => 'firstname',
                                'dataType' => 'STRING',
                            ],
                            [
                                'externalPropertyName' => 'email',
                                'hubspotPropertyName' => 'email',
                                'dataType' => 'STRING',
                            ]
                        ],
                    ],
                    'PRODUCT' => [
                        'properties' => [
                            [
                                'externalPropertyName' => 'firstname',
                                'hubspotPropertyName' => 'firstname',
                                'dataType' => 'STRING',
                            ]
                        ]
                    ],
                    'DEAL' => [
                        'properties' => [
                            [
                                'externalPropertyName' => 'hubspotdealstage',
                                'hubspotPropertyName' => 'dealstage',
                                'dataType' => 'STRING',
                            ]
                        ]
                    ],
                    'LINE_ITEM' => [
                        'properties' => [
                            [
                                'externalPropertyName' => 'total',
                                'hubspotPropertyName' => 'price',
                                'dataType' => 'NUMBER',
                            ]
                        ]
                    ]
                ]
            ]);
        } catch (HubspotException $e) {
            \Craft::dd($e->getResponse()->getBody()->getContents());
        }
    }

    public function sendSyncMessages(SyncMessages $syncMessages) {
        if (!$syncMessages->validate()) {
            return false;
        }
        $this->hs->ecommerceBridge()->sendSyncMessages($syncMessages->storeId, $syncMessages->objectType, $syncMessages->messages);
        return true;
    }
}
