<?php

namespace venveo\hubspottoolbox\payloads;

use venveo\hubspottoolbox\entities\HubSpotSyncMessage;
use venveo\hubspottoolbox\validators\EmbeddedModelValidator;

/**
 *
 * @property-read mixed $messagesPayload
 */
class SyncMessages extends HubSpotPayload
{
    public const TYPE_CONTACT = 'CONTACT';
    public const TYPE_DEAL = 'DEAL';
    public const TYPE_LINE_ITEM = 'LINE_ITEM';
    public const TYPE_PRODUCT = 'PRODUCT';

    public string $objectType;
    public string $storeId;
    public array $messages = [];

    /**
     * Adds a message to the sync messages request
     * @param HubSpotSyncMessage $message
     * @param false $validate
     * @return bool
     */
    public function addMessage(HubSpotSyncMessage $message, $validate = false): bool
    {
        if ($validate && !$message->validate()) {
            return false;
        }
        $this->messages[] = $message;
        return true;
    }

    public function getMessagesPayload() {
        return array_map(function(HubSpotSyncMessage $m) {
            return array_filter($m->toArray());
        }, $this->messages);
    }


    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = [['objectType', 'storeId', 'messages'], 'required'];
        $rules[] = ['messages', 'each', 'rule' => [EmbeddedModelValidator::class]];
        $rules[] = ['objectType', 'in', 'range' => [self::TYPE_CONTACT, self::TYPE_DEAL, self::TYPE_LINE_ITEM, self::TYPE_PRODUCT]];
        return $rules;
    }
}