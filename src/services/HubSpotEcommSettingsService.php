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
use SevenShores\Hubspot\Exceptions\HubspotException;
use venveo\hubspottoolbox\entities\ecommerce\SyncMessagesWithMetaData;
use venveo\hubspottoolbox\HubSpotToolbox;

/**
 */
class HubSpotEcommSettingsService extends Component
{
    /** @var \SevenShores\Hubspot\Factory $hs */
    private $hs = null;
    private $appId = null;

    public function init()
    {
        parent::init();

        $this->hs = HubSpotToolbox::$plugin->hubspot->getHubspot(true);
        $this->appId = \Craft::parseEnv(HubSpotToolbox::$plugin->settings->appId);
    }

    public function getMappingSettings()
    {
        try {
            $results = $this->hs->ecommerceBridge()->getSettings([
                'appId' => $this->appId
            ]);
        } catch (HubspotException $e) {
            \Craft::dd($e->getResponse()->getBody()->getContents());
        }
        return $results->getData();
    }

    public function saveMappingSettings()
    {
        try {
            $response = $this->hs->ecommerceBridge()->upsertSettings([
                'enabled' => true,
//                'webhookUri' => UrlHelper::actionUrl('hubspot-toolbox/ecommerce-import/webhook-import'),
                'webhookUri' => null,
                'mappings' => [
                    'CONTACT' => [
                        'properties' => [
                            [
                                'externalPropertyName' => 'email',
                                'dataType' => 'STRING',
                                'hubspotPropertyName' => 'email',
                            ],
                            [
                                'externalPropertyName' => 'firstname',
                                'dataType' => 'STRING',
                                'hubspotPropertyName' => 'firstname',
                            ],
                            [
                                'externalPropertyName' => 'lastname',
                                'dataType' => 'STRING',
                                'hubspotPropertyName' => 'lastname',
                            ],
                            [
                                'externalPropertyName' => 'billing_company',
                                'dataType' => 'STRING',
                                'hubspotPropertyName' => 'company',
                            ],
                            [
                                'externalPropertyName' => 'billing_phone',
                                'dataType' => 'STRING',
                                'hubspotPropertyName' => 'phone',
                            ],
                            [
                                'externalPropertyName' => 'billing_mobile',
                                'dataType' => 'STRING',
                                'hubspotPropertyName' => 'mobilephone',
                            ],
                            [
                                'externalPropertyName' => 'billing_address_1',
                                'dataType' => 'STRING',
                                'hubspotPropertyName' => 'address',
                            ],
                            [
                                'externalPropertyName' => 'billing_city',
                                'dataType' => 'STRING',
                                'hubspotPropertyName' => 'city',
                            ],
                            [
                                'externalPropertyName' => 'billing_state',
                                'dataType' => 'STRING',
                                'hubspotPropertyName' => 'state',
                            ],
                            [
                                'externalPropertyName' => 'billing_country',
                                'dataType' => 'STRING',
                                'hubspotPropertyName' => 'country',
                            ],
                            [
                                'externalPropertyName' => 'billing_postcode',
                                'dataType' => 'STRING',
                                'hubspotPropertyName' => 'zip',
                            ],

                        ],
                    ],
                    'PRODUCT' => [
                        'properties' => [
                            [
                                'externalPropertyName' => 'product_name',
                                'dataType' => 'STRING',
                                'hubspotPropertyName' => 'name',
                            ],
                            [
                                'externalPropertyName' => 'product_image_url',
                                'dataType' => 'AVATAR_IMAGE',
                                'hubspotPropertyName' => 'ip__ecomm_bridge__image_url',
                            ],
                            [
                                'externalPropertyName' => 'product_price',
                                'dataType' => 'NUMBER',
                                'hubspotPropertyName' => 'price',
                            ],
                            [
                                'externalPropertyName' => 'product_description',
                                'dataType' => 'STRING',
                                'hubspotPropertyName' => 'description',
                            ],
                        ]
                    ],
                    'DEAL' => [
                        'properties' => [
                            [
                                'externalPropertyName' => 'dealstage',
                                'dataType' => 'STRING',
                                'hubspotPropertyName' => 'dealstage',
                            ],
                            [
                                'externalPropertyName' => 'dealname',
                                'dataType' => 'STRING',
                                'hubspotPropertyName' => 'dealname',
                            ],
                            [
                                'externalPropertyName' => 'closedate',
                                'dataType' => 'STRING',
                                'hubspotPropertyName' => 'closedate',
                            ],
                            [
                                'externalPropertyName' => 'order_date',
                                'dataType' => 'STRING',
                                'hubspotPropertyName' => 'createdate',
                            ],
                            [
                                'externalPropertyName' => 'order_amount',
                                'dataType' => 'NUMBER',
                                'hubspotPropertyName' => 'amount',
                            ],
                            [
                                'externalPropertyName' => 'order_abandoned_cart_url',
                                'dataType' => 'STRING',
                                'hubspotPropertyName' => 'ip__ecomm_bridge__abandoned_cart_url',
                            ],
                            [
                                'externalPropertyName' => 'order_discount_amount',
                                'dataType' => 'NUMBER',
                                'hubspotPropertyName' => 'ip__ecomm_bridge__discount_amount',
                            ],
                            [
                                'externalPropertyName' => 'order_id',
                                'dataType' => 'STRING',
                                'hubspotPropertyName' => 'ip__ecomm_bridge__order_number',
                            ],
                            [
                                'externalPropertyName' => 'order_shipment_ids',
                                'dataType' => 'STRING',
                                'hubspotPropertyName' => 'ip__ecomm_bridge__shipment_ids',
                            ],
                            [
                                'externalPropertyName' => 'order_tax_amount',
                                'dataType' => 'NUMBER',
                                'hubspotPropertyName' => 'ip__ecomm_bridge__tax_amount',
                            ],
                            [
                                'externalPropertyName' => 'customer_note',
                                'dataType' => 'NUMBER',
                                'hubspotPropertyName' => 'description',
                            ],
                        ]
                    ],
                    'LINE_ITEM' => [
                        'properties' => [
                            [
                                'externalPropertyName' => 'discount_amount',
                                'dataType' => 'NUMBER',
                                'hubspotPropertyName' => 'discount',
                            ],
                            [
                                'externalPropertyName' => 'quantity',
                                'dataType' => 'NUMBER',
                                'hubspotPropertyName' => 'quantity',
                            ],
                            [
                                'externalPropertyName' => 'name',
                                'dataType' => 'STRING',
                                'hubspotPropertyName' => 'name',
                            ],
                            [
                                'externalPropertyName' => 'price',
                                'dataType' => 'NUMBER',
                                'hubspotPropertyName' => 'price',
                            ],
                            [
                                'externalPropertyName' => 'sku',
                                'dataType' => 'STRING',
                                'hubspotPropertyName' => 'description',
                            ],
                            [
                                'externalPropertyName' => 'amount',
                                'dataType' => 'NUMBER',
                                'hubspotPropertyName' => 'amount',
                            ],
                            [
                                'externalPropertyName' => 'tax_amount',
                                'dataType' => 'NUMBER',
                                'hubspotPropertyName' => 'tax',
                            ],
                        ]
                    ]
                ]
            ], ['appId' => $this->appId]);
            \Craft::dd($response->getBody()->getContents());
        } catch (HubspotException $e) {
            \Craft::dd($e->getResponse()->getBody()->getContents());
        }
    }
}
