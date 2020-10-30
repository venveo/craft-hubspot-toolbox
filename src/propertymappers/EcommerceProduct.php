<?php
/*
 *  @link      https://www.venveo.com
 *  @copyright Copyright (c) 2020 Venveo
 */

namespace venveo\hubspottoolbox\propertymappers;

use craft\commerce\elements\Variant;
use craft\commerce\models\ProductType;
use craft\commerce\Plugin;
use venveo\hubspottoolbox\traits\PreviewableMapperTrait;

class EcommerceProduct extends MultiTypePropertyMapper implements PreviewablePropertyMapperInterface
{

    use PreviewableMapperTrait;

    public static function getHubSpotObjectName(): string
    {
        return 'products';
    }

    public static function getObjectContext(): string
    {
        return 'ecomm.variant';
    }


    public function getTemplateParams(): array
    {
        $variant = Variant::findOne($this->getSourceId());
        return [
            'variant' => $variant
        ];
    }

    public function producePreviewObjectId()
    {
        $productType = $this->sourceTypeId;
        if ($productType) {
            $productType = Plugin::getInstance()->productTypes->getProductTypeById($productType);
        }
        return Variant::find()->orderBy('RAND()')->typeId($productType)->one()->id ?? null;
    }

    public static function getSourceTypes(): array
    {
        $productTypes = Plugin::getInstance()->productTypes->allProductTypes;
        return array_map(function (ProductType $productType) {
            $subtype = new MapperSourceType();
            $subtype->id = $productType->id;
            $subtype->displayName = $productType->name;
            return $subtype;
        }, $productTypes);
    }

    public static function getSourceTypeName(): string
    {
        return 'Product Type';
    }
}