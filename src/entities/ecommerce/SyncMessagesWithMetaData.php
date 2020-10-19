<?php
/*
 *  @link      https://www.venveo.com
 *  @copyright Copyright (c) 2020 Venveo
 */

namespace venveo\hubspottoolbox\entities\ecommerce;

use venveo\hubspottoolbox\entities\HubSpotEntity;
use venveo\hubspottoolbox\validators\EmbeddedModelValidator;

/**
 * Class HubSpotSyncMessage
 * @package venveo\hubspottoolbox\entities
 */
class SyncMessagesWithMetaData extends HubSpotEntity
{
    public const TYPE_CONTACT = 'CONTACT';
    public const TYPE_DEAL = 'DEAL';
    public const TYPE_LINE_ITEM = 'LINE_ITEM';
    public const TYPE_PRODUCT = 'PRODUCT';

    public string $storeId;
    public string $objectType;
    /** @var <ExternalSyncMessage>[] $messages */
    public array $messages = [];


    /**
     * Adds a message to the sync messages request
     * @param ExternalSyncMessage $message
     * @param false $validate
     * @return bool
     */
    public function addMessage(ExternalSyncMessage $message, $validate = false): bool
    {
        if ($validate && !$message->validate()) {
            return false;
        }
        $this->messages[] = $message;
        return true;
    }

    public function getMessagesPayload() {
        return array_map(function(ExternalSyncMessage $m) {
            return array_filter($m->toArray());
        }, $this->messages);
    }

    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = [['storeId', 'objectType'], 'required'];
        $rules[] = ['messages', 'each', 'rule' => [EmbeddedModelValidator::class]];
        $rules[] = ['objectType', 'in', 'range' => [self::TYPE_CONTACT, self::TYPE_DEAL, self::TYPE_LINE_ITEM, self::TYPE_PRODUCT]];
        return $rules;
    }
}