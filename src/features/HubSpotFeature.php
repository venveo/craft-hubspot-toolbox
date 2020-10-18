<?php

namespace venveo\hubspottoolbox\features;

use craft\base\SavableComponent;
use craft\helpers\FileHelper;
use craft\helpers\StringHelper;
use craft\helpers\Template;

abstract class HubSpotFeature extends SavableComponent implements HubSpotFeatureInterface
{

    public function getRequiredScopes(): array
    {
        return [];
    }

    /**
     * Shamelessly taken from Andrew Welch
     * Source: https://nystudio107.com/blog/writing-craft-plugins-with-extensible-components
     * @return array
     */
    public static function getTemplatesRoot(): array
    {
        $reflect = new \ReflectionClass(static::class);
        $classPath = FileHelper::normalizePath(
                dirname($reflect->getFileName())
                . '/../templates'
            )
            . DIRECTORY_SEPARATOR;
        $id = StringHelper::toKebabCase($reflect->getShortName());

        return [$id, $classPath];
    }

    public function renderSettingsHtml(): \Twig\Markup
    {
        return Template::raw($this->getSettingsHtml());
    }
}