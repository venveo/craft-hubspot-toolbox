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
use SevenShores\Hubspot\Exceptions\HubspotException;
use venveo\hubspottoolbox\entities\ecommerce\ExternalPropertyMapping;
use venveo\hubspottoolbox\entities\ecommerce\ExternalSyncSettings;
use venveo\hubspottoolbox\entities\ecommerce\Settings as EcommerceBridgeSettings;
use venveo\hubspottoolbox\enums\HubSpotObjectType;
use venveo\hubspottoolbox\traits\HubSpotDevAuthorization;

class EcommSettingsService extends Component
{
    use HubSpotDevAuthorization;

    public function getMappingSettings()
    {
        try {
            $results = $this->getHubSpotDev()->ecommerceBridge()->getSettings([
                'appId' => $this->getAppId()
            ]);
        } catch (HubspotException $e) {
            \Craft::dd($e->getResponse()->getBody()->getContents());
        }
        return $results->getData();
    }

    protected function getContactProperties(): ExternalSyncSettings
    {
        $props = [];
        $props[] = new ExternalPropertyMapping('email', 'email',
            ExternalPropertyMapping::DATA_TYPE_STRING);
        $props[] = new ExternalPropertyMapping('firstname', 'firstname',
            ExternalPropertyMapping::DATA_TYPE_STRING);
        $props[] = new ExternalPropertyMapping('lastname', 'lastname',
            ExternalPropertyMapping::DATA_TYPE_STRING);
        $props[] = new ExternalPropertyMapping('billing_company', 'billing_company',
            ExternalPropertyMapping::DATA_TYPE_STRING);
        $props[] = new ExternalPropertyMapping('billing_phone', 'billing_phone',
            ExternalPropertyMapping::DATA_TYPE_STRING);
        $props[] = new ExternalPropertyMapping('billing_mobile', 'billing_mobile',
            ExternalPropertyMapping::DATA_TYPE_STRING);
        $props[] = new ExternalPropertyMapping('billing_address_1', 'billing_address_1',
            ExternalPropertyMapping::DATA_TYPE_STRING);
        $props[] = new ExternalPropertyMapping('billing_city', 'billing_city',
            ExternalPropertyMapping::DATA_TYPE_STRING);
        $props[] = new ExternalPropertyMapping('billing_state', 'billing_state',
            ExternalPropertyMapping::DATA_TYPE_STRING);
        $props[] = new ExternalPropertyMapping('billing_country', 'billing_country',
            ExternalPropertyMapping::DATA_TYPE_STRING);
        $props[] = new ExternalPropertyMapping('billing_postcode', 'billing_postcode',
            ExternalPropertyMapping::DATA_TYPE_STRING);

        return new ExternalSyncSettings([
            'properties' => $props
        ]);
    }

    protected function getProductProperties(): ExternalSyncSettings
    {
        $props = [];
        $props[] = new ExternalPropertyMapping('product_name', 'name',
            ExternalPropertyMapping::DATA_TYPE_STRING);
        $props[] = new ExternalPropertyMapping('sku', 'hs_sku',
            ExternalPropertyMapping::DATA_TYPE_STRING);
        $props[] = new ExternalPropertyMapping('product_image_url', 'ip__ecomm_bridge__image_url',
            ExternalPropertyMapping::DATA_TYPE_STRING);
        $props[] = new ExternalPropertyMapping('product_price', 'price',
            ExternalPropertyMapping::DATA_TYPE_NUMBER);
        $props[] = new ExternalPropertyMapping('product_description', 'description',
            ExternalPropertyMapping::DATA_TYPE_STRING);

        return new ExternalSyncSettings([
            'properties' => $props
        ]);
    }

