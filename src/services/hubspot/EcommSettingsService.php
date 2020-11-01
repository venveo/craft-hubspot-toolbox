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
use venveo\hubspottoolbox\HubSpotToolbox;
use venveo\hubspottoolbox\propertymappers\EcommerceContact;
use venveo\hubspottoolbox\propertymappers\EcommerceDeal;
use venveo\hubspottoolbox\propertymappers\EcommerceLineItem;
use venveo\hubspottoolbox\propertymappers\EcommerceProduct;
use venveo\hubspottoolbox\traits\HubSpotApiKeyAuthorization;

class EcommSettingsService extends Component
{
    use HubSpotApiKeyAuthorization;

    public function getMappingSettings()
    {
        try {
            $results = $this->getHubSpotFromKey()->ecommerceBridge()->getSettings();
        } catch (HubspotException $e) {
            \Craft::dd($e->getResponse()->getBody()->getContents());
        }
        return $results->getData();
    }

    protected function getContactProperties(): ExternalSyncSettings
    {
        $props = [];
        $propertyNames = HubSpotToolbox::$plugin->properties->getAllUniqueMappedPropertyNames(EcommerceContact::class);
        $properties = HubSpotToolbox::$plugin->properties->getObjectProperties(EcommerceContact::getHubSpotObjectType(),
            $propertyNames);
        foreach ($properties as $property) {
            $props[] = new ExternalPropertyMapping($property->name, $property->name,
                ExternalPropertyMapping::DATA_TYPE_STRING);
        }

        /*
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
        */
        return new ExternalSyncSettings([
            'properties' => $props
        ]);
    }

    protected function getProductProperties(): ExternalSyncSettings
    {
//        $props = [];
//        $props[] = new ExternalPropertyMapping('name', 'name',
//            ExternalPropertyMapping::DATA_TYPE_STRING);
//        $props[] = new ExternalPropertyMapping('hs_sku', 'hs_sku',
//            ExternalPropertyMapping::DATA_TYPE_STRING);
//        $props[] = new ExternalPropertyMapping('ip__ecomm_bridge__image_url', 'ip__ecomm_bridge__image_url',
//            ExternalPropertyMapping::DATA_TYPE_STRING);
//        $props[] = new ExternalPropertyMapping('price', 'price',
//            ExternalPropertyMapping::DATA_TYPE_NUMBER);
//        $props[] = new ExternalPropertyMapping('description', 'description',
//            ExternalPropertyMapping::DATA_TYPE_STRING);
        $propertyNames = HubSpotToolbox::$plugin->properties->getAllUniqueMappedPropertyNames(EcommerceProduct::class);
        $properties = HubSpotToolbox::$plugin->properties->getObjectProperties(EcommerceProduct::getHubSpotObjectType(),
            $propertyNames);
        foreach ($properties as $property) {
            $props[] = new ExternalPropertyMapping($property->name, $property->name,
                ExternalPropertyMapping::DATA_TYPE_STRING);
        }
        return new ExternalSyncSettings([
            'properties' => $props
        ]);
    }

