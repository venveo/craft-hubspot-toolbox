<?php

namespace venveo\hubspottoolbox\payloads;

use venveo\hubspottoolbox\entities\HubSpotSyncMessage;
use venveo\hubspottoolbox\requests\HubSpotPayload;
use venveo\hubspottoolbox\validators\EmbeddedModelValidator;

const TYPE_CONTACT = 'CONTACT';
const TYPE_DEAL = 'DEAL';
const TYPE_LINE_ITEM = 'LINE_ITEM';
const TYPE_PRODUCT = 'PRODUCT';

class SyncMessages extends HubSpotPayload
{
    public string $objectType;
    public string $storeId;
    public array $messages;

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


    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = [['objectType', 'storeId', 'messages'], 'required'];
        $rules[] = ['messages', 'each', EmbeddedModelValidator::class];
        $rules[] = ['objectType', 'in', 'range' => [TYPE_CONTACT, TYPE_DEAL, TYPE_LINE_ITEM, TYPE_PRODUCT]];
        return $rules;
    }
}