    protected function getDealProperties(): ExternalSyncSettings
    {
        $props = [];
        $props[] = new ExternalPropertyMapping('dealstage', 'dealstage',
            ExternalPropertyMapping::DATA_TYPE_STRING);
        $props[] = new ExternalPropertyMapping('dealname', 'dealname',
            ExternalPropertyMapping::DATA_TYPE_STRING);
        $props[] = new ExternalPropertyMapping('closedate', 'closedate',
            ExternalPropertyMapping::DATA_TYPE_STRING);
        $props[] = new ExternalPropertyMapping('order_date', 'createdate',
            ExternalPropertyMapping::DATA_TYPE_STRING);
        $props[] = new ExternalPropertyMapping('order_amount', 'amount',
            ExternalPropertyMapping::DATA_TYPE_NUMBER);
        $props[] = new ExternalPropertyMapping('order_abandoned_cart_url', 'ip__ecomm_bridge__abandoned_cart_url',
            ExternalPropertyMapping::DATA_TYPE_STRING);
        $props[] = new ExternalPropertyMapping('order_discount_amount', 'ip__ecomm_bridge__discount_amount',
            ExternalPropertyMapping::DATA_TYPE_NUMBER);
        $props[] = new ExternalPropertyMapping('order_id', 'ip__ecomm_bridge__order_number',
            ExternalPropertyMapping::DATA_TYPE_STRING);
        $props[] = new ExternalPropertyMapping('order_shipment_ids', 'ip__ecomm_bridge__shipment_ids',
            ExternalPropertyMapping::DATA_TYPE_STRING);
        $props[] = new ExternalPropertyMapping('order_tax_amount', 'ip__ecomm_bridge__tax_amount',
            ExternalPropertyMapping::DATA_TYPE_NUMBER);
        $props[] = new ExternalPropertyMapping('customer_note', 'description',
            ExternalPropertyMapping::DATA_TYPE_STRING);

        return new ExternalSyncSettings([
            'properties' => $props
        ]);
    }

    protected function getLineItemProperties(): ExternalSyncSettings
    {
        $props = [];
        $props[] = new ExternalPropertyMapping('discount_amount', 'discount',
            ExternalPropertyMapping::DATA_TYPE_NUMBER);
        $props[] = new ExternalPropertyMapping('quantity', 'quantity',
            ExternalPropertyMapping::DATA_TYPE_NUMBER);
        $props[] = new ExternalPropertyMapping('name', 'name',
            ExternalPropertyMapping::DATA_TYPE_STRING);
        $props[] = new ExternalPropertyMapping('price', 'price',
            ExternalPropertyMapping::DATA_TYPE_NUMBER);
        $props[] = new ExternalPropertyMapping('description', 'description',
            ExternalPropertyMapping::DATA_TYPE_STRING);
        $props[] = new ExternalPropertyMapping('amount', 'amount',
            ExternalPropertyMapping::DATA_TYPE_NUMBER);
        $props[] = new ExternalPropertyMapping('tax_amount', 'tax_amount',
            ExternalPropertyMapping::DATA_TYPE_NUMBER);

        return new ExternalSyncSettings([
            'properties' => $props
        ]);
    }

    public function saveMappingSettings()
    {
//        UrlHelper::actionUrl('hubspot-toolbox/ecommerce-import/webhook-import')
        $settings = new EcommerceBridgeSettings();
        $settings->webhookUri = 'https://a8df854fa34c.ngrok.io/hubspot-toolbox/ecommerce-import/webhook-import';
        $settings->enabled = true;
        $settings->setObjectSettings(HubSpotObjectType::Contact, $this->getContactProperties());
        $settings->setObjectSettings(HubSpotObjectType::Product, $this->getProductProperties());
        $settings->setObjectSettings(HubSpotObjectType::Product, $this->getDealProperties());
        $settings->setObjectSettings(HubSpotObjectType::LineItem, $this->getLineItemProperties());
        try {
            $response = $this->getHubSpotDev()->ecommerceBridge()->upsertSettings($settings->prepareForApi(),
                ['appId' => $this->getAppId()]);
        } catch (HubspotException $e) {
            \Craft::dd($e->getResponse()->getBody()->getContents());
        }
    }
}