    protected function getDealProperties(): ExternalSyncSettings
    {
        $propertyNames = HubSpotToolbox::$plugin->properties->getAllUniqueMappedPropertyNames(EcommerceDeal::class);
        $properties = HubSpotToolbox::$plugin->properties->getObjectProperties(EcommerceDeal::getHubSpotObjectType(),
            $propertyNames);
        foreach ($properties as $property) {
            $props[] = new ExternalPropertyMapping($property->name, $property->name,
                ExternalPropertyMapping::DATA_TYPE_STRING);
        }
        $props[] = new ExternalPropertyMapping('dealstage', 'dealstage',
            ExternalPropertyMapping::DATA_TYPE_STRING);
//        $props[] = new ExternalPropertyMapping('dealname', 'dealname',
//            ExternalPropertyMapping::DATA_TYPE_STRING);
//        $props[] = new ExternalPropertyMapping('closedate', 'closedate',
//            ExternalPropertyMapping::DATA_TYPE_STRING);
//        $props[] = new ExternalPropertyMapping('createdate', 'createdate',
//            ExternalPropertyMapping::DATA_TYPE_STRING);
//        $props[] = new ExternalPropertyMapping('amount', 'amount',
//            ExternalPropertyMapping::DATA_TYPE_NUMBER);
//        $props[] = new ExternalPropertyMapping('ip__ecomm_bridge__abandoned_cart_url', 'ip__ecomm_bridge__abandoned_cart_url',
//            ExternalPropertyMapping::DATA_TYPE_STRING);
//        $props[] = new ExternalPropertyMapping('ip__ecomm_bridge__discount_amount', 'ip__ecomm_bridge__discount_amount',
//            ExternalPropertyMapping::DATA_TYPE_NUMBER);
//        $props[] = new ExternalPropertyMapping('ip__ecomm_bridge__order_number', 'ip__ecomm_bridge__order_number',
//            ExternalPropertyMapping::DATA_TYPE_STRING);
//        $props[] = new ExternalPropertyMapping('ip__ecomm_bridge__shipment_ids', 'ip__ecomm_bridge__shipment_ids',
//            ExternalPropertyMapping::DATA_TYPE_STRING);
//        $props[] = new ExternalPropertyMapping('ip__ecomm_bridge__tax_amount', 'ip__ecomm_bridge__tax_amount',
//            ExternalPropertyMapping::DATA_TYPE_NUMBER);
//        $props[] = new ExternalPropertyMapping('description', 'description',
//            ExternalPropertyMapping::DATA_TYPE_STRING);

        return new ExternalSyncSettings([
            'properties' => $props
        ]);
    }

    protected function getLineItemProperties(): ExternalSyncSettings
    {
        $propertyNames = HubSpotToolbox::$plugin->properties->getAllUniqueMappedPropertyNames(EcommerceLineItem::class);
        $properties = HubSpotToolbox::$plugin->properties->getObjectProperties(EcommerceLineItem::getHubSpotObjectType(),
            $propertyNames);
        foreach ($properties as $property) {
            $props[] = new ExternalPropertyMapping($property->name, $property->name,
                ExternalPropertyMapping::DATA_TYPE_STRING);
        }
        $props = [];
//        $props[] = new ExternalPropertyMapping('discount_amount', 'discount',
//            ExternalPropertyMapping::DATA_TYPE_NUMBER);
//        $props[] = new ExternalPropertyMapping('quantity', 'quantity',
//            ExternalPropertyMapping::DATA_TYPE_NUMBER);
//        $props[] = new ExternalPropertyMapping('name', 'name',
//            ExternalPropertyMapping::DATA_TYPE_STRING);
//        $props[] = new ExternalPropertyMapping('price', 'price',
//            ExternalPropertyMapping::DATA_TYPE_NUMBER);
//        $props[] = new ExternalPropertyMapping('description', 'description',
//            ExternalPropertyMapping::DATA_TYPE_STRING);
//        $props[] = new ExternalPropertyMapping('amount', 'amount',
//            ExternalPropertyMapping::DATA_TYPE_NUMBER);
//        $props[] = new ExternalPropertyMapping('tax_amount', 'tax_amount',
//            ExternalPropertyMapping::DATA_TYPE_NUMBER);

        return new ExternalSyncSettings([
            'properties' => $props
        ]);
    }


    public function saveMappingSettings()
    {
//        UrlHelper::actionUrl('hubspot-toolbox/ecommerce-import/webhook-import')
        $settings = new EcommerceBridgeSettings();
        $settings->webhookUri = null;
        $settings->enabled = true;
        $settings->setObjectSettings(HubSpotObjectType::Contact, $this->getContactProperties());
        $settings->setObjectSettings(HubSpotObjectType::Product, $this->getProductProperties());
        $settings->setObjectSettings(HubSpotObjectType::Deal, $this->getDealProperties());
        $settings->setObjectSettings(HubSpotObjectType::LineItem, $this->getLineItemProperties());
        try {
            $response = $this->getHubSpotFromKey()->ecommerceBridge()->upsertSettings($settings->prepareForApi());
        } catch (HubspotException $e) {
            \Craft::dd($e->getResponse()->getBody()->getContents());
        }
    }

    public function deleteMappingSettings()
    {
        return $this->getHubSpotFromKey()->ecommerceBridge()->deleteSettings()->getData();
    }
}
