<?php

namespace venveo\hubspottoolbox\features;

use craft\base\SavableComponentInterface;

interface HubSpotFeatureInterface extends SavableComponentInterface
{
    public static function getName(): string;

    public static function getHandle(): string;

    public static function getTemplatesRoot(): array;

    public function getRequiredScopes(): array;
